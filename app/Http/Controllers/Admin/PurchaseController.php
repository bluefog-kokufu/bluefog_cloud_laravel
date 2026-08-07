<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\ImportPurchaseCsvRequest;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Http\Requests\Purchase\UpdatePurchaseRequest;
use App\Models\Customer;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseController extends Controller
{
    public function __construct(private readonly PurchaseService $purchaseService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'method', 'status', 'from', 'to']);
        $purchases = $this->purchaseService->paginate($filters);
        $customers = Customer::orderBy('name')->get();

        // バリデーションエラーで戻ってきた場合、直前に開いていたフォームをモーダルで再表示する
        $reopenPurchase = old('purchase_id') ? $this->purchaseService->find(old('purchase_id')) : null;

        return view('admin.purchases.index', compact('purchases', 'customers', 'filters', 'reopenPurchase'));
    }

    public function create(): string
    {
        $customers = Customer::orderBy('name')->get();

        return view('admin.purchases.form', ['purchase' => null, 'customers' => $customers])->render();
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['cust_id', 'date', 'method', 'amount', 'tax', 'status', 'memo']);
        $this->purchaseService->create($data, $this->uploadedFiles($request));

        return redirect()->route('purchase')->with('status', '取引書類を作成しました。');
    }

    public function edit(Purchase $purchase): string
    {
        $customers = Customer::orderBy('name')->get();

        return view('admin.purchases.form', ['purchase' => $purchase, 'customers' => $customers])->render();
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        $data = $request->safe()->only(['cust_id', 'date', 'method', 'amount', 'tax', 'status', 'memo']);
        $this->purchaseService->update($purchase, $data, $this->uploadedFiles($request));

        return redirect()->route('purchase')->with('status', '取引書類を更新しました。');
    }

    public function destroy(Purchase $purchase)
    {
        $this->purchaseService->delete($purchase);

        return response()->json(['status' => 'deleted']);
    }

    public function file(Purchase $purchase, string $key): StreamedResponse
    {
        abort_unless(array_key_exists($key, PurchaseService::DOCS), 404);

        return $this->purchaseService->fileResponse($purchase, $key);
    }

    public function template(): StreamedResponse
    {
        return $this->streamCsv('発注取引一覧_テンプレート.csv', $this->purchaseService->templateRows());
    }

    public function export(): StreamedResponse
    {
        return $this->streamCsv('発注取引一覧.csv', $this->purchaseService->exportRows());
    }

    public function import(ImportPurchaseCsvRequest $request): RedirectResponse
    {
        $added = $this->purchaseService->importCsv($request->file('csv_file')->getRealPath());

        return redirect()->route('purchase')->with('status', "インポート完了: {$added}件の取引を登録しました");
    }

    /**
     * リクエストからアップロードされた書類ファイルのみを取り出す
     */
    private function uploadedFiles(Request $request): array
    {
        $files = [];
        foreach (array_keys(PurchaseService::DOCS) as $key) {
            if ($request->hasFile($key)) {
                $files[$key] = $request->file($key);
            }
        }

        return $files;
    }

    private function streamCsv(string $filename, array $rows): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
