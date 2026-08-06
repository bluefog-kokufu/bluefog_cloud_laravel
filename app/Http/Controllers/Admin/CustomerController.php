<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
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
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'query'));
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:受注取引管理,発注取引管理,両方で使用する'],
            'zip' => ['nullable', 'string', 'max:20'],
            'pref' => ['nullable', 'string', 'max:50'],
            'addr1' => ['nullable', 'string', 'max:255'],
            'addr2' => ['nullable', 'string', 'max:255'],
            'tel' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'fax' => ['nullable', 'string', 'max:50'],
            'url' => ['nullable', 'string', 'max:255'],
            'person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        Customer::create($data);

        return redirect()->route('customer')->with('status', '顧客を作成しました。');
    }

    public function edit(Customer $customer): string
    {
        return view('admin.customers.modal_edit', compact('customer'))->render();
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:受注取引管理,発注取引管理,両方で使用する'],
            'zip' => ['nullable', 'string', 'max:20'],
            'pref' => ['nullable', 'string', 'max:50'],
            'addr1' => ['nullable', 'string', 'max:255'],
            'addr2' => ['nullable', 'string', 'max:255'],
            'tel' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'fax' => ['nullable', 'string', 'max:50'],
            'url' => ['nullable', 'string', 'max:255'],
            'person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        $customer->update($data);

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
        ]);

        $file = $request->file('csv_file');
        $added = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) === false) {
            return redirect()->route('customer')->with('status', 'CSVファイルを読み込めませんでした。');
        }

        $first = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($first) {
                $first = false;
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
                if (isset($row[0]) && $row[0] === '会社名') {
                    continue;
                }
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

    /*
    public function create(): View
    {
        return view('admin.notices.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'published_at' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'link' => ['nullable', 'string', 'max:255'],
            'pdf_link' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        Notice::create($data);

        return redirect()->route('admin.notices.index')->with('status', 'お知らせを追加しました。');
    }

    public function edit(Notice $notice): View
    {
        return view('admin.notices.form', compact('notice'));
    }

    public function update(Request $request, Notice $notice): RedirectResponse
    {
        $data = $request->validate([
            'published_at' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'link' => ['nullable', 'string', 'max:255'],
            'pdf_link' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $notice->update($data);

        return redirect()->route('admin.notices.index')->with('status', 'お知らせを更新しました。');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        $notice->delete();

        return redirect()->route('admin.notices.index')->with('status', 'お知らせを削除しました。');
    }
    */
}
