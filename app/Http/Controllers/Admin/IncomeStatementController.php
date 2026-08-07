<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeStatement\UpdateIncomeStatementRequest;
use App\Services\IncomeStatementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncomeStatementController extends Controller
{
    public function __construct(private readonly IncomeStatementService $incomeStatementService) {}

    public function edit(): View
    {
        $incomeStatement = $this->incomeStatementService->get();
        $totals = $this->incomeStatementService->totals($incomeStatement);

        return view('admin.income_statements.edit', compact('incomeStatement', 'totals'));
    }

    public function update(UpdateIncomeStatementRequest $request): RedirectResponse
    {
        $incomeStatement = $this->incomeStatementService->get();
        $this->incomeStatementService->update($incomeStatement, $request->validated());

        return redirect()->route('pl')->with('status', '損益計算書を保存しました。');
    }

    public function export(): StreamedResponse
    {
        $incomeStatement = $this->incomeStatementService->get();
        $rows = $this->incomeStatementService->exportRows($incomeStatement);

        return $this->streamCsv('損益計算書.csv', $rows);
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
