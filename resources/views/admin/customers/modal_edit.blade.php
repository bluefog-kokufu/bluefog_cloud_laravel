<div>
    <h3>顧客編集</h3>
    <form method="POST" action="{{ route('customer.update', $customer) }}" id="customer-edit-form">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="field"><label>会社名<span class="req">必須</span></label><input type="text" name="name" value="{{ old('name', $customer->name) }}"></div>
            <div class="field"><label>顧客タイプ<span class="req">必須</span></label>
                <div style="display:flex;gap:18px;align-items:center;padding:8px 0">
                    @foreach(['受注取引管理', '発注取引管理', '両方で使用する'] as $type)
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:13px;color:var(--text)">
                        <input type="radio" name="type" value="{{ $type }}" style="width:auto" {{ old('type', $customer->type) === $type ? 'checked' : '' }}> {{ $type }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="field">
                <label>郵便番号<span class="opt">任意</span></label>
                <input type="text" name="zip" value="{{ old('zip', $customer->zip) }}" placeholder="1000001">
                <div class="hint">例: 1000001 または 100-0001</div>
            </div>
            <div class="field">
                <label>都道府県<span class="opt">任意</span></label>
                <select name="pref">
                    <option value="">選択してください</option>
                    @foreach (\App\Http\Controllers\Admin\CustomerController::PREFS as $pref)
                    <option value="{{ $pref }}" {{ old('pref', $customer->pref) === $pref ? 'selected' : '' }}>{{ $pref }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>住所(市区町村・丁番地)<span class="opt">任意</span></label><input type="text" name="addr1" value="{{ old('addr1', $customer->addr1) }}"></div>
            <div class="field"><label>住所2(建物名・部屋番号)<span class="opt">任意</span></label><input type="text" name="addr2" value="{{ old('addr2', $customer->addr2) }}"></div>
            <div class="field">
                <label>電話番号<span class="opt">任意</span></label>
                <input type="text" name="tel" value="{{ old('tel', $customer->tel) }}">
                <div class="hint">半角数字とハイフンのみ入力可(例: 090-1234-5678)</div>
            </div>
            <div class="field">
                <label>携帯電話番号<span class="opt">任意</span></label>
                <input type="text" name="mobile" value="{{ old('mobile', $customer->mobile) }}">
                <div class="hint">半角数字とハイフンのみ入力可(例: 090-1234-5678)</div>
            </div>
            <div class="field">
                <label>ファックス番号<span class="opt">任意</span></label>
                <input type="text" name="fax" value="{{ old('fax', $customer->fax) }}">
                <div class="hint">半角数字とハイフンのみ入力可(例: 03-0000-0001)</div>
            </div>
            <div class="field">
                <label>ウェブサイトURL<span class="opt">任意</span></label>
                <input type="url" name="url" value="{{ old('url', $customer->url) }}" placeholder="https://example.com">
                <div class="hint">http:// または https:// から始まるURL形式</div>
            </div>
            <div class="field"><label>担当者名<span class="opt">任意</span></label><input type="text" name="person" value="{{ old('person', $customer->person) }}"></div>
            <div class="field">
                <label>メールアドレス<span class="opt">任意</span></label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}" placeholder="user@example.com">
                <div class="hint">例: user@example.com</div>
            </div>
            <div class="field"><label>メモ<span class="opt">任意</span></label><textarea name="memo" rows="4">{{ old('memo', $customer->memo) }}</textarea></div>
        </div>
        <div class="toolbar" style="justify-content:flex-end; gap:10px;">
            <button class="btn ghost" type="button" onclick="closeModal()">キャンセル</button>
            <button class="btn" type="button" onclick="customerEditSave('{{ $customer->id }}')">保存</button>
        </div>
    </form>
</div>
