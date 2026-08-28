<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\ImportSaleCsvRequest;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Models\Customer;
use App\Models\Sale;
use App\Repositories\SaleRepositoryInterface;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService,
        private readonly SaleRepositoryInterface $saleRepository,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'method', 'status', 'from', 'to']);
        $sales = $this->saleService->paginate($filters);
        $customers = Customer::orderBy('name')->get();

        // バリデーションエラーで戻ってきた場合、直前に開いていたフォームをモーダルで再表示する
        $reopenSale = old('sale_id') ? $this->saleRepository->findWithItems(old('sale_id')) : null;

        return view('admin.sales.index', compact('sales', 'customers', 'filters', 'reopenSale'));
    }

    public function create(): string
    {
        $customers = Customer::orderBy('name')->get();

        return view('admin.sales.form', ['sale' => null, 'customers' => $customers])->render();
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $this->saleService->create($request->validated(), $this->uploadedFiles($request));

        return redirect()->route('sale')->with('status', '取引を作成しました。');
    }

    public function edit(Sale $sale): string
    {
        $sale->load('items');
        $customers = Customer::orderBy('name')->get();

        return view('admin.sales.form', ['sale' => $sale, 'customers' => $customers])->render();
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $this->saleService->update($sale, $request->validated(), $this->uploadedFiles($request));

        return redirect()->route('sale')->with('status', '取引を更新しました。');
    }

    public function destroy(Sale $sale)
    {
        $this->saleService->delete($sale);

        return response()->json(['status' => 'deleted']);
    }

    public function invoice(Sale $sale): string
    {
        $data = $this->saleService->invoiceData($sale);

        return view('admin.sales.invoice', $data)->render();
    }

    public function seal(Sale $sale, string $key): StreamedResponse
    {
        abort_unless(array_key_exists($key, SaleService::SEALS), 404);

        return $this->saleService->sealResponse($sale, $key);
    }

    public function issue(Sale $sale): RedirectResponse
    {
        $this->saleService->issueInvoice($sale);

        return redirect()->route('sale')->with('status', '取引を請求済にしました。');
    }

    public function template(): StreamedResponse
    {
        return $this->streamCsv('受注取引一覧_テンプレート.csv', $this->saleService->templateRows());
    }

    public function export(): StreamedResponse
    {
        return $this->streamCsv('受注取引一覧.csv', $this->saleService->exportRows());
    }

    public function import(ImportSaleCsvRequest $request): RedirectResponse
    {
        $added = $this->saleService->importCsv($request->file('csv_file')->getRealPath());

        return redirect()->route('sale')->with('status', "インポート完了: {$added}件の取引を登録しました");
    }

    /**
     * リクエストからアップロードされた印鑑画像のみを取り出す
     */
    private function uploadedFiles(Request $request): array
    {
        $files = [];
        foreach (array_keys(SaleService::SEALS) as $key) {
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
