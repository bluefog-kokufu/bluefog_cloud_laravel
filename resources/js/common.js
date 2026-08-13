"use strict";
/* ================= state ================= */
const DEFAULT_DB = {
  profile:{name:"ユーザー企業 ご担当者", email:"user@user.com", pw:"password"},
  settings:{taxRate:10, rounding:"floor",
    company:{name:"ユーザー企業株式会社", regNo:"T1234567890123", zip:"600-0000",
      addr:"京都府京都市中京区〇〇町1-2-3", tel:"075-000-0000",
      bank:"〇〇銀行 △△支店 普通 1234567 ユーザーキギヨウ(カ"}},
  customers:[
    {id:"c1", name:"test1株式会社", person:"山田 太郎", email:"info@test1.co.jp", tel:"03-0000-0000", addr:"東京都千代田区1-1-1", site:"月末締め翌月末払い", regNo:"T9876543210987", memo:""},
    {id:"c2", name:"サンプル商事株式会社", person:"佐藤 花子", email:"sato@sample.co.jp", tel:"06-0000-0000", addr:"大阪府大阪市北区2-2-2", site:"20日締め翌月10日払い", regNo:"", memo:""}
  ],
  sales:[
    {id:"01h04cdeeq8pgxgswe8ab7qb83", date:"2026-05-11", custId:"c1", method:"現金", amount:55000, tax:5500, status:"請求済", invoiced:"", memo:""},
    {id:"01h10apet73rdvnymkzqf3wq5f", date:"2026-05-21", custId:"c1", method:"現金", amount:132000, tax:13200, status:"請求済", invoiced:"2026-05-21 23:37", memo:""}
  ],
  purchases:[
    {id:"5bzsfdnj2", date:"2026-05-21", custId:"c1", method:"現金", amount:88000, tax:8800, up:"2026-05-21", status:"支払い済", files:{}, memo:""},
    {id:"8jfrhv3ms", date:"2026-05-16", custId:"c1", method:"普通預金", amount:46200, tax:4620, up:"2026-05-16", status:"支払い済", files:{}, memo:""},
    {id:"85tagbt6s", date:"2026-05-15", custId:"c1", method:"現金", amount:27500, tax:2750, up:"2026-05-15", status:"支払い済", files:{}, memo:""}
  ],
  ledger:{date:"2026-05-02", rows:[{no:"test1", m:"1", d:"3", acct:"売掛金", note:"商品売上", page:"1", dr:55000, cr:0}]},
  bs:{date:"2026-08-16",
    assets:[{name:"現金及び預金", v:3550000},{name:"売掛金", v:1450000},{name:"固定資産", v:5500000}],
    liabs:[{name:"買掛金", v:980000},{name:"長期借入金", v:3200000}],
    equity:[{name:"資本金", v:5000000},{name:"利益剰余金", v:1320000}]},
  pl:{from:"2026-01-01", to:"2026-12-31",
    rows:[{name:"売上高", type:"収益", v:12800000},{name:"売上原価", type:"費用", v:6400000},{name:"販売費及び一般管理費", type:"費用", v:3200000}]},
  cf:{from:"2026-01-01", to:"2026-12-31", beg:2100000,
    op:[{name:"税引前当期純利益", v:3450000}],
    inv:[{name:"有形固定資産の取得による支出", v:-1200000}],
    fin:[{name:"長期借入金の返済による支出", v:-800000}]},
  paymentNotices:[
    {id:"SC-20260701-001", custId:"c1", title:"6月分お支払いのご案内", payDate:"2026-07-31", created:"2026-07-01",
     items:[{date:"2026-06-30", item:"業務委託料(6月分)", price:120000, unit:"式", qty:1, tax:"外税"},
            {date:"2026-06-30", item:"交通費実費", price:3200, unit:"式", qty:1, tax:"非課税"}]}
  ],
  masters:{m_landlords:[], m_contractors:[], m_repairers:[], m_agents:[], m_insurers:[]}
};
let db;
const LS_KEY="bluefog_cloud_db_v1";
function loadDb(){
  try{const s=localStorage.getItem(LS_KEY); if(s){db=JSON.parse(s);}}catch(e){}
  if(!db)db=JSON.parse(JSON.stringify(DEFAULT_DB));
  /* migration for saved data from older versions */
  if(!db.paymentNotices)db.paymentNotices=JSON.parse(JSON.stringify(DEFAULT_DB.paymentNotices));
  if(!db.masters)db.masters=JSON.parse(JSON.stringify(DEFAULT_DB.masters));
}
function save(){ try{localStorage.setItem(LS_KEY, JSON.stringify(db));}catch(e){} }

/* ================= helpers ================= */
const $=s=>document.querySelector(s);
function esc(s){return String(s==null?"":s).replace(/[&<>"']/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));}
function yen(n){n=Number(n)||0;return "¥"+n.toLocaleString("ja-JP");}
function num(n){n=Number(n)||0;return n.toLocaleString("ja-JP");}
function uid(){return Date.now().toString(36)+Math.random().toString(36).slice(2,8);}
function today(){const d=new Date();return d.toISOString().slice(0,10);}
function nowStamp(){const d=new Date();const p=x=>String(x).padStart(2,"0");return `${d.getFullYear()}.${p(d.getMonth()+1)}.${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}`;}
function dotDate(s){return s?s.replaceAll("-","."):"";}
function custName(id){const c=db.customers.find(c=>c.id===id);return c?c.name:"(削除済み)";}
function calcTax(amount){
  const r=Number(db.settings.taxRate)/100, raw=amount*r, m=db.settings.rounding;
  return m==="ceil"?Math.ceil(raw):m==="round"?Math.round(raw):Math.floor(raw);
}
function csvDownload(filename, rows){
  const csv=rows.map(r=>r.map(c=>{c=String(c==null?"":c);return /[",\n]/.test(c)?'"'+c.replaceAll('"','""')+'"':c;}).join(",")).join("\r\n");
  const blob=new Blob(["﻿"+csv],{type:"text/csv"});
  const a=document.createElement("a");a.href=URL.createObjectURL(blob);a.download=filename;a.click();
  setTimeout(()=>URL.revokeObjectURL(a.href),3000);
}
/* 郵便番号から住所を自動入力する(zipcloud API) */
function fillAddressFromZip(){
  const zipInput=document.getElementById("zip"),prefInput=document.getElementById("pref"),addr1Input=document.getElementById("addr1"),msg=document.getElementById("zipMsg");
  if(!zipInput||!prefInput||!addr1Input)return;
  const zipcode=zipInput.value.replace(/[^0-9]/g,"");
  if(zipcode.length!==7){if(msg){msg.textContent="郵便番号は7桁の数字で入力してください。";msg.style.color="var(--danger)";}return;}
  if(msg){msg.textContent="住所を検索中...";msg.style.color="";}
  fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${zipcode}`)
    .then(res=>res.json())
    .then(data=>{
      if(data.status!==200||!data.results||data.results.length===0){
        if(msg){msg.textContent="該当する住所が見つかりませんでした。";msg.style.color="var(--danger)";}
        return;
      }
      const r=data.results[0];
      prefInput.value=r.address1;
      addr1Input.value=r.address2+r.address3;
      if(msg){msg.textContent="住所を自動入力しました。";msg.style.color="var(--ok)";}
    })
    .catch(()=>{if(msg){msg.textContent="住所の取得に失敗しました。通信環境をご確認ください。";msg.style.color="var(--danger)";}});
}
function openModal(html){$("#modalBox").innerHTML=html;$("#modalBg").classList.add("open");if(document.getElementById("saleItems")){saleRecalcAll();}if(document.getElementById("paynoticeItems")){paynoticeRecalcAll();}}
function closeModal(){$("#modalBg").classList.remove("open");}
function customerEdit(id){
  fetch(`/customer/${encodeURIComponent(id)}/edit`, { headers: { Accept: 'text/html' } })
    .then(response => {
      if (!response.ok) {
        throw new Error('編集フォームの取得に失敗しました。');
      }
      return response.text();
    })
    .then(html => openModal(html))
    .catch(error => alert(error.message));
}
function customerDelete(id){
  if (!confirm('この顧客を削除しますか？')) {
    return;
  }
  const token = document.querySelector('meta[name="csrf-token"]')?.content;
  fetch(`/customer/${encodeURIComponent(id)}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': token || '',
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('削除に失敗しました。');
      }
      return response.json();
    })
    .then(() => location.reload())
    .catch(error => alert(error.message));
}
/* ================= sale (受注取引一覧) ================= */
function saleCreate(){
  fetch('/sale/create', { headers: { Accept: 'text/html' } })
    .then(response => {
      if (!response.ok) {
        throw new Error('作成フォームの取得に失敗しました。');
      }
      return response.text();
    })
    .then(html => openModal(html))
    .catch(error => alert(error.message));
}
function saleEdit(id){
  fetch(`/sale/${encodeURIComponent(id)}/edit`, { headers: { Accept: 'text/html' } })
    .then(response => {
      if (!response.ok) {
        throw new Error('編集フォームの取得に失敗しました。');
      }
      return response.text();
    })
    .then(html => openModal(html))
    .catch(error => alert(error.message));
}
function saleDelete(id){
  if (!confirm('この取引を削除しますか？')) {
    return;
  }
  const token = document.querySelector('meta[name="csrf-token"]')?.content;
  fetch(`/sale/${encodeURIComponent(id)}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': token || '',
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('削除に失敗しました。');
      }
      return response.json();
    })
    .then(() => location.reload())
    .catch(error => alert(error.message));
}
function saleInvoiceView(id){
  fetch(`/sale/${encodeURIComponent(id)}/invoice`, { headers: { Accept: 'text/html' } })
    .then(response => {
      if (!response.ok) {
        throw new Error('請求書の取得に失敗しました。');
      }
      return response.text();
    })
    .then(html => openModal(html))
    .catch(error => alert(error.message));
}
function saleInvoiceIssue(id){
  const token = document.querySelector('meta[name="csrf-token"]')?.content;
  fetch(`/sale/${encodeURIComponent(id)}/issue`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': token || '' },
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('更新に失敗しました。');
      }
      location.reload();
    })
    .catch(error => alert(error.message));
}
function saleInvoicePrint(){
  const content = document.getElementById('invoiceContent');
  const printArea = document.getElementById('invoicePrintArea');
  if (!content || !printArea) {
    return;
  }
  printArea.innerHTML = content.innerHTML;
  document.body.classList.add('printing-invoice');
  window.print();
  document.body.classList.remove('printing-invoice');
}
function saleCalcTaxAt(amount, rate){
  return Math.floor(((Number(amount) || 0) * (Number(rate) || 0)) / 100);
}
function saleRowRecalc(el){
  const tr = el.closest('tr');
  if (!tr) {
    return;
  }
  const amount = tr.querySelector('input[name$="[amount]"]')?.value;
  const rate = tr.querySelector('select[name$="[rate]"]')?.value;
  const taxCell = tr.querySelector('.sale-item-tax');
  if (taxCell) {
    taxCell.textContent = yen(saleCalcTaxAt(amount, rate));
  }
  saleRecalcAll();
}
function saleRecalcAll(){
  const rows = document.querySelectorAll('#saleItemsBody tr');
  const groups = {};
  rows.forEach(tr => {
    const amount = Number(tr.querySelector('input[name$="[amount]"]')?.value) || 0;
    const rate = Number(tr.querySelector('select[name$="[rate]"]')?.value) || 0;
    groups[rate] = (groups[rate] || 0) + amount;
    const taxCell = tr.querySelector('.sale-item-tax');
    if (taxCell) {
      taxCell.textContent = yen(saleCalcTaxAt(amount, rate));
    }
  });
  let sub = 0, tax = 0;
  Object.keys(groups).forEach(rate => {
    sub += groups[rate];
    tax += saleCalcTaxAt(groups[rate], Number(rate));
  });
  const subEl = document.getElementById('saleSub');
  const taxEl = document.getElementById('saleTaxTotal');
  const totalEl = document.getElementById('saleGrandTotal');
  if (subEl) subEl.textContent = yen(sub);
  if (taxEl) taxEl.textContent = yen(tax);
  if (totalEl) totalEl.textContent = yen(sub + tax);
}
function saleItemAdd(){
  const table = document.getElementById('saleItems');
  const tbody = document.getElementById('saleItemsBody');
  if (!table || !tbody) {
    return;
  }
  const index = Number(table.dataset.nextIndex || tbody.children.length);
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="text" name="items[${index}][name]" placeholder="品目・内容"></td>
    <td><input type="number" name="items[${index}][amount]" style="text-align:right" oninput="saleRowRecalc(this)"></td>
    <td><select name="items[${index}][rate]" onchange="saleRowRecalc(this)">
      <option value="10">10%(標準)</option>
      <option value="8">8%(軽減)</option>
      <option value="0">対象外(0%)</option>
    </select></td>
    <td class="num sale-item-tax" style="padding:4px 8px">¥0</td>
    <td><button type="button" class="icon-btn" onclick="saleItemDel(this)">🗑</button></td>`;
  tbody.appendChild(tr);
  table.dataset.nextIndex = String(index + 1);
}
function saleItemDel(btn){
  const rows = document.querySelectorAll('#saleItemsBody tr');
  if (rows.length <= 1) {
    alert('明細は1件以上必要です。');
    return;
  }
  btn.closest('tr')?.remove();
  saleRecalcAll();
}
/* ================= payment notice (支払通知書一覧) ================= */
function paynoticeCreate(){
  fetch('/paynotice/create', { headers: { Accept: 'text/html' } })
    .then(response => {
      if (!response.ok) {
        throw new Error('作成フォームの取得に失敗しました。');
      }
      return response.text();
    })
    .then(html => openModal(html))
    .catch(error => alert(error.message));
}
function paynoticeEdit(id){
  fetch(`/paynotice/${encodeURIComponent(id)}/edit`, { headers: { Accept: 'text/html' } })
    .then(response => {
      if (!response.ok) {
        throw new Error('編集フォームの取得に失敗しました。');
      }
      return response.text();
    })
    .then(html => openModal(html))
    .catch(error => alert(error.message));
}
function paynoticeView(id){
  fetch(`/paynotice/${encodeURIComponent(id)}/view`, { headers: { Accept: 'text/html' } })
    .then(response => {
      if (!response.ok) {
        throw new Error('支払通知書の取得に失敗しました。');
      }
      return response.text();
    })
    .then(html => openModal(html))
    .catch(error => alert(error.message));
}
function paynoticeDelete(id){
  if (!confirm('この支払通知書を削除しますか？')) {
    return;
  }
  const token = document.querySelector('meta[name="csrf-token"]')?.content;
  fetch(`/paynotice/${encodeURIComponent(id)}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': token || '',
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('削除に失敗しました。');
      }
      return response.json();
    })
    .then(() => location.reload())
    .catch(error => alert(error.message));
}
function paynoticePrint(){
  const content = document.getElementById('paynoticeContent');
  const printArea = document.getElementById('invoicePrintArea');
  if (!content || !printArea) {
    return;
  }
  printArea.innerHTML = content.innerHTML;
  document.body.classList.add('printing-invoice');
  window.print();
  document.body.classList.remove('printing-invoice');
}
function paynoticeCalcTaxAt(amount, taxLabel){
  const rate = taxLabel === '10%' ? 10 : (taxLabel === '8%' || taxLabel === '8%軽減税率') ? 8 : 0;
  return Math.floor(((Number(amount) || 0) * rate) / 100);
}
function paynoticeRowRecalc(el){
  paynoticeRecalcAll();
}
function paynoticeRecalcAll(){
  const rows = document.querySelectorAll('#paynoticeItemsBody tr');
  let sub = 0, tax = 0;
  rows.forEach(tr => {
    const price = Number(tr.querySelector('input[name$="[price]"]')?.value) || 0;
    const qty = Number(tr.querySelector('input[name$="[qty]"]')?.value) || 0;
    const taxLabel = tr.querySelector('select[name$="[tax]"]')?.value || '';
    const amount = price * qty;
    sub += amount;
    tax += paynoticeCalcTaxAt(amount, taxLabel);
    const amtCell = tr.querySelector('.paynotice-item-amount');
    if (amtCell) {
      amtCell.textContent = num(amount);
    }
  });
  const subEl = document.getElementById('pnSub');
  const taxEl = document.getElementById('pnTax');
  const totalEl = document.getElementById('pnTotal');
  if (subEl) subEl.textContent = yen(sub);
  if (taxEl) taxEl.textContent = yen(tax);
  if (totalEl) totalEl.textContent = yen(sub + tax);
}
function paynoticeItemAdd(){
  const table = document.getElementById('paynoticeItems');
  const tbody = document.getElementById('paynoticeItemsBody');
  if (!table || !tbody) {
    return;
  }
  const index = Number(table.dataset.nextIndex || tbody.children.length);
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="date" name="items[${index}][date]" value="${today()}" style="width:140px" oninput="paynoticeRowRecalc(this)"></td>
    <td><input type="text" name="items[${index}][item]" style="min-width:160px"></td>
    <td><input type="number" name="items[${index}][price]" style="width:110px;text-align:right" oninput="paynoticeRowRecalc(this)"></td>
    <td><input type="text" name="items[${index}][unit]" value="式" style="width:60px"></td>
    <td><input type="number" name="items[${index}][qty]" value="1" style="width:70px;text-align:right" oninput="paynoticeRowRecalc(this)"></td>
    <td><select name="items[${index}][tax]" style="width:110px" onchange="paynoticeRowRecalc(this)">
      <option value="非課税">非課税</option>
      <option value="8%">8%</option>
      <option value="8%軽減税率">8%軽減税率</option>
      <option value="10%" selected>10%</option>
    </select></td>
    <td class="num paynotice-item-amount" style="padding:4px 8px">0</td>
    <td><button type="button" class="icon-btn" onclick="paynoticeItemDel(this)">🗑</button></td>`;
  tbody.appendChild(tr);
  table.dataset.nextIndex = String(index + 1);
}
function paynoticeItemDel(btn){
  btn.closest('tr')?.remove();
  paynoticeRecalcAll();
}
/* ================= purchase (発注取引一覧アップロード) ================= */
function purchaseCreate(){
  fetch('/purchase/create', { headers: { Accept: 'text/html' } })
    .then(response => {
      if (!response.ok) {
        throw new Error('作成フォームの取得に失敗しました。');
      }
      return response.text();
    })
    .then(html => openModal(html))
    .catch(error => alert(error.message));
}
function purchaseEdit(id){
  fetch(`/purchase/${encodeURIComponent(id)}/edit`, { headers: { Accept: 'text/html' } })
    .then(response => {
      if (!response.ok) {
        throw new Error('編集フォームの取得に失敗しました。');
      }
      return response.text();
    })
    .then(html => openModal(html))
    .catch(error => alert(error.message));
}
function purchaseDelete(id){
  if (!confirm('この取引書類を削除しますか？')) {
    return;
  }
  const token = document.querySelector('meta[name="csrf-token"]')?.content;
  fetch(`/purchase/${encodeURIComponent(id)}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': token || '',
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('削除に失敗しました。');
      }
      return response.json();
    })
    .then(() => location.reload())
    .catch(error => alert(error.message));
}
/* ================= financial statements (財務三表) ================= */
function triText(n){
  n = Number(n) || 0;
  return n < 0 ? '△'+num(Math.abs(n)) : num(n);
}
function sumRowsIn(tbodyId){
  let total = 0;
  document.querySelectorAll('#'+tbodyId+' tr').forEach(tr => {
    total += Number(tr.querySelector('input[name$="[v]"]')?.value) || 0;
  });
  return total;
}
function addStatementRow(tbodyId, namePrefix, onInput, onDelete){
  const tbody = document.getElementById(tbodyId);
  if (!tbody) {
    return;
  }
  const index = Number(tbody.dataset.nextIndex || tbody.children.length);
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="text" name="${namePrefix}[${index}][name]"></td>
    <td style="width:180px"><input type="number" name="${namePrefix}[${index}][v]" value="0" style="text-align:right" oninput="${onInput}"></td>
    <td style="width:36px"><button type="button" class="icon-btn" onclick="${onDelete}(this)">🗑</button></td>`;
  tbody.appendChild(tr);
  tbody.dataset.nextIndex = String(index + 1);
}
function bsRowAdd(section){
  const map = { assets: 'bsAssetsBody', liabs: 'bsLiabsBody', equity: 'bsEquityBody' };
  addStatementRow(map[section], section, 'bsRecalcAll()', 'bsRowDel');
}
function bsRowDel(btn){
  btn.closest('tr')?.remove();
  bsRecalcAll();
}
function bsRecalcAll(){
  const assets = sumRowsIn('bsAssetsBody');
  const liabs = sumRowsIn('bsLiabsBody');
  const equity = sumRowsIn('bsEquityBody');
  const liabsEquity = liabs + equity;
  const assetsEl = document.getElementById('bsAssetsTotal');
  const liabsEl = document.getElementById('bsLiabsTotal');
  const equityEl = document.getElementById('bsEquityTotal');
  const liabsEquityEl = document.getElementById('bsLiabsEquityTotal');
  const msgEl = document.getElementById('bsBalanceMsg');
  if (assetsEl) assetsEl.textContent = num(assets);
  if (liabsEl) liabsEl.textContent = num(liabs);
  if (equityEl) equityEl.textContent = num(equity);
  if (liabsEquityEl) liabsEquityEl.textContent = num(liabsEquity);
  if (msgEl) {
    if (assets === liabsEquity) {
      msgEl.style.color = 'var(--ok)';
      msgEl.textContent = '✓ 貸借一致しています。';
    } else {
      msgEl.style.color = 'var(--danger)';
      msgEl.textContent = `⚠ 資産合計(${num(assets)})と負債・純資産合計(${num(liabsEquity)})が一致していません。`;
    }
  }
}
function plRowAdd(){
  const tbody = document.getElementById('plItemsBody');
  if (!tbody) {
    return;
  }
  const index = Number(tbody.dataset.nextIndex || tbody.children.length);
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="text" name="rows[${index}][name]"></td>
    <td><select name="rows[${index}][type]" onchange="plRecalcAll()">
      <option value="収益">収益</option>
      <option value="費用" selected>費用</option>
    </select></td>
    <td><input type="number" name="rows[${index}][v]" value="0" style="text-align:right" oninput="plRecalcAll()"></td>
    <td><button type="button" class="icon-btn" onclick="plRowDel(this)">🗑</button></td>`;
  tbody.appendChild(tr);
  tbody.dataset.nextIndex = String(index + 1);
}
function plRowDel(btn){
  btn.closest('tr')?.remove();
  plRecalcAll();
}
function plRecalcAll(){
  let revenue = 0, expense = 0;
  document.querySelectorAll('#plItemsBody tr').forEach(tr => {
    const v = Number(tr.querySelector('input[name$="[v]"]')?.value) || 0;
    const type = tr.querySelector('select[name$="[type]"]')?.value;
    if (type === '収益') revenue += v; else expense += v;
  });
  const revenueEl = document.getElementById('plRevenueTotal');
  const expenseEl = document.getElementById('plExpenseTotal');
  const profitEl = document.getElementById('plProfitTotal');
  if (revenueEl) revenueEl.textContent = num(revenue);
  if (expenseEl) expenseEl.textContent = num(expense);
  if (profitEl) profitEl.textContent = triText(revenue - expense);
}
function cfRowAdd(section){
  const map = { operating: 'cfOperatingBody', investing: 'cfInvestingBody', financing: 'cfFinancingBody' };
  addStatementRow(map[section], section, 'cfRecalcAll()', 'cfRowDel');
}
function cfRowDel(btn){
  btn.closest('tr')?.remove();
  cfRecalcAll();
}
function cfRecalcAll(){
  const operating = sumRowsIn('cfOperatingBody');
  const investing = sumRowsIn('cfInvestingBody');
  const financing = sumRowsIn('cfFinancingBody');
  const delta = operating + investing + financing;
  const beginningBalance = Number(document.getElementById('cfBeginningBalance')?.value) || 0;
  const ending = beginningBalance + delta;
  const operatingEl = document.getElementById('cfOperatingTotal');
  const investingEl = document.getElementById('cfInvestingTotal');
  const financingEl = document.getElementById('cfFinancingTotal');
  const deltaEl = document.getElementById('cfDeltaTotal');
  const endingEl = document.getElementById('cfEndingTotal');
  if (operatingEl) operatingEl.textContent = triText(operating);
  if (investingEl) investingEl.textContent = triText(investing);
  if (financingEl) financingEl.textContent = triText(financing);
  if (deltaEl) deltaEl.textContent = triText(delta);
  if (endingEl) endingEl.textContent = triText(ending);
}
/* ================= 総勘定元帳 (仕訳帳) ================= */
function ledgerRowAdd(){
  const tbody = document.getElementById('lgTableBody');
  if (!tbody) {
    return;
  }
  const index = Number(tbody.dataset.nextIndex || tbody.children.length);
  const dateVal = document.getElementById('lg_date')?.value;
  const year = dateVal ? dateVal.slice(0, 4) : String(new Date().getFullYear());
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="text" name="rows[${index}][no]" style="width:80px"></td>
    <td><input type="text" name="rows[${index}][year]" value="${year}" style="width:60px;text-align:center"></td>
    <td><input type="text" name="rows[${index}][m]" style="width:44px;text-align:center"></td>
    <td><input type="text" name="rows[${index}][d]" style="width:44px;text-align:center"></td>
    <td><input type="text" name="rows[${index}][dr_acct]" list="acctList" style="width:110px" oninput="ledgerRowSync(this)"></td>
    <td><input type="number" name="rows[${index}][dr_amt]" class="num" style="width:100px;text-align:right" oninput="ledgerRowSync(this)"></td>
    <td><input type="text" name="rows[${index}][cr_acct]" list="acctList" style="width:110px" oninput="ledgerRowSync(this)"></td>
    <td><input type="number" name="rows[${index}][cr_amt]" class="num" style="width:100px;text-align:right" oninput="ledgerRowSync(this)"></td>
    <td class="muted lg-acct-pair" style="padding:4px 8px"></td>
    <td><input type="text" name="rows[${index}][note]" style="width:140px"></td>
    <td><input type="text" name="rows[${index}][page]" style="width:44px;text-align:center"></td>
    <td class="num muted lg-dr-mirror" style="padding:4px 8px"></td>
    <td class="num muted lg-cr-mirror" style="padding:4px 8px"></td>
    <td class="num muted" style="padding:4px 8px"></td>
    <td><button type="button" class="icon-btn" onclick="ledgerRowDel(this)">🗑</button></td>`;
  tbody.appendChild(tr);
  tbody.dataset.nextIndex = String(index + 1);
}
function ledgerRowDel(btn){
  btn.closest('tr')?.remove();
  ledgerRecalcAll();
}
function ledgerRowSync(el){
  const tr = el.closest('tr');
  if (!tr) {
    return;
  }
  const drAcct = tr.querySelector('input[name$="[dr_acct]"]')?.value || '';
  const crAcct = tr.querySelector('input[name$="[cr_acct]"]')?.value || '';
  const drAmt = Number(tr.querySelector('input[name$="[dr_amt]"]')?.value) || 0;
  const crAmt = Number(tr.querySelector('input[name$="[cr_amt]"]')?.value) || 0;
  const pairEl = tr.querySelector('.lg-acct-pair');
  const drMirrorEl = tr.querySelector('.lg-dr-mirror');
  const crMirrorEl = tr.querySelector('.lg-cr-mirror');
  if (pairEl) pairEl.textContent = [drAcct, crAcct].filter(Boolean).join('／');
  if (drMirrorEl) drMirrorEl.textContent = drAmt ? num(drAmt) : '';
  if (crMirrorEl) crMirrorEl.textContent = crAmt ? num(crAmt) : '';
  ledgerRecalcAll();
}
function ledgerRecalcAll(){
  let dr = 0, cr = 0;
  document.querySelectorAll('#lgTableBody tr').forEach(tr => {
    dr += Number(tr.querySelector('input[name$="[dr_amt]"]')?.value) || 0;
    cr += Number(tr.querySelector('input[name$="[cr_amt]"]')?.value) || 0;
  });
  const drEl = document.getElementById('lgDrTotal');
  const crEl = document.getElementById('lgCrTotal');
  const drEl2 = document.getElementById('lgDrTotal2');
  const crEl2 = document.getElementById('lgCrTotal2');
  if (drEl) drEl.textContent = num(dr);
  if (crEl) crEl.textContent = num(cr);
  if (drEl2) drEl2.textContent = num(dr);
  if (crEl2) crEl2.textContent = num(cr);
}
function purchaseAmountInput(el){
  const taxEl = document.getElementById('pf_tax');
  if (taxEl) {
    taxEl.value = Math.floor(((Number(el.value) || 0) * 10) / 100);
  }
}
window.fillAddressFromZip = fillAddressFromZip;
window.openModal = openModal;
window.closeModal = closeModal;
window.customerEdit = customerEdit;
window.customerDelete = customerDelete;
window.saleCreate = saleCreate;
window.saleEdit = saleEdit;
window.saleDelete = saleDelete;
window.saleInvoiceView = saleInvoiceView;
window.saleInvoiceIssue = saleInvoiceIssue;
window.saleInvoicePrint = saleInvoicePrint;
window.saleRowRecalc = saleRowRecalc;
window.saleRecalcAll = saleRecalcAll;
window.saleItemAdd = saleItemAdd;
window.saleItemDel = saleItemDel;
window.purchaseCreate = purchaseCreate;
window.purchaseEdit = purchaseEdit;
window.purchaseDelete = purchaseDelete;
window.purchaseAmountInput = purchaseAmountInput;
window.paynoticeCreate = paynoticeCreate;
window.paynoticeEdit = paynoticeEdit;
window.paynoticeView = paynoticeView;
window.paynoticeDelete = paynoticeDelete;
window.paynoticePrint = paynoticePrint;
window.paynoticeRowRecalc = paynoticeRowRecalc;
window.paynoticeRecalcAll = paynoticeRecalcAll;
window.paynoticeItemAdd = paynoticeItemAdd;
window.paynoticeItemDel = paynoticeItemDel;
window.bsRowAdd = bsRowAdd;
window.bsRowDel = bsRowDel;
window.bsRecalcAll = bsRecalcAll;
window.plRowAdd = plRowAdd;
window.plRowDel = plRowDel;
window.plRecalcAll = plRecalcAll;
window.cfRowAdd = cfRowAdd;
window.cfRowDel = cfRowDel;
window.cfRecalcAll = cfRecalcAll;
window.ledgerRowAdd = ledgerRowAdd;
window.ledgerRowDel = ledgerRowDel;
window.ledgerRowSync = ledgerRowSync;
window.ledgerRecalcAll = ledgerRecalcAll;
$("#modalBg") && document.addEventListener("click",e=>{if(e.target.id==="modalBg")closeModal();});
/* ================= auth ================= */
function doLogin(){
  const em=$("#loginEmail").value.trim(), pw=$("#loginPw").value;
  if(em===db.profile.email && pw===db.profile.pw){
    sessionStorage.setItem("bf_login","1");
    showApp();
  }else{
    $("#loginErr").textContent="メールアドレスまたはパスワードが正しくありません。";
  }
}
function logout(){sessionStorage.removeItem("bf_login");location.reload();}
function showApp(){
  $("#loginView").style.display="none";$("#app").style.display="block";
  $("#userLabel").textContent=db.profile.name+"("+db.profile.email+")";
  show("home");
}
/* ================= clock ================= */
function tickClock(){
  const el=document.getElementById("clockTime");if(!el)return;
  const d=new Date(),p=x=>String(x).padStart(2,"0");
  el.textContent=`${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`;
  document.getElementById("clockDate").textContent=`${d.getFullYear()}年${d.getMonth()+1}月${d.getDate()}日(${"日月火水木金土"[d.getDay()]})`;
}
setInterval(tickClock,1000);tickClock();


