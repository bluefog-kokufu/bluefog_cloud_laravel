<div>
    <h3>顧客作成</h3>
    <div class="card">
        <div class="field"><label><span class="req">必須</span>会社名</label><input type="text" id="cq_name"></div>
        <div class="field"><label><span class="req">必須</span>顧客タイプ</label>
            <div style="display:flex;gap:18px;align-items:center;padding:8px 0">
                @foreach(['受注取引管理', '発注取引管理', '両方で使用する'] as $type)
                <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:13px;color:var(--text)">
                    <input type="radio" name="cq_type" value="{{ $type }}" style="width:auto" {{ $type === '受注取引管理' ? 'checked' : '' }}> {{ $type }}
                </label>
                @endforeach
            </div>
        </div>
        <div class="field">
            <label>郵便番号</label>
            <div style="display:flex; gap:8px; align-items:center;">
                <input type="text" id="zip" placeholder="1000001" style="flex:1;">
                <button type="button" class="btn small" onclick="fillAddressFromZip()">自動入力アドレス</button>
            </div>
            <div id="zipMsg" class="muted" style="margin-top:4px;"></div>
        </div>
        <div class="field"><label>都道府県</label><input type="text" id="pref"></div>
        <div class="field"><label>住所(市区町村・丁番地)</label><input type="text" id="addr1"></div>
        <div class="field"><label>住所2(建物名・部屋番号)</label><input type="text" id="cq_addr2"></div>
        <div class="field"><label>電話番号</label><input type="text" id="cq_tel"></div>
        <div class="field"><label>携帯電話番号</label><input type="text" id="cq_mobile"></div>
        <div class="field"><label>ファックス番号</label><input type="text" id="cq_fax"></div>
        <div class="field"><label>ウェブサイトURL</label><input type="text" id="cq_url"></div>
        <div class="field"><label>担当者名</label><input type="text" id="cq_person"></div>
        <div class="field"><label>メールアドレス</label><input type="email" id="cq_email"></div>
        <div class="field"><label>メモ</label><textarea id="cq_memo" rows="2"></textarea></div>
    </div>
    <div class="formfoot">
        <button class="btn ghost" type="button" onclick="closeModal()">キャンセル</button>
        <button class="btn" type="button" onclick="customerQuickSave()">作成</button>
    </div>
</div>
