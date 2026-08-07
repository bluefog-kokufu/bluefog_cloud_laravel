<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashFlowStatement\UpdateCashFlowStatementRequest;
use App\Services\CashFlowStatementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CashFlowStatementController extends Controller
{
    public function __construct(private readonly CashFlowStatementService $cashFlowStatementService) {}

    public function edit(): View
    {
        $cashFlowStatement = $this->cashFlowStatementService->get();
        $totals = $this->cashFlowStatementService->totals($cashFlowStatement);

        return view('admin.cash_flow_statements.edit', compact('cashFlowStatement', 'totals'));
    }

    public function update(UpdateCashFlowStatementRequest $request): RedirectResponse
    {
        $cashFlowStatement = $this->cashFlowStatementService->get();
        $this->cashFlowStatementService->update($cashFlowStatement, $request->validated());

        return redirect()->route('cf')->with('status', 'キャッシュフロー計算書を保存しました。');
    }

    public function export(): StreamedResponse
    {
        $cashFlowStatement = $this->cashFlowStatementService->get();
        $rows = $this->cashFlowStatementService->exportRows($cashFlowStatement);

        return $this->streamCsv('キャッシュフロー計算書.csv', $rows);
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
