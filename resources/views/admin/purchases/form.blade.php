<div>
    <h3>{{ $purchase ? '取引書類編集' : '取引書類一覧(アップロード)作成' }}</h3>
    @include('admin.partials.error-summary')
    <form method="POST" action="{{ $purchase ? route('purchase.update', $purchase) : route('purchase.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($purchase)
        @method('PUT')
        @endif
        <input type="hidden" name="purchase_id" value="{{ $purchase->id ?? '' }}">
        <div class="card">
            <div class="grid2">
                <div>
                    <div class="field">
                        <label>取引先名<span class="req">必須</span></label>
                        <select name="cust_id">
                            <option value="">選択してください</option>
                            @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('cust_id', $purchase->cust_id ?? '') === $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('cust_id')<div class="field-error">{{ $message }}</div>@enderror
                        <div style="margin-top:6px">
                            <a class="btn ghost small" href="{{ route('customer.create') }}" target="_blank">顧客情報を新規登録</a>
                            <span class="muted">別タブで登録後、この一覧を再読み込みしてください。</span>
                        </div>
                    </div>
                    <div class="field">
                        <label>取引年月日<span class="req">必須</span></label>
                        <input type="date" name="date" value="{{ old('date', optional($purchase?->date)->format('Y-m-d')) }}">
                        @error('date')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>入金方法<span class="req">必須</span></label>
                        <select name="method">
                            <option value="">選択してください</option>
                            @foreach (\App\Services\PurchaseService::METHODS as $method)
                            <option value="{{ $method }}" {{ old('method', $purchase->method ?? '') === $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                        @error('method')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>取引金額(税抜)<span class="req">必須</span></label>
                        <input type="number" name="amount" value="{{ old('amount', $purchase->amount ?? '') }}" oninput="purchaseAmountInput(this)">
                        <div class="hint">半角数字のみ入力可</div>
                        @error('amount')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>税額<span class="req">必須</span></label>
                        <input type="number" name="tax" id="pf_tax" value="{{ old('tax', $purchase->tax ?? '') }}">
                        <div class="hint">半角数字のみ入力可</div>
                        @error('tax')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>ステータス<span class="req">必須</span></label>
                        <select name="status">
                            @foreach (\App\Services\PurchaseService::STATUSES as $status)
                            <option value="{{ $status }}" {{ old('status', $purchase->status ?? '未払い') === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div>
                    @foreach (\App\Services\PurchaseService::DOCS as $key => $label)
                    @php
                        $existing = $purchase->files[$key] ?? null;
                        $isContract = $key === 'contract';
                        $accept = $isContract ? '.pdf,.jpg,.jpeg,.png' : '.pdf';
                        $formatHint = $isContract ? 'PDF, JPG, PNG (10MBまで)' : 'PDFのみ (10MBまで)';
                    @endphp
                    <div class="field">
                        <label>{{ $label }}<span class="opt">任意</span></label>
                        <div class="upload-box {{ $existing ? 'has' : '' }}">
                            @if ($existing)
                            ✓ アップロード済み: {{ $existing['name'] }}
                            @else
                            ⬆ ファイルを選択してください<br>{{ $formatHint }}
                            @endif
                        </div>
                        <input type="file" name="{{ $key }}" accept="{{ $accept }}" style="margin-top:6px">
                        <div class="hint">{{ $formatHint }}</div>
                        @error($key)<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    @endforeach
                    <div class="field">
                        <label>メモ<span class="opt">任意</span></label>
                        <textarea name="memo" rows="2">{{ old('memo', $purchase->memo ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="formfoot">
            <button class="btn ghost" type="button" onclick="closeModal()">キャンセル</button>
            <button class="btn" type="submit">{{ $purchase ? '更新' : '作成' }}</button>
        </div>
    </form>
</div>
