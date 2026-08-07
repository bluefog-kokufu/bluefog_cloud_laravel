<div>
    <h3>{{ $purchase ? '取引書類編集' : '取引書類一覧(アップロード)作成' }}</h3>
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
                        <label><span class="req">必須</span>取引先名</label>
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
                        <label><span class="req">必須</span>取引年月日</label>
                        <input type="date" name="date" value="{{ old('date', optional($purchase?->date)->format('Y-m-d')) }}">
                        @error('date')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label><span class="req">必須</span>入金方法</label>
                        <select name="method">
                            <option value="">選択してください</option>
                            @foreach (\App\Services\PurchaseService::METHODS as $method)
                            <option value="{{ $method }}" {{ old('method', $purchase->method ?? '') === $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                        @error('method')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label><span class="req">必須</span>取引金額(税抜)</label>
                        <input type="number" name="amount" value="{{ old('amount', $purchase->amount ?? '') }}" oninput="purchaseAmountInput(this)">
                        @error('amount')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label><span class="req">必須</span>税額</label>
                        <input type="number" name="tax" id="pf_tax" value="{{ old('tax', $purchase->tax ?? '') }}">
                        @error('tax')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label><span class="req">必須</span>ステータス</label>
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
                    @php $existing = $purchase->files[$key] ?? null; @endphp
                    <div class="field">
                        <label>{{ $label }}</label>
                        <div class="upload-box {{ $existing ? 'has' : '' }}">
                            @if ($existing)
                            ✓ アップロード済み: {{ $existing['name'] }}
                            @else
                            ⬆ ファイルを選択してください<br>PDF, JPG, PNG (up to 10MB)
                            @endif
                        </div>
                        <input type="file" name="{{ $key }}" accept=".pdf,.jpg,.jpeg,.png" style="margin-top:6px">
                        @error($key)<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    @endforeach
                    <div class="field">
                        <label>メモ</label>
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
