<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Support\Pagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    /** 都道府県の選択肢(47都道府県固定) */
    public const PREFS = [
        '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県', '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
        '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
        '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県',
    ];
    public function index(Request $request): View
    {
        $query = $request->query('q');

        $customers = Customer::query()
            ->when($query, function ($builder, $query) {
                $builder->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('person', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('tel', 'like', "%{$query}%");
                });
            })
            ->orderBy('name')
            ->paginate(Pagination::PER_PAGE)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'query'));
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    /**
     * 他画面(請求書作成等)からモーダルで素早く顧客を新規登録するためのフォームを返す
     */
    public function createModal(): string
    {
        return view('admin.customers.modal_create')->render();
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:受注取引管理,発注取引管理,両方で使用する'],
            'zip' => ['nullable', 'string', 'max:20'],
            'pref' => ['nullable', 'string', Rule::in(self::PREFS)],
            'addr1' => ['nullable', 'string', 'max:255'],
            'addr2' => ['nullable', 'string', 'max:255'],
            'tel' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-]+$/'],
            'mobile' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-]+$/'],
            'fax' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-]+$/'],
            'url' => ['nullable', 'url', 'max:255'],
            'person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'memo' => ['nullable', 'string'],
        ], $this->phoneMessages(), $this->attributeNames());

        // モーダルからの登録時は、この画面自体のリダイレクトによるJSON判定(shouldRenderJsonWhen)が
        // api/*配下しか対象にしないため、バリデーション失敗時は明示的にJSONで返す
        if ($validator->fails() && $request->wantsJson()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $validator->validate();

        $customer = Customer::create($data);

        // モーダルからの登録時は、他画面の入力内容を失わないようリダイレクトせずJSONで返す
        if ($request->wantsJson()) {
            return response()->json(['id' => $customer->id, 'name' => $customer->name]);
        }

        return redirect()->route('customer')->with('status', '顧客を作成しました。');
    }

    public function edit(Customer $customer): string
    {
        return view('admin.customers.modal_edit', compact('customer'))->render();
    }

    public function update(Request $request, Customer $customer): RedirectResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:受注取引管理,発注取引管理,両方で使用する'],
            'zip' => ['nullable', 'string', 'max:20'],
            'pref' => ['nullable', 'string', Rule::in(self::PREFS)],
            'addr1' => ['nullable', 'string', 'max:255'],
            'addr2' => ['nullable', 'string', 'max:255'],
            'tel' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-]+$/'],
            'mobile' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-]+$/'],
            'fax' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-]+$/'],
            'url' => ['nullable', 'url', 'max:255'],
            'person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'memo' => ['nullable', 'string'],
        ], $this->phoneMessages(), $this->attributeNames());

        // モーダルからの更新時は、モーダルを離脱せずエラーを表示できるよう明示的にJSONで返す
        if ($validator->fails() && $request->wantsJson()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $validator->validate();

        $customer->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'updated']);
        }

        return redirect()->route('customer')->with('status', '顧客を更新しました。');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(['status' => 'deleted']);
    }

    public function template(): StreamedResponse
    {
        $filename = '顧客一覧_テンプレート.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['会社名', '顧客タイプ', '郵便番号', '都道府県', '住所(市区町村・丁番地)', '住所2(建物名・部屋番号)', '電話番号', '携帯電話番号', 'ファックス番号', 'ウェブサイトURL', '担当者名', 'メールアドレス', 'メモ']);
            fputcsv($out, ['サンプル株式会社', '両方で使用する', '100-0001', '東京都', '千代田区1-1-1', '', '03-0000-0000', '090-0000-0000', '03-0000-0001', 'https://example.com', '山田 太郎', 'info@sample.co.jp', '']);
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export(): StreamedResponse
    {
        $filename = '顧客一覧.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            // header
            fputcsv($out, ['会社名', '顧客タイプ', '郵便番号', '都道府県', '住所(市区町村・丁番地)', '住所2(建物名・部屋番号)', '電話番号', '携帯電話番号', 'ファックス番号', 'ウェブサイトURL', '担当者名', 'メールアドレス', 'メモ']);
            // rows
            foreach (Customer::orderBy('name')->get() as $c) {
                fputcsv($out, [
                    $c->name,
                    $c->type ?? '',
                    $c->zip ?? '',
                    $c->pref ?? '',
                    $c->addr1 ?? '',
                    $c->addr2 ?? '',
                    $c->tel ?? '',
                    $c->mobile ?? '',
                    $c->fax ?? '',
                    $c->url ?? '',
                    $c->person ?? '',
                    $c->email ?? '',
                    $c->memo ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ], [], ['csv_file' => 'CSVファイル']);

        $file = $request->file('csv_file');
        $added = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) === false) {
            return redirect()->route('customer')->with('error', 'CSVファイルを読み込めませんでした。');
        }

        $first = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($first) {
                $first = false;
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
                if (! isset($row[0]) || $row[0] !== '会社名') {
                    fclose($handle);

                    return redirect()->route('customer')->with('error', 'CSVファイルの形式が正しくありません。「CSVテンプレート」からダウンロードした形式でアップロードしてください。');
                }
                continue;
            }

            $name = trim((string) ($row[0] ?? ''));
            if ($name === '') {
                continue;
            }

            $data = [
                'name' => $name,
                'type' => in_array($row[1] ?? '', ['受注取引管理', '発注取引管理', '両方で使用する'], true) ? $row[1] : '両方で使用する',
                'zip' => $row[2] ?? '',
                'pref' => $row[3] ?? '',
                'addr1' => $row[4] ?? '',
                'addr2' => $row[5] ?? '',
                'tel' => $row[6] ?? '',
                'mobile' => $row[7] ?? '',
                'fax' => $row[8] ?? '',
                'url' => $row[9] ?? '',
                'person' => $row[10] ?? '',
                'email' => $row[11] ?? '',
                'memo' => $row[12] ?? '',
            ];
            // 新規のみ追加。既存の顧客は更新せずスキップする。
            $exists = Customer::where('name', $name)->exists();
            if ($exists) {
                continue;
            }
            Customer::create($data);
            $added++;
        }

        fclose($handle);

        return redirect()->route('customer')->with('status', "インポート完了: 新規{$added}件");
    }

    /**
     * バリデーションエラーメッセージ内の項目名を日本語化する
     */
    private function attributeNames(): array
    {
        return [
            'name' => '会社名',
            'type' => '顧客タイプ',
            'zip' => '郵便番号',
            'pref' => '都道府県',
            'addr1' => '住所(市区町村・丁番地)',
            'addr2' => '住所2(建物名・部屋番号)',
            'tel' => '電話番号',
            'mobile' => '携帯電話番号',
            'fax' => 'ファックス番号',
            'url' => 'ウェブサイトURL',
            'person' => '担当者名',
            'email' => 'メールアドレス',
            'memo' => 'メモ',
        ];
    }

    /**
     * 電話番号系項目(半角数字とハイフンのみ許容)のエラーメッセージ
     */
    private function phoneMessages(): array
    {
        return [
            'tel.regex' => '無効な電話番号です。',
            'mobile.regex' => '無効な電話番号です。',
            'fax.regex' => '無効な電話番号です。',
        ];
    }
}
