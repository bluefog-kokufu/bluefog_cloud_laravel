<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ledger\ImportLedgerCsvRequest;
use App\Http\Requests\Ledger\UpdateLedgerRequest;
use App\Services\LedgerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LedgerController extends Controller
{
    public function __construct(private readonly LedgerService $ledgerService) {}

    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'lg' ? 'lg' : 'jnl';
        $rows = $this->ledgerService->rows();

        if ($tab === 'jnl') {
            $totals = $this->ledgerService->totals($rows);
            $acctOptions = $this->ledgerService->accountOptions($rows);

            return view('admin.ledger.index', ['tab' => $tab, 'rows' => $rows, 'totals' => $totals, 'acctOptions' => $acctOptions]);
        }

        $accounts = $this->ledgerService->accountsUsed($rows);
        $entriesByAccount = collect($accounts)->mapWithKeys(
            fn ($acct) => [$acct => $this->ledgerService->entriesForAccount($rows, $acct)]
        );

        return view('admin.ledger.index', ['tab' => $tab, 'accounts' => $accounts, 'entriesByAccount' => $entriesByAccount]);
    }

    public function update(UpdateLedgerRequest $request): RedirectResponse
    {
        $this->ledgerService->update($request->validated()['rows'] ?? []);

        return redirect()->route('ledger')->with('status', '総勘定元帳を保存しました。');
    }

    public function export(): StreamedResponse
    {
        $rows = $this->ledgerService->exportRows($this->ledgerService->rows());

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="総勘定元帳.csv"',
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

    public function import(ImportLedgerCsvRequest $request): RedirectResponse
    {
        try {
            $added = $this->ledgerService->importCsv($request->file('csv_file')->getRealPath());
        } catch (RuntimeException $e) {
            return redirect()->route('ledger')->with('error', $e->getMessage());
        }

        return redirect()->route('ledger')->with('status', "インポート完了: {$added}件の仕訳を登録しました");
    }
}
