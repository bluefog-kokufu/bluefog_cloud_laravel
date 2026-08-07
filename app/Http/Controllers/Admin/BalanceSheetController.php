<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BalanceSheet\UpdateBalanceSheetRequest;
use App\Services\BalanceSheetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BalanceSheetController extends Controller
{
    public function __construct(private readonly BalanceSheetService $balanceSheetService) {}

    public function edit(): View
    {
        $balanceSheet = $this->balanceSheetService->get();
        $totals = $this->balanceSheetService->totals($balanceSheet);

        return view('admin.balance_sheets.edit', compact('balanceSheet', 'totals'));
    }

    public function update(UpdateBalanceSheetRequest $request): RedirectResponse
    {
        $balanceSheet = $this->balanceSheetService->get();
        $this->balanceSheetService->update($balanceSheet, $request->validated());

        return redirect()->route('bs')->with('status', '貸借対照表を保存しました。');
    }

    public function export(): StreamedResponse
    {
        $balanceSheet = $this->balanceSheetService->get();
        $rows = $this->balanceSheetService->exportRows($balanceSheet);

        return $this->streamCsv('貸借対照表.csv', $rows);
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
