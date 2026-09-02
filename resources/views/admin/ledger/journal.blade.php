@php
    $tableRows = old('rows', $rows->map(fn ($r) => $r->toArray())->all());
@endphp
<form method="POST" action="{{ route('ledger.update') }}">
    @csrf
    @method('PUT')
    <div class="sheet-head">日付: <input type="date" id="lg_date" value="{{ now()->format('Y-m-d') }}" style="width:160px"></div>
    <div style="overflow-x:auto">
        <datalist id="acctList">
            @foreach ($acctOptions as $acct)
            <option value="{{ $acct }}"></option>
            @endforeach
        </datalist>
        <table class="sheet" id="lgTable">
            <thead>
                <tr>
                    <th>伝票No.</th><th>年</th><th>月</th><th>日</th>
                    <th>借方勘定科目</th><th>金額</th><th>貸方勘定科目</th><th>金額</th>
                    <th>勘定科目</th><th>摘要</th><th>仕丁</th><th>借方</th><th>貸方</th><th>残高</th><th></th>
                </tr>
            </thead>
            <tbody id="lgTableBody" data-next-index="{{ count($tableRows) }}">
                @foreach ($tableRows as $i => $r)
                <tr>
                    <td><input type="text" name="rows[{{ $i }}][no]" value="{{ $r['no'] ?? '' }}" style="width:80px"></td>
                    <td><input type="text" name="rows[{{ $i }}][year]" value="{{ $r['year'] ?? '' }}" style="width:60px;text-align:center"></td>
                    <td><input type="text" name="rows[{{ $i }}][m]" value="{{ $r['m'] ?? '' }}" style="width:44px;text-align:center"></td>
                    <td><input type="text" name="rows[{{ $i }}][d]" value="{{ $r['d'] ?? '' }}" style="width:44px;text-align:center"></td>
                    <td><input type="text" name="rows[{{ $i }}][dr_acct]" list="acctList" value="{{ $r['dr_acct'] ?? '' }}" style="width:110px" oninput="ledgerRowSync(this)"></td>
                    <td><input type="text" inputmode="numeric" name="rows[{{ $i }}][dr_amt]" class="num" value="{{ ($r['dr_amt'] ?? '') !== '' ? number_format($r['dr_amt']) : '' }}" style="width:100px;text-align:right" oninput="ledgerRowSync(this)" onblur="ledgerAmountBlur(this)"></td>
                    <td><input type="text" name="rows[{{ $i }}][cr_acct]" list="acctList" value="{{ $r['cr_acct'] ?? '' }}" style="width:110px" oninput="ledgerRowSync(this)"></td>
                    <td><input type="text" inputmode="numeric" name="rows[{{ $i }}][cr_amt]" class="num" value="{{ ($r['cr_amt'] ?? '') !== '' ? number_format($r['cr_amt']) : '' }}" style="width:100px;text-align:right" oninput="ledgerRowSync(this)" onblur="ledgerAmountBlur(this)"></td>
                    <td class="muted lg-acct-pair" style="padding:4px 8px">{{ implode('／', array_filter([$r['dr_acct'] ?? '', $r['cr_acct'] ?? ''])) }}</td>
                    <td><input type="text" name="rows[{{ $i }}][note]" value="{{ $r['note'] ?? '' }}" style="width:140px"></td>
                    <td><input type="text" name="rows[{{ $i }}][page]" value="{{ $r['page'] ?? '' }}" style="width:44px;text-align:center"></td>
                    <td class="num muted lg-dr-mirror" style="padding:4px 8px">{{ ($r['dr_amt'] ?? 0) ? number_format($r['dr_amt']) : '' }}</td>
                    <td class="num muted lg-cr-mirror" style="padding:4px 8px">{{ ($r['cr_amt'] ?? 0) ? number_format($r['cr_amt']) : '' }}</td>
                    <td class="num muted" style="padding:4px 8px"></td>
                    <td><button type="button" class="icon-btn" onclick="ledgerRowDel(this)">🗑</button></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="total" colspan="5">合計</td>
                    <td class="total num" id="lgDrTotal" style="padding:4px 8px">{{ number_format($totals['dr']) }}</td>
                    <td class="total"></td>
                    <td class="total num" id="lgCrTotal" style="padding:4px 8px">{{ number_format($totals['cr']) }}</td>
                    <td class="total" colspan="3"></td>
                    <td class="total num" id="lgDrTotal2" style="padding:4px 8px">{{ number_format($totals['dr']) }}</td>
                    <td class="total num" id="lgCrTotal2" style="padding:4px 8px">{{ number_format($totals['cr']) }}</td>
                    <td class="total"></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @error('rows')<div class="field-error">{{ $message }}</div>@enderror
    <div class="formfoot">
        <button class="btn" type="submit">保存する</button>
        <a class="btn ghost" href="{{ route('ledger.export') }}">CSV保存</a>
    </div>
</form>
