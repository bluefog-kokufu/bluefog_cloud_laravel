<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Purchase;
use App\Repositories\PurchaseRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseService
{
    /** 入金方法の選択肢 */
    public const METHODS = ['現金', '普通預金', '当座預金', 'クレジット'];

    /** ステータスの選択肢 */
    public const STATUSES = ['未払い', '支払い済'];

    /** アップロード対象の書類種別(キー => 表示名) */
    public const DOCS = [
        'quote' => '見積書',
        'invoice' => '請求書',
        'receipt' => '領収書',
        'contract' => '契約書',
    ];

    private const CSV_HEADER = ['No', '取引年月日', '取引先名', '入金方法', '取引金額(税抜)', '税額', 'ステータス', 'メモ'];

    /** 書類ファイルの保存先ディスク(非公開) */
    private const DISK = 'local';

    public function __construct(private readonly PurchaseRepositoryInterface $purchases) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->purchases->paginate($filters);
    }

    public function find(string $id): ?Purchase
    {
        return $this->purchases->find($id);
    }

    public function calcTax(int $amount): int
    {
        return (int) floor($amount * 10 / 100);
    }

    /**
     * 取引書類を作成し、あわせてアップロードされた書類を保存する。
     */
    public function create(array $data, array $uploadedFiles): Purchase
    {
        $data['files'] = [];
        $data['up'] = now()->toDateString();
        $purchase = $this->purchases->create($data);

        $files = $this->storeFiles($purchase, $uploadedFiles);
        if ($files) {
            $this->purchases->update($purchase, ['files' => $files]);
        }

        return $purchase->refresh();
    }

    /**
     * 取引書類を更新し、アップロードされた書類があれば既存の書類に追加・上書きする。
     */
    public function update(Purchase $purchase, array $data, array $uploadedFiles): Purchase
    {
        $newFiles = $this->storeFiles($purchase, $uploadedFiles);
        $data['files'] = array_merge($purchase->files ?? [], $newFiles);
        $data['up'] = now()->toDateString();

        return $this->purchases->update($purchase, $data);
    }

    public function delete(Purchase $purchase): void
    {
        Storage::disk(self::DISK)->deleteDirectory('purchases/'.$purchase->id);
        $this->purchases->delete($purchase);
    }

    /**
     * アップロードされた書類をディスクに保存する。既存の同種別ファイルは置き換える。
     *
     * @param  array<string, UploadedFile>  $uploadedFiles
     */
    private function storeFiles(Purchase $purchase, array $uploadedFiles): array
    {
        $stored = [];
        foreach ($uploadedFiles as $key => $file) {
            if (! $file instanceof UploadedFile || ! array_key_exists($key, self::DOCS)) {
                continue;
            }

            $existing = $purchase->files[$key] ?? null;
            if ($existing && ! empty($existing['path'])) {
                Storage::disk(self::DISK)->delete($existing['path']);
            }

            $path = $file->storeAs('purchases/'.$purchase->id, $key.'_'.now()->format('YmdHis').'.'.$file->extension(), self::DISK);

            $stored[$key] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'ts' => now()->format('Y.m.d H:i'),
            ];
        }

        return $stored;
    }

    /**
     * アップロード済み書類のダウンロードレスポンスを返す。
     */
    public function fileResponse(Purchase $purchase, string $key): StreamedResponse
    {
        $file = $purchase->files[$key] ?? null;
        abort_unless($file && Storage::disk(self::DISK)->exists($file['path']), 404);

        return Storage::disk(self::DISK)->download($file['path'], $file['name']);
    }

    public function exportRows(): array
    {
        $rows = [self::CSV_HEADER];
        foreach ($this->purchases->all() as $purchase) {
            $rows[] = [
                $purchase->id,
                optional($purchase->date)->format('Y-m-d'),
                $purchase->customer?->name ?? '(削除済み)',
                $purchase->method,
                $purchase->amount,
                $purchase->tax,
                $purchase->status,
                $purchase->memo,
            ];
        }

        return $rows;
    }

    public function templateRows(): array
    {
        return [
            ['取引年月日', '取引先名', '入金方法', '取引金額(税抜)', '税額(空欄で自動計算)', 'ステータス', 'メモ'],
            ['2026-08-01', 'サンプル商事株式会社', '現金', '88000', '', '未払い', ''],
        ];
    }

    /**
     * CSVをインポートする。書類ファイルはCSVでは取り込めないため、登録後に編集からアップロードする必要がある。
     * エクスポート形式(No列あり)とテンプレート形式(No列なし)は、1列目が日付形式かどうかで自動判別する。
     * 取引先名が未登録の場合は顧客情報も自動作成する。
     */
    public function importCsv(string $realPath): int
    {
        $handle = fopen($realPath, 'r');
        if ($handle === false) {
            return 0;
        }

        $entries = [];
        $first = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($first) {
                $first = false;
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) ($row[0] ?? ''));
                if (($row[0] ?? null) === self::CSV_HEADER[0] || ($row[0] ?? null) === '取引年月日') {
                    continue;
                }
            }

            $offset = preg_match('/^\d{4}-\d{2}-\d{2}$/', trim((string) ($row[0] ?? ''))) ? 0 : 1;
            $custName = trim((string) ($row[$offset + 1] ?? ''));
            if ($custName === '') {
                continue;
            }

            $amount = (int) ($row[$offset + 3] ?? 0);
            $taxCell = trim((string) ($row[$offset + 4] ?? ''));

            $entries[] = [
                'date' => $row[$offset] ?: now()->format('Y-m-d'),
                'cust_name' => $custName,
                'method' => in_array($row[$offset + 2] ?? '', self::METHODS, true) ? $row[$offset + 2] : '現金',
                'amount' => $amount,
                'tax' => $taxCell !== '' && is_numeric($taxCell) ? (int) $taxCell : $this->calcTax($amount),
                'status' => in_array($row[$offset + 5] ?? '', self::STATUSES, true) ? $row[$offset + 5] : '未払い',
                'memo' => $row[$offset + 6] ?? '',
            ];
        }
        fclose($handle);

        $added = 0;
        DB::transaction(function () use ($entries, &$added) {
            foreach ($entries as $entry) {
                $this->purchases->create([
                    'date' => $entry['date'],
                    'cust_id' => $this->resolveCustomerIdByName($entry['cust_name']),
                    'method' => $entry['method'],
                    'amount' => $entry['amount'],
                    'tax' => $entry['tax'],
                    'status' => $entry['status'],
                    'memo' => $entry['memo'],
                    'files' => [],
                    'up' => now()->toDateString(),
                ]);
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

        return Customer::create([
            'name' => $name,
            'type' => '発注取引管理',
            'memo' => 'CSVインポートで自動登録',
        ])->id;
    }
}
