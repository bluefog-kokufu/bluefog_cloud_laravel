<div>
    <h3>顧客編集</h3>
    <form method="POST" action="{{ route('customer.update', $customer) }}" id="customer-edit-form">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="field"><label><span class="req">必須</span>会社名</label><input type="text" name="name" value="{{ old('name', $customer->name) }}"></div>
            <div class="field"><label><span class="req">必須</span>顧客タイプ</label>
                <div style="display:flex;gap:18px;align-items:center;padding:8px 0">
                    @foreach(['受注取引管理', '発注取引管理', '両方で使用する'] as $type)
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:13px;color:var(--text)">
                        <input type="radio" name="type" value="{{ $type }}" style="width:auto" {{ old('type', $customer->type) === $type ? 'checked' : '' }}> {{ $type }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="field"><label>郵便番号</label><input type="text" name="zip" value="{{ old('zip', $customer->zip) }}" placeholder="1000001"></div>
            <div class="field"><label>都道府県</label><input type="text" name="pref" value="{{ old('pref', $customer->pref) }}"></div>
            <div class="field"><label>住所(市区町村・丁番地)</label><input type="text" name="addr1" value="{{ old('addr1', $customer->addr1) }}"></div>
            <div class="field"><label>住所2(建物名・部屋番号)</label><input type="text" name="addr2" value="{{ old('addr2', $customer->addr2) }}"></div>
            <div class="field"><label>電話番号</label><input type="text" name="tel" value="{{ old('tel', $customer->tel) }}"></div>
            <div class="field"><label>携帯電話番号</label><input type="text" name="mobile" value="{{ old('mobile', $customer->mobile) }}"></div>
            <div class="field"><label>ファックス番号</label><input type="text" name="fax" value="{{ old('fax', $customer->fax) }}"></div>
            <div class="field"><label>ウェブサイトURL</label><input type="text" name="url" value="{{ old('url', $customer->url) }}"></div>
            <div class="field"><label>担当者名</label><input type="text" name="person" value="{{ old('person', $customer->person) }}"></div>
            <div class="field"><label>メールアドレス</label><input type="email" name="email" value="{{ old('email', $customer->email) }}"></div>
            <div class="field"><label>メモ</label><textarea name="memo" rows="4">{{ old('memo', $customer->memo) }}</textarea></div>
        </div>
        <div class="toolbar" style="justify-content:flex-end; gap:10px;">
            <button class="btn ghost" type="button" onclick="closeModal()">キャンセル</button>
            <button class="btn" type="submit">保存</button>
        </div>
    </form>
</div>