<div>
    <h3>預金口座情報の編集</h3>
    <div class="field">
        <label>入金口座(銀行名・支店名・種別・口座番号・名義)</label>
        <input type="text" id="bk_val" value="{{ $company->bank }}" placeholder="〇〇銀行 △△支店 普通 1234567 カ)〇〇">
    </div>
    <div class="formfoot">
        <button class="btn ghost" type="button" onclick="closeModal()">キャンセル</button>
        <button class="btn" type="button" onclick="companyBankSave()">保存する</button>
    </div>
</div>
