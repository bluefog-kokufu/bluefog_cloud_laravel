@if (empty($accounts))
<div class="muted" style="padding:14px 0">仕訳帳に仕訳を登録すると、勘定科目ごとに自動で表示されます。</div>
@else
@foreach ($accounts as $acct)
<div style="margin-bottom:22px">
    <div style="border-left:4px solid var(--navy);background:#f4f8fd;padding:8px 12px;font-weight:700;color:var(--navy);margin-bottom:8px">勘定科目: {{ $acct }}</div>
    <div style="overflow-x:auto">
        <table class="sheet">
            <thead>
                <tr><th>伝票No.</th><th>年</th><th>月</th><th>日</th><th>摘要</th><th>相手科目</th><th>借方</th><th>貸方</th><th>残高</th></tr>
            </thead>
            <tbody>
                @foreach ($entriesByAccount[$acct] as $entry)
                <tr>
                    <td>{{ $entry['no'] }}</td><td>{{ $entry['year'] }}</td><td>{{ $entry['m'] }}</td><td>{{ $entry['d'] }}</td>
                    <td>{{ $entry['note'] }}</td><td>{{ $entry['other'] }}</td>
                    <td class="num" style="padding:4px 8px">{{ $entry['dr'] ? number_format($entry['dr']) : '' }}</td>
                    <td class="num" style="padding:4px 8px">{{ $entry['cr'] ? number_format($entry['cr']) : '' }}</td>
                    <td class="num" style="padding:4px 8px">{{ number_format($entry['balance']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
<div class="formfoot">
    <a class="btn ghost" href="{{ route('ledger', ['tab' => 'jnl']) }}">仕訳帳へ</a>
    <a class="btn ghost" href="{{ route('ledger.export') }}">CSV保存</a>
</div>
@endif
