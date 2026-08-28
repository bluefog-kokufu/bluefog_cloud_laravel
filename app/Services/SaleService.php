<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Sale;
use App\Repositories\SaleRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleService
{
    /** 支払方法・入金方法の選択肢 */
    public const METHODS = ['現金', '普通預金', '当座預金', 'クレジット'];

    /** ステータスの選択肢 */
    public const STATUSES = ['未請求', '請求済', '入金済', '回収不能'];

    /** 消費税率の選択肢 */
    public const RATES = [10, 8, 0];

    /** アップロード対象の印鑑画像種別(キー => 表示名) */
    public const SEALS = [
        'seal' => '印鑑',
        'staff_seal' => '担当者印鑑',
    ];

    /** 印鑑画像の保存先ディスク(非公開) */
    private const DISK = 'local';

    private const CSV_HEADER = ['取引No', '作成日', '取引先名', '入金方法', '品目・内容', '税抜金額', '税率(%)', 'ステータス', '備考'];

    public function __construct(private readonly SaleRepositoryInterface $sales) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->sales->paginate($filters);
    }

    public function findWithItems(string $id): ?Sale
    {
        return $this->sales->findWithItems($id);
    }

    /**
     * 取引を明細ごと作成する。金額・税額は明細から自動計算する。
     *
     * @param  array<string, UploadedFile>  $uploadedFiles
     */
    public function create(array $data, array $uploadedFiles = []): Sale
    {
        $items = $data['items'];
        unset($data['items'], $data['seal'], $data['staff_seal']);
        $totals = $this->totals($items);

        $data['amount'] = $totals['sub'];
        $data['tax'] = $totals['tax'];
        $data['files'] = [];

        $sale = DB::transaction(fn () => $this->sales->create($data, $items));

        $files = $this->storeFiles($sale, $uploadedFiles);
        if ($files) {
            $sale = $this->sales->update($sale, ['files' => $files], $sale->items->toArray());
        }

        return $sale;
    }

    /**
     * 取引を明細ごと更新する。金額・税額は明細から自動計算する。
     *
     * @param  array<string, UploadedFile>  $uploadedFiles
     */
    public function update(Sale $sale, array $data, array $uploadedFiles = []): Sale
    {
        $items = $data['items'];
        unset($data['items'], $data['seal'], $data['staff_seal']);
        $totals = $this->totals($items);

        $data['amount'] = $totals['sub'];
        $data['tax'] = $totals['tax'];
        $data['files'] = array_merge($sale->files ?? [], $this->storeFiles($sale, $uploadedFiles));

        return DB::transaction(fn () => $this->sales->update($sale, $data, $items));
    }

    public function delete(Sale $sale): void
    {
        Storage::disk(self::DISK)->deleteDirectory('sales/'.$sale->id);
        $this->sales->delete($sale);
    }

    /**
     * アップロードされた印鑑画像をディスクに保存する。既存の同種別ファイルは置き換える。
     *
     * @param  array<string, UploadedFile>  $uploadedFiles
     */
    private function storeFiles(Sale $sale, array $uploadedFiles): array
    {
        $stored = [];
        foreach ($uploadedFiles as $key => $file) {
            if (! $file instanceof UploadedFile || ! array_key_exists($key, self::SEALS)) {
                continue;
            }

            $existing = $sale->files[$key] ?? null;
            if ($existing && ! empty($existing['path'])) {
                Storage::disk(self::DISK)->delete($existing['path']);
            }

            $path = $file->storeAs('sales/'.$sale->id, $key.'_'.now()->format('YmdHis').'.'.$file->extension(), self::DISK);

            $stored[$key] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'ts' => now()->format('Y.m.d H:i'),
            ];
        }

        return $stored;
    }

    /**
     * アップロード済みの印鑑画像を、請求書モーダル内での表示に使うためインライン表示で返す(ダウンロードさせない)。
     */
    public function sealResponse(Sale $sale, string $key): StreamedResponse
    {
        $file = $sale->files[$key] ?? null;
        abort_unless($file && Storage::disk(self::DISK)->exists($file['path']), 404);

        return Storage::disk(self::DISK)->response($file['path'], $file['name']);
    }

    /**
     * 税率ごとの税抜金額・消費税額を集計する。
     * 適格請求書のルール上、端数処理は税率ごとに1回だけ行う必要があるため、
     * 明細行ごとに計算せず、同一税率の税抜金額を先に合算してから税額を算出する。
     */
    public function totals(array $items): array
    {
        $groups = [];
        foreach ($items as $item) {
            $rate = (int) ($item['rate'] ?? 0);
            $groups[$rate] ??= ['rate' => $rate, 'sub' => 0, 'tax' => 0];
            $groups[$rate]['sub'] += (int) ($item['amount'] ?? 0);
        }
        foreach ($groups as $rate => $group) {
            $groups[$rate]['tax'] = $this->calcTaxAt($group['sub'], $rate);
        }

        $sub = array_sum(array_column($groups, 'sub'));
        $tax = array_sum(array_column($groups, 'tax'));

        return [
            'sub' => $sub,
            'tax' => $tax,
            'total' => $sub + $tax,
            'groups' => $groups,
        ];
    }

    /**
     * 指定税率での消費税額を算出する（端数処理: 切り捨て）
     */
    public function calcTaxAt(int $amount, int $rate): int
    {
        return (int) floor($amount * $rate / 100);
    }

    /**
     * 請求書の表示・印刷に必要な情報一式を組み立てる。
     * 初回表示時は請求書発行日時（invoiced）を記録する。
     */
    public function invoiceData(Sale $sale): array
    {
        $sale->loadMissing(['items', 'customer']);

        if (! $sale->customer) {
            return ['error' => '取引先が未設定のため請求書を作成できません。'];
        }

        if (! $sale->invoiced) {
            $sale->invoiced = Carbon::now();
            $sale->save();
        }

        $items = $sale->items->isNotEmpty()
            ? $sale->items
            : collect([['name' => $sale->memo ?: '商品売上', 'amount' => $sale->amount, 'rate' => 10]]);

        $totals = $this->totals($items->map(fn ($item) => is_array($item) ? $item : $item->toArray())->all());
        $company = Company::query()->first();

        return [
            'sale' => $sale,
            'customer' => $sale->customer,
            'company' => $company,
            'items' => $items,
            'totals' => $totals,
            'dueDate' => Carbon::parse($sale->date)->addDays(30),
            'hasReduced' => $items->contains(fn ($item) => (int) (is_array($item) ? $item['rate'] : $item->rate) === 8),
            'rates' => collect(self::RATES)->filter(fn ($rate) => isset($totals['groups'][$rate])),
        ];
    }

    /**
     * ステータスを「請求済」にし、請求書発行日時を更新する。
     */
    public function issueInvoice(Sale $sale): void
    {
        $sale->status = '請求済';
        $sale->invoiced = Carbon::now();
        $sale->save();
    }

    public function exportRows(): array
    {
        $rows = [self::CSV_HEADER];
        foreach ($this->sales->allWithItems() as $sale) {
            $customerName = $sale->customer?->name ?? '(削除済み)';
            foreach ($sale->items as $item) {
                $rows[] = [
                    $sale->id,
                    optional($sale->date)->format('Y-m-d'),
                    $customerName,
                    $sale->method,
                    $item->name,
                    $item->amount,
                    $item->rate,
                    $sale->status,
                    $sale->memo,
                ];
            }
        }

        return $rows;
    }

    public function templateRows(): array
    {
        return [
            self::CSV_HEADER,
            ['T001', '2026-08-01', 'サンプル株式会社', '現金', '保守サポート費用(月額)', '135000', '10', '未請求', ''],
            ['T001', '2026-08-01', 'サンプル株式会社', '現金', '商品B', '50000', '8', '未請求', ''],
            ['T002', '2026-08-02', 'サンプル株式会社', '普通預金', '商品売上', '55000', '10', '未請求', ''],
        ];
    }

    /**
     * CSVをインポートする。「取引No」列が同じ行は1つの取引の明細としてまとめて登録する。
     * 取引先名が未登録の場合は顧客情報も自動作成する。
     */
    public function importCsv(string $realPath): int
    {
        $handle = fopen($realPath, 'r');
        if ($handle === false) {
            return 0;
        }

        $groups = [];
        $order = [];
        $first = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($first) {
                $first = false;
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $row[0]);
                if (($row[0] ?? null) === self::CSV_HEADER[0]) {
                    continue;
                }
            }

            $custName = trim((string) ($row[2] ?? ''));
            $itemName = trim((string) ($row[4] ?? ''));
            if ($custName === '' || $itemName === '') {
                continue;
            }

            $key = trim((string) ($row[0] ?? '')) ?: ('row'.count($order));
            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'date' => $row[1] ?: now()->format('Y-m-d'),
                    'cust_name' => $custName,
                    'method' => in_array($row[3] ?? '', self::METHODS, true) ? $row[3] : '現金',
                    'status' => in_array($row[7] ?? '', self::STATUSES, true) ? $row[7] : '未請求',
                    'memo' => $row[8] ?? '',
                    'items' => [],
                ];
                $order[] = $key;
            }

            $rate = in_array((int) ($row[6] ?? null), self::RATES, true) ? (int) $row[6] : 10;
            $groups[$key]['items'][] = [
                'name' => $itemName,
                'amount' => (int) ($row[5] ?? 0),
                'rate' => $rate,
            ];
        }
        fclose($handle);

        $added = 0;
        DB::transaction(function () use ($groups, $order, &$added) {
            foreach ($order as $key) {
                $group = $groups[$key];
                $totals = $this->totals($group['items']);

                $this->sales->create([
                    'id' => 'T'.Str::upper(Str::random(10)),
                    'date' => $group['date'],
                    'cust_id' => $this->resolveCustomerIdByName($group['cust_name']),
                    'method' => $group['method'],
                    'status' => $group['status'],
                    'memo' => $group['memo'],
                    'amount' => $totals['sub'],
                    'tax' => $totals['tax'],
                ], $group['items']);
                $added++;
            }
        });

        return $added;
    }

    /**
     * 会社名から顧客IDを取得する。該当する顧客が存在しない場合は自動で新規作成する。
     */
    private function resolveCustomerIdByName(string $name): string
    {
        $customer = Customer::where('name', $name)->first();
        if ($customer) {
            return $customer->id;
        }

        $customer = Customer::create([
            'name' => $name,
            'type' => '受注取引管理',
            'site' => '月末締め翌月末払い',
            'memo' => 'CSVインポートで自動登録',
        ]);

        return $customer->id;
    }
}
