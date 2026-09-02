<?php

namespace App\Services;

use App\Repositories\LedgerEntryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class LedgerService
{
    /** 勘定科目の入力候補 */
    public const ACCOUNTS = [
        '現金', '普通預金', '売掛金', '買掛金', '商品', '備品', '借入金', '資本金',
        '売上高', '仕入高', '給料手当', '地代家賃', '通信費', '消耗品費', '雑費',
    ];

    private const CSV_HEADER = ['伝票No.', '年', '月', '日', '借方勘定科目', '金額', '貸方勘定科目', '金額', '勘定科目', '摘要', '仕丁', '借方', '貸方', '残高'];

    public function __construct(private readonly LedgerEntryRepositoryInterface $ledgerEntries) {}

    public function rows(): Collection
    {
        return $this->ledgerEntries->all();
    }

    /**
     * 仕訳帳全体を置き換えて保存する
     */
    public function update(array $rows): void
    {
        $this->ledgerEntries->replaceAll($rows);
    }

    /**
     * 借方・貸方それぞれの合計を算出する
     */
    public function totals(Collection $rows): array
    {
        return [
            'dr' => (int) $rows->sum('dr_amt'),
            'cr' => (int) $rows->sum('cr_amt'),
        ];
    }

    /**
     * 既定の勘定科目に、仕訳帳で使われている科目を加えた入力候補一覧を取得する
     */
    public function accountOptions(Collection $rows): array
    {
        $options = self::ACCOUNTS;
        foreach ($this->accountsUsed($rows) as $acct) {
            if (! in_array($acct, $options, true)) {
                $options[] = $acct;
            }
        }

        return $options;
    }

    /**
     * 仕訳帳に登場した勘定科目を初出順に取得する（総勘定元帳タブの見出し用）
     */
    public function accountsUsed(Collection $rows): array
    {
        $used = [];
        foreach ($rows as $row) {
            foreach ([$row->dr_acct, $row->cr_acct] as $acct) {
                if ($acct && ! in_array($acct, $used, true)) {
                    $used[] = $acct;
                }
            }
        }

        return $used;
    }

    /**
     * 指定した勘定科目の取引履歴を日付順に並べ、残高を計算する
     */
    public function entriesForAccount(Collection $rows, string $account): array
    {
        $entries = [];
        foreach ($rows as $row) {
            if ($row->dr_acct === $account && (int) $row->dr_amt !== 0) {
                $entries[] = [
                    'no' => $row->no, 'year' => $row->year, 'm' => $row->m, 'd' => $row->d,
                    'note' => $row->note, 'other' => $row->cr_acct ?: '諸口',
                    'dr' => (int) $row->dr_amt, 'cr' => 0,
                ];
            }
            if ($row->cr_acct === $account && (int) $row->cr_amt !== 0) {
                $entries[] = [
                    'no' => $row->no, 'year' => $row->year, 'm' => $row->m, 'd' => $row->d,
                    'note' => $row->note, 'other' => $row->dr_acct ?: '諸口',
                    'dr' => 0, 'cr' => (int) $row->cr_amt,
                ];
            }
        }

        usort($entries, fn ($a, $b) => $this->sortKey($a) <=> $this->sortKey($b));

        $balance = 0;
        foreach ($entries as &$entry) {
            $balance += $entry['dr'] - $entry['cr'];
            $entry['balance'] = $balance;
        }

        return $entries;
    }

    private function sortKey(array $entry): string
    {
        return sprintf('%04s-%02s-%02s', $entry['year'] ?: '0', $entry['m'] ?: '0', $entry['d'] ?: '0');
    }

    public function exportRows(Collection $rows): array
    {
        $csvRows = [self::CSV_HEADER];
        foreach ($rows as $row) {
            $csvRows[] = [
                $row->no, $row->year, $row->m, $row->d,
                $row->dr_acct, $row->dr_amt ?: '', $row->cr_acct, $row->cr_amt ?: '',
                implode('／', array_filter([$row->dr_acct, $row->cr_acct])),
                $row->note, $row->page, $row->dr_amt ?: '', $row->cr_amt ?: '', '',
            ];
        }

        return $csvRows;
    }

    /**
     * CSVをインポートし、仕訳帳に行を追加する。「伝票No.」欄がヘッダーと一致する先頭行は読み飛ばす。
     */
    public function importCsv(string $realPath): int
    {
        $handle = fopen($realPath, 'r');
        if ($handle === false) {
            return 0;
        }

        $rows = [];
        $first = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($first) {
                $first = false;
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $row[0]);
                if (($row[0] ?? null) !== self::CSV_HEADER[0]) {
                    fclose($handle);

                    throw new RuntimeException('CSVファイルの形式が正しくありません。「CSVテンプレート」からダウンロードした形式でアップロードしてください。');
                }
                continue;
            }

            $no = trim((string) ($row[0] ?? ''));
            $drAcct = trim((string) ($row[4] ?? ''));
            $crAcct = trim((string) ($row[6] ?? ''));
            if ($no === '' && $drAcct === '' && $crAcct === '') {
                continue;
            }

            $rows[] = [
                'no' => $no,
                'year' => $row[1] ?? '',
                'm' => $row[2] ?? '',
                'd' => $row[3] ?? '',
                'dr_acct' => $drAcct,
                'dr_amt' => (int) ($row[5] ?? 0),
                'cr_acct' => $crAcct,
                'cr_amt' => (int) ($row[7] ?? 0),
                'note' => $row[9] ?? '',
                'page' => $row[10] ?? '',
            ];
        }
        fclose($handle);

        return $this->ledgerEntries->createMany($rows);
    }
}
