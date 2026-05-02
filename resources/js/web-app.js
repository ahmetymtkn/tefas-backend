import './bootstrap';

const API_URL = '/api';

function getToken(){return localStorage.getItem('auth_token')||null;}
function getUser(){const u=localStorage.getItem('user');return u?JSON.parse(u):null;}

async function apiRequest(path, options={}){
  const headers={'Accept':'application/json','Content-Type':'application/json',...(options.headers||{})};
  const token=getToken();
  if(token){headers['Authorization']='Bearer '+token;}
  const res=await fetch(API_URL+path,{...options,headers});
  if(res.status===401){localStorage.removeItem('auth_token');localStorage.removeItem('user');if(!location.pathname.startsWith('/login')){location.href='/login';}throw new Error('Yetkisiz');}
  const text=await res.text();
  try{return JSON.parse(text);}catch{return {raw:text};}
}

function syncNav(){const user=getUser();document.querySelectorAll('.nav-auth-only').forEach(el=>{el.classList.toggle('hidden',!user);});document.querySelectorAll('.nav-guest-only').forEach(el=>{el.classList.toggle('hidden',!!user);});}

async function handleLogout(){try{await apiRequest('/logout',{method:'POST'});}catch(e){}finally{localStorage.removeItem('auth_token');localStorage.removeItem('user');location.href='/login';}}

async function handleLoginPage(){const form=document.getElementById('loginForm');if(!form)return;const errorEl=document.getElementById('loginError');form.addEventListener('submit',async e=>{e.preventDefault();if(errorEl)errorEl.style.display='none';const data=Object.fromEntries(new FormData(form).entries());try{const res=await apiRequest('/login',{method:'POST',body:JSON.stringify(data)});if(res.success&&res.token){localStorage.setItem('auth_token',res.token);if(res.user)localStorage.setItem('user',JSON.stringify(res.user));location.href='/';}else{if(errorEl){errorEl.textContent=res.message||'Giriş başarısız';errorEl.style.display='block';}}}catch(err){if(errorEl){errorEl.textContent='Giriş sırasında hata oluştu';errorEl.style.display='block';}}});}

async function handleRegisterPage(){const form=document.getElementById('registerForm');if(!form)return;const errEl=document.getElementById('registerError');const okEl=document.getElementById('registerSuccess');form.addEventListener('submit',async e=>{e.preventDefault();if(errEl)errEl.style.display='none';if(okEl)okEl.style.display='none';const data=Object.fromEntries(new FormData(form).entries());try{const res=await apiRequest('/register',{method:'POST',body:JSON.stringify(data)});if(res.success){if(okEl){okEl.textContent='Kayıt başarılı, lütfen giriş yapın.';okEl.style.display='block';}setTimeout(()=>location.href='/login',800);}else{if(errEl){errEl.textContent=typeof res.message==='object'?JSON.stringify(res.message):res.message||'Kayıt başarısız';errEl.style.display='block';}}}catch(err){if(errEl){errEl.textContent='Kayıt sırasında hata oluştu';errEl.style.display='block';}}});}

async function handleHomePage(){const root=document.getElementById('homePage');if(!root)return;const periodSel=document.getElementById('homePeriod');const catEl=document.getElementById('homeCategories');const fundEl=document.getElementById('homeFunds');async function loadCategories(){if(!periodSel||!catEl)return;catEl.innerHTML='Yükleniyor...';fundEl.innerHTML='';const pid=periodSel.value;try{const res=await apiRequest(`/tefas/best-category-rates/period/${pid}`);if(!res.success){catEl.innerHTML='Veri bulunamadı.';return;}const data=res.data||[];if(!data.length){catEl.innerHTML='Bu dönem için veri yok.';return;}const max=Math.max(...data.map((d)=>Number(d.rate)||0),1);catEl.innerHTML=data.map((d,idx)=>{const rate=Number(d.rate)||0;const width=25+(rate/max)*65;const name=(d.category&&d.category.name)||'Kategori';return `<div class="mb-2 cursor-pointer" data-cat="${d.category_id}"><div class="flex items-center justify-between mb-1"><div class="text-sm font-medium">${name}</div><div class="text-sm text-muted">%${rate.toFixed(2)}</div></div><div class="h-6 rounded bg-slate-200 overflow-hidden"><div style="width:${width}%;background:#38bdf8" class="h-full"></div></div></div>`;}).join('');Array.from(catEl.querySelectorAll('[data-cat]')).forEach(el=>{el.addEventListener('click',()=>{const catId=el.getAttribute('data-cat');if(catId)loadFunds(catId);});});}catch(e){catEl.innerHTML='Veri yüklenirken hata oluştu.';}}

async function loadFunds(categoryId){if(!fundEl)return;fundEl.innerHTML='Yükleniyor...';const pid=document.getElementById('homePeriod').value;try{const res=await apiRequest(`/tefas/best-fund-rates/category/${categoryId}/period/${pid}`);if(!res.success){fundEl.innerHTML='Fon verisi bulunamadı.';return;}const data=res.data||[];if(!data.length){fundEl.innerHTML='Bu kategori için fon bulunamadı.';return;}fundEl.innerHTML='<h2 class="text-lg font-semibold mb-2">Seçilen kategori için en çok kazandıran fonlar</h2>'+data.map((f)=>{const rate=Number(f.rate)||0;const name=(f.fund&&f.fund.name)||'';const code=f.fund_code||(f.fund&&f.fund.code)||'';return `<div class="flex items-center justify-between border-b border-slate-200 py-1 text-sm cursor-pointer" data-code="${code}"><div><div class="font-medium">${code}</div><div class="text-muted">${name}</div></div><div class="badge badge-soft">%${rate.toFixed(2)}</div></div>`;}).join('');Array.from(fundEl.querySelectorAll('[data-code]')).forEach(el=>{el.addEventListener('click',()=>{const code=el.getAttribute('data-code');if(code)location.href=`/funds/${code}`;});});}catch(e){fundEl.innerHTML='Fon verileri yüklenirken hata oluştu.';}}

if(periodSel){periodSel.addEventListener('change',loadCategories);loadCategories();}}

async function loadFavoritesSet(){try{const res=await apiRequest('/favorites');if(res.success&&Array.isArray(res.data)){return new Set(res.data.map(f=>f.fund_code));}}catch(e){}return new Set();}

async function handleFundsPage(){const root=document.getElementById('fundsPage');if(!root)return;const search=document.getElementById('fundSearch');const tbody=document.getElementById('fundsTableBody');const hint=document.getElementById('fundsHint');let funds=[];let favs=new Set();async function load(){if(hint)hint.textContent='Yükleniyor...';if(tbody)tbody.innerHTML='';try{const res=await apiRequest('/tefas/funds');funds=res.data||[];favs=await loadFavoritesSet();render();}catch(e){if(hint)hint.textContent='Veriler yüklenirken hata oluştu.';}}

function render(){if(!tbody)return;const q=(search&&search.value||'').toLowerCase();const list=funds.filter(f=>{if(!q)return true;if(q.length<=3)return f.code.toLowerCase().includes(q);return f.name.toLowerCase().includes(q);});tbody.innerHTML=list.map(f=>{const fav=favs.has(f.code);return `<tr data-code="${f.code}" class="cursor-pointer hover:bg-slate-50 transition-colors"><td><a href="/funds/${f.code}" class="font-bold text-slate-700">${f.code}</a></td><td>${f.name}</td><td>${f.category_name||'–'}</td><td class="text-center"><span class="star cursor-pointer text-lg hover:scale-125 inline-block transition-transform" data-fav="${f.code}">${fav?'⭐':'☆'}</span></td></tr>`;}).join('');if(hint)hint.textContent=`${list.length} fon listelendi.`;
tbody.querySelectorAll('tr[data-code]').forEach(el => {
    el.addEventListener('click', (ev) => {
        // Eğer yıldıza tıklandıysa fon detayına gitmesini engelle
        if(ev.target.closest('[data-fav]')) return;
        const code = el.getAttribute('data-code');
        if(code) location.href = `/funds/${code}`;
    });
});
tbody.querySelectorAll('[data-fav]').forEach(el=>{el.addEventListener('click',async ev=>{ev.stopPropagation();const code=el.getAttribute('data-fav');if(!code)return;const isFav=favs.has(code);try{if(isFav){const res=await apiRequest(`/favorites/${code}`,{method:'DELETE'});if(res.success)favs.delete(code);}else{const res=await apiRequest('/favorites/add',{method:'POST',body:JSON.stringify({fund_code:code})});if(res.success)favs.add(code);}render();}catch(e){alert('İşlem sırasında hata oluştu');}});});}

if(search){search.addEventListener('input',()=>render());}load();}

async function handleHistoryPage(){const root=document.getElementById('historyPage');if(!root)return;const dateInput=document.getElementById('historyDate');const reloadBtn=document.getElementById('historyReload');const tbody=document.getElementById('historyTableBody');const hint=document.getElementById('historyHint');const today=new Date().toISOString().split('T')[0];if(dateInput)dateInput.value=today;async function load(){if(!dateInput||!tbody)return;const d=dateInput.value||today;if(hint)hint.textContent='Yükleniyor...';tbody.innerHTML='';try{const res=await apiRequest(`/tefas/fund-details?date=${d}`);const data=res.data||[];if(!data.length){if(hint)hint.textContent=`${d} tarihine ait veri bulunamadı.`;return;}tbody.innerHTML=data.map(r=>{const fiyat=r.fiyat!=null?`${r.fiyat} TL`:'–';return `<tr data-code="${r.code}" data-date="${d}"><td class="underline cursor-pointer">${r.code}</td><td>${r.name||'–'}</td><td>${r.category_name||'–'}</td><td>${fiyat}</td></tr>`;}).join('');if(hint)hint.textContent=`${data.length} kayıt yüklendi.`;tbody.querySelectorAll('tr[data-code]').forEach(el=>{el.addEventListener('click',()=>{const code=el.getAttribute('data-code');const dt=el.getAttribute('data-date');if(code)location.href=`/funds/${code}?date=${dt}`;});});}catch(e){if(hint)hint.textContent='Veriler yüklenirken hata oluştu.';}}

if(reloadBtn)reloadBtn.addEventListener('click',load);load();}

async function handleProfilePage(){const root=document.getElementById('profilePage');if(!root)return;const nameEl=document.getElementById('profileName');const emailEl=document.getElementById('profileEmail');const idEl=document.getElementById('profileId');const favInfo=document.getElementById('profileFavInfo');const favBody=document.getElementById('profileFavBody');try{const u=await apiRequest('/user');if(nameEl)nameEl.textContent=u.name||'';if(emailEl)emailEl.textContent=u.email||'';if(idEl)idEl.textContent=u.id||'';}catch(e){location.href='/login';return;}try{const res=await apiRequest('/favorites');if(res.success&&Array.isArray(res.data)){const list=res.data; if(favInfo)favInfo.textContent=`${list.length} favori fonunuz var.`;if(favBody)favBody.innerHTML=list.map(f=>`<tr data-code="${f.fund_code}"><td><a href="/funds/${f.fund_code}" class="underline">${f.fund_code}</a></td><td>${f.name||'–'}</td><td>${f.category_name||'–'}</td><td><button type="button" class="text-xs text-red-600" data-remove="${f.fund_code}">Kaldır</button></td></tr>`).join('');favBody.querySelectorAll('[data-remove]').forEach(el=>{el.addEventListener('click',async ev=>{ev.preventDefault();const code=el.getAttribute('data-remove');if(!code)return;if(!confirm(`${code} fonunu favorilerden çıkarmak istiyor musunuz?`))return;try{const r=await apiRequest(`/favorites/${code}`,{method:'DELETE'});if(r.success)el.closest('tr').remove();}catch(e){alert('Silme sırasında hata oluştu');}});});}}
catch(e){if(favInfo)favInfo.textContent='Favoriler yüklenirken hata oluştu.';}

  // Profilde trend analizini de aynı token sistemiyle yükle
  await loadTrendAnalysisProfile();}

async function loadTrendAnalysisProfile(){
  const root=document.getElementById('profilePage');
  if(!root)return;

  const loadingEl=document.getElementById('trendLoading');
  const gridEl=document.getElementById('trendGrid');
  const errorEl=document.getElementById('trendError');
  const dateInfoEl=document.getElementById('trendDateInfo');
  const container=document.getElementById('trendContainer');
  const totalEl=document.getElementById('trendTotal');

  const filtersEl=document.getElementById('trendFilters');
  const categoryFilter=document.getElementById('trendCategoryFilter');
  const streakFilter=document.getElementById('trendStreakFilter');
  const sortFilter=document.getElementById('trendSortFilter');

  if(!loadingEl||!gridEl||!errorEl||!container||!dateInfoEl)return;

  // başlangıç durumu
  loadingEl.classList.remove('hidden');
  gridEl.classList.add('hidden');
  errorEl.classList.add('hidden');
  if(filtersEl) filtersEl.classList.add('hidden');

  let allTrends = [];

  function renderTrends() {
    const catVal = categoryFilter ? categoryFilter.value : '';
    const streakVal = streakFilter ? parseInt(streakFilter.value) : 0;
    const sortVal = sortFilter ? sortFilter.value : 'desc';

    let filtered = allTrends.filter(t => {
      if (catVal && t.category_id != catVal) return false;
      if (streakVal > 0 && t.streak_days < streakVal) return false;
      return true;
    });

    filtered.sort((a,b) => {
       return sortVal === 'desc' 
          ? b.streak_days - a.streak_days 
          : a.streak_days - b.streak_days;
    });

    container.innerHTML='';

    if(filtered.length === 0) {
       container.innerHTML='<tr><td colspan="4" class="text-center py-6 text-muted">Seçilen kriterlere uygun fon bulunamadı.</td></tr>';
       return;
    }

    filtered.forEach(trend=>{
      const isUp=trend.streak_days>0;
      const streakDays=Math.abs(trend.streak_days);
      const changePercent=Number(trend.change_percent||0).toFixed(2);
      const lastPrice=Number(trend.last_price||0).toFixed(2);
      const rowClass=isUp?'trend-row-up':'trend-row-down';
      const icon=isUp?'📈':'📉';
      
      const tr=document.createElement('tr');
      tr.className=rowClass;
      tr.style.cursor='pointer';
      tr.addEventListener('click',()=>{if(trend.fund_code)location.href=`/funds/${trend.fund_code}`;});
      
      const categoryBadge = trend.category_name ? `<br><span class="text-xs font-normal opacity-75">${trend.category_name}</span>` : '';
      tr.innerHTML=`<td><span class="trend-icon">${icon}</span><strong>${trend.fund_code}</strong>${categoryBadge}</td><td>${streakDays} Gün</td><td>${isUp?'+':''}${changePercent}%</td><td>${lastPrice} ₺</td>`;
      container.appendChild(tr);
    });
  }

  try{
    const res=await apiRequest('/tefas/trend-analysis');

    if(!res.success||!Array.isArray(res.data)||!res.data.length){
      if(errorEl){
        loadingEl.classList.add('hidden');
        gridEl.classList.add('hidden');
        errorEl.classList.remove('hidden');
        errorEl.innerHTML='<p class="text-muted">Trend analiz verisi bulunamadı</p>';
      }
      return;
    }

    const {analysis_date,data}=res;
    allTrends = data;

    if (categoryFilter) {
       const categories = new Map();
       data.forEach(t => {
          if (t.category_id && t.category_name) {
             categories.set(t.category_id, t.category_name);
          }
       });
       categoryFilter.innerHTML = '<option value="">Tüm Kategoriler</option>';
       [...categories.entries()]
         .sort((a,b) => a[1].localeCompare(b[1]))
         .forEach(([id, name]) => {
            categoryFilter.innerHTML += `<option value="${id}">${name}</option>`;
         });
       
       categoryFilter.addEventListener('change', renderTrends);
    }
    if (streakFilter) streakFilter.addEventListener('change', renderTrends);
    if (sortFilter) sortFilter.addEventListener('change', renderTrends);

    // tarih bilgisi
    if(dateInfoEl)dateInfoEl.textContent=`Güncelleme: ${analysis_date}`;

    // tabloyu göster
    loadingEl.classList.add('hidden');
    errorEl.classList.add('hidden');
    if(filtersEl) filtersEl.classList.remove('hidden');
    gridEl.classList.remove('hidden');

    renderTrends();

    if(totalEl)totalEl.style.display='none';
  }catch(e){
    console.error('Trend analiz yüklenirken hata oluştu',e);
    if(loadingEl)loadingEl.classList.add('hidden');
    if(gridEl)gridEl.classList.add('hidden');
    if(errorEl){
      errorEl.classList.remove('hidden');
      errorEl.innerHTML='<p class="text-muted">Trend verileri yüklenemedi</p>';
    }
  }
}

async function handleFundDetailPage(){const root=document.getElementById('fundDetailPage');if(!root)return;const code=root.getAttribute('data-code');const nameEl=document.getElementById('fdName');const catEl=document.getElementById('fdCategory');const infoRows=document.getElementById('fdInfoRows');const statsRows=document.getElementById('fdStatsRows');const compBody=document.getElementById('fdComparisonBody');const periodSel=document.getElementById('fdPeriod');const dateInput=document.getElementById('fdDate');const dateReload=document.getElementById('fdDateReload');const dateRows=document.getElementById('fdDateRows');const urlParams=new URLSearchParams(window.location.search);const initialDate=urlParams.get('date');const today=new Date().toISOString().split('T')[0];if(dateInput)dateInput.value=initialDate||today;async function loadInfo(){try{const [fundRes,statsRes]=await Promise.all([apiRequest(`/tefas/funds/${code}`),apiRequest(`/tefas/fund-stats/${code}`)]);if(fundRes.success&&fundRes.data){const f=fundRes.data;if(nameEl)nameEl.textContent=f.name||code;if(catEl)catEl.textContent=(f.category&&f.category.name)||'';if(infoRows){infoRows.innerHTML='';const rows=[['ISIN Kodu',f.isin_code],['Platform Durumu',f.platform_status],['İşlem Başlama Saati',f.start_time],['İşlem Bitiş Saati',f.end_time],['Alış Valörü',f.buy_valor],['Satış Valörü',f.sell_valor],['Min. Alış',f.min_buy_amount],['Min. Satış',f.min_sell_amount],['Giriş Komisyonu',f.entry_commission],['Çıkış Komisyonu',f.exit_commission],['Faiz İçeriği',f.interest_content],['Risk Değeri',f.risk_value]];rows.forEach(([label,val])=>{if(val===null||val===undefined||val==='')return;const div=document.createElement('div');div.className='flex items-center justify-between border-b border-slate-100 py-1';div.innerHTML=`<span class="text-sm text-slate-600">${label}</span><span class="text-sm font-medium">${val}</span>`;infoRows.appendChild(div);});}}
if(statsRes.success&&statsRes.data&&statsRows){const s=statsRes.data;statsRows.innerHTML='';const rows=[['Fon Fiyat (TL)',s.last_price,true],['Günlük Getiri (%)',s.daily_return,true],['Pay (Adet)',s.shares_outstanding,false],['Fon Toplam Değer (TL)',s.total_value,false],['Kategori',s.category,false],['Kategori Sıralaması',s.category_rank,false],['Yatırımcı Sayısı',s.investor_count,false],['Pazar Payı (%)',s.market_share,false],['Son 1 Ay Getirisi (%)',s.return_1m,true],['Son 3 Ay Getirisi (%)',s.return_3m,true],['Son 6 Ay Getirisi (%)',s.return_6m,true],['Son 1 Yıl Getirisi (%)',s.return_1y,true]];rows.forEach(([label,val,hl])=>{if(val===null||val===undefined||val==='')return;const div=document.createElement('div');div.className='flex items-center justify-between border-b border-slate-100 py-1';div.innerHTML=`<span class="text-sm text-slate-600">${label}</span><span class="text-sm font-medium ${hl?'text-sky-600':''}">${val}</span>`;statsRows.appendChild(div);});}}
catch(e){}
}

async function loadComparison(){if(!periodSel||!compBody)return;compBody.innerHTML='Yükleniyor...';const pid=periodSel.value;try{const res=await apiRequest(`/tefas/comparison/${code}/period/${pid}`);if(!res.success||!res.data){compBody.innerHTML='Bu dönem için karşılaştırma verisi bulunamadı.';return;}const d=res.data;let names=[],values=[];try{names=JSON.parse(d.comparison_names||'[]');values=JSON.parse(d.comparison_values||'[]');}catch{names=[];values=[];}if(!names.length){compBody.innerHTML='Veri yok.';return;}const max=Math.max(...values.map(v=>Math.abs(Number(v)||0)),1);compBody.innerHTML=names.map((n,idx)=>{const val=Number(values[idx])||0;const width=25+(Math.abs(val)/max)*65;const isFund=String(n).toUpperCase()===String(code).toUpperCase();return `<div class="flex items-center justify-between mb-1"><div class="flex-1 mr-2"><div class="text-xs mb-0.5 ${isFund?'font-semibold':''}">${n}</div><div class="h-4 rounded bg-slate-200 overflow-hidden"><div style="width:${width}%" class="h-full ${isFund?'bg-sky-500':'bg-sky-300'}"></div></div></div><div class="text-xs ${val<0?'text-red-600':'text-slate-700'}">%${val.toFixed(2)}</div></div>`;}).join('');}catch(e){compBody.innerHTML='Veri yüklenirken hata oluştu.';}}

async function loadDate(){if(!dateInput||!dateRows)return;const d=dateInput.value||today;dateRows.innerHTML='Yükleniyor...';try{const res=await apiRequest(`/tefas/fund-details/${code}?date=${d}`);if(!res.success||!res.data){dateRows.innerHTML=`${d} tarihine ait veri bulunamadı.`;return;}const r=res.data;const fields=['FIYAT','BilFiyat','BORSABULTENFIYAT','PORTFOYBUYUKLUK','TEDPAYSAYISI','KISISAYISI'];const labels={FIYAT:'Birim Fiyat (TL)',BilFiyat:'Bilgi Fiyatı',BORSABULTENFIYAT:'Borsa Bülten Fiyatı',PORTFOYBUYUKLUK:'Portföy Büyüklüğü (TL)',TEDPAYSAYISI:'Tedavüldeki Pay Sayısı',KISISAYISI:'Katılımcı Sayısı'};dateRows.innerHTML='';fields.forEach(k=>{const v=r[k];if(v===null||v===undefined||v===''||v==='0.0000'||v==='0.00')return;const div=document.createElement('div');div.className='flex items-center justify-between border-b border-slate-100 py-1';div.innerHTML=`<span class="text-sm text-slate-600">${labels[k]||k}</span><span class="text-sm font-medium">${v}</span>`;dateRows.appendChild(div);});}catch(e){dateRows.innerHTML='Veri yüklenirken hata oluştu.';}}

if(periodSel)periodSel.addEventListener('change',loadComparison);if(dateReload)dateReload.addEventListener('click',loadDate);loadInfo();loadComparison();loadDate();}

window.addEventListener('DOMContentLoaded',()=>{syncNav();const logoutBtn=document.getElementById('logoutButton');if(logoutBtn)logoutBtn.addEventListener('click',handleLogout);handleLoginPage();handleRegisterPage();handleHomePage();handleFundsPage();handleHistoryPage();handleProfilePage();handleFundDetailPage();});
