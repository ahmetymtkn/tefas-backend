@extends('layouts.app')

@section('title', 'Tarihsel Veriler')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Tarihsel Veriler</h2>
    <p class="text-gray-500 mt-1">Geçmişteki belirli bir tarihe ait fon fiyatlarını ve portföy durumlarını inceleyin.</p>
</div>

<!-- Filtre & Kontrol Alanı -->
<div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col md:flex-row gap-4 justify-between items-end md:items-center relative z-10">
    <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto items-end sm:items-center">
        <!-- Tarih Seçici -->
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Tarih</label>
            <div class="relative">
                <input type="date" id="historyDateInput" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl focus:ring-brand-teal focus:border-brand-teal block w-full pl-4 pr-4 py-2.5 outline-none transition-colors cursor-pointer" />
            </div>
        </div>
        
        <!-- Yükle Butonu -->
        <button id="historyLoadBtn" class="w-full sm:w-auto bg-brand-teal hover:bg-teal-500 text-white font-bold py-2.5 px-6 rounded-xl shadow-md shadow-brand-teal/30 transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            Sorgula
        </button>
    </div>

    <!-- Arama Kutusu -->
    <div class="w-full md:w-80 relative">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Hızlı Ara (Filtrele)</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" id="historySearchInput" placeholder="Fon Kodu veya Adı ile arayın..." class="bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-brand-teal focus:border-brand-teal block w-full pl-10 pr-3 py-2.5 shadow-sm font-medium outline-none transition-colors disabled:bg-gray-50 disabled:cursor-not-allowed" disabled>
        </div>
    </div>
</div>

<!-- Sonuçlar / Tablo Alanı -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-[65vh] min-h-[500px]">
    
    <!-- Tablo Header / Stats -->
    <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center shrink-0">
        <div>
            <h3 class="font-bold text-gray-800" id="tableTitle">Sonuçlar Bekleniyor</h3>
            <p class="text-xs text-gray-500 mt-0.5" id="tableSubTitle">Lütfen bir tarih seçip sorgulama yapın.</p>
        </div>
        <div class="text-sm font-semibold text-brand-teal bg-brand-teal/10 px-3 py-1 rounded-lg" id="resultCount">0 Fon</div>
    </div>

    <!-- Loading Alanı -->
    <div id="loadingArea" class="hidden flex-1 flex-col items-center justify-center bg-white z-10">
        <div class="relative w-16 h-16 mb-4">
            <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-brand-teal border-t-transparent animate-spin"></div>
        </div>
        <span class="text-sm font-bold text-gray-500 animate-pulse">TEFAS kayıtları çekiliyor... Lütfen bekleyin.</span>
    </div>

    <!-- Tablo İçeriği -->
    <div class="flex-1 overflow-y-auto relative custom-scrollbar bg-white">
        <table class="w-full text-left border-collapse" id="historyTable">
            <thead class="sticky top-0 bg-white/95 backdrop-blur-sm shadow-[0_1px_2px_rgba(0,0,0,0.05)] z-20">
                <tr>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Fon Kodu</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Fon Adı</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Fiyat (TL)</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">İşlem</th>
                </tr>
            </thead>
            <tbody id="historyTableBody" class="divide-y divide-gray-50">
                <tr>
                    <td colspan="5" class="px-6 py-24 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-gray-400 font-medium">Henüz bir sorgulama yapılmadı. Tarih seçip sorgula butonuna basın.</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Tablo scroll için özel modern tasarım */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
        border: 2px solid white; /* padding efekti */
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('auth_token');
    if(!token) {
        window.location.href = '/login';
        return;
    }

    const dateInput = document.getElementById('historyDateInput');
    const loadBtn = document.getElementById('historyLoadBtn');
    const searchInput = document.getElementById('historySearchInput');
    
    const tableBody = document.getElementById('historyTableBody');
    const loadingArea = document.getElementById('loadingArea');
    const tableTitle = document.getElementById('tableTitle');
    const tableSubTitle = document.getElementById('tableSubTitle');
    const resultCount = document.getElementById('resultCount');
    
    let currentData = [];

    // Tarihi Bugüne Ayarla
    const today = new Date();
    // Haftasonunu kontrol edip Cuma gününü seçmek isterseniz:
    // if(today.getDay() === 0) today.setDate(today.getDate() - 2); // Pazar ise Cuma
    // if(today.getDay() === 6) today.setDate(today.getDate() - 1); // Cumartesi ise Cuma
    const todayStr = today.toISOString().split('T')[0];
    dateInput.value = todayStr;

    const showLoading = () => {
        loadingArea.classList.remove('hidden');
        loadingArea.classList.add('flex');
        tableBody.innerHTML = '';
        searchInput.disabled = true;
    };

    const hideLoading = () => {
        loadingArea.classList.add('hidden');
        loadingArea.classList.remove('flex');
        searchInput.disabled = false;
    };

    const loadData = async () => {
        const selectedDate = dateInput.value;
        if(!selectedDate) return alert('Lütfen bir tarih seçin.');

        showLoading();
        tableTitle.textContent = "Veriler Arasında Aranıyor...";
        tableSubTitle.textContent = `${selectedDate} tarihli TEFAS kayıtları sorgulanıyor.`;
        resultCount.textContent = "Sorgulanıyor...";

        try {
            const res = await fetch(`/api/tefas/fund-details?date=${selectedDate}`, {
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            
            if (data && data.success) {
                currentData = data.data || [];
                tableTitle.textContent = `${selectedDate} Tarihli Veriler`;
                
                if (currentData.length > 0) {
                    tableSubTitle.textContent = "Bu tarihe ait fon fiyatları ve detayları listeleniyor.";
                    renderTable(currentData);
                    searchInput.value = '';
                    searchInput.focus(); // Arama çubuğuna focusla
                } else {
                    tableSubTitle.textContent = "Kayıt bulunamadı.";
                    resultCount.textContent = "0 Fon";
                    tableBody.innerHTML = `<tr><td colspan="5" class="px-6 py-20 text-center text-gray-500 font-medium bg-gray-50/50">Bu tarihe ait veri bulunamadı. Lütfen işlem günlerini (Hafta içi) tercih ediniz.</td></tr>`;
                }
            } else {
                throw new Error("API hatası veya veriye ulaşılamadı.");
            }
        } catch (error) {
            console.error("Tarihsel Veri Hatası: ", error);
            tableTitle.textContent = "Hata Oluştu";
            tableSubTitle.textContent = "Veriler çekilirken sistemsel bir hata meydana geldi.";
            resultCount.textContent = "Hata";
            tableBody.innerHTML = `<tr><td colspan="5" class="px-6 py-20 text-center text-red-500 font-medium">Veriler yüklenirken bir hata oluştu. Backend bağlantınızı kontrol edin.</td></tr>`;
        } finally {
            hideLoading();
        }
    };

    const renderTable = (funds) => {
        tableBody.innerHTML = '';
        resultCount.textContent = `${funds.length} Fon`;

        if (funds.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="px-6 py-20 text-center text-gray-500 font-medium bg-gray-50/50">Arama kriterinize uygun fon bulunamadı.</td></tr>`;
            return;
        }

        // DOM oluşturma maliyetini düşürmek için fragment (RAM üzerinde toplu oluşturma) kullanıyoruz
        const fragment = document.createDocumentFragment();

        funds.forEach(fund => {
            const tr = document.createElement('tr');
            tr.className = "hover:bg-brand-teal/5 transition-colors group cursor-pointer";
            tr.onclick = () => { window.location.href = `/funds/${fund.code}?date=${dateInput.value}`; };
            
            // Fiyat formatlama
            const fiyatNum = parseFloat(fund.fiyat || fund.last_price || 0);
            const fiyat = fiyatNum > 0 ? fiyatNum.toFixed(6) : '-';
            
            const catName = fund.category_name || '-';
            
            tr.innerHTML = `
                <td class="px-6 py-3 whitespace-nowrap border-r border-gray-50">
                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md bg-gray-100 text-gray-800 font-bold text-xs tracking-widest group-hover:bg-brand-teal group-hover:text-white transition-colors shadow-sm">
                        ${fund.code}
                    </span>
                </td>
                <td class="px-6 py-3">
                    <div class="text-sm font-semibold text-gray-800 line-clamp-1" title="${fund.name}">${fund.name || '-'}</div>
                </td>
                <td class="px-6 py-3">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-50 inline-block px-2 py-1 rounded-md">${catName}</div>
                </td>
                <td class="px-6 py-3 text-right">
                    <div class="text-sm font-black text-gray-700">${fiyat} <span class="text-gray-400 text-[10px] ml-1">TL</span></div>
                </td>
                <td class="px-6 py-3 text-center border-l border-gray-50">
                    <div class="opacity-0 group-hover:opacity-100 text-brand-teal text-[11px] font-bold transition-all flex items-center justify-center gap-1">
                        İncele <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </td>
            `;
            fragment.appendChild(tr);
        });

        tableBody.appendChild(fragment);
    };

    // --- Olay Dinleyiciler (Events) ---
    
    // Yükle Butonu
    loadBtn.addEventListener('click', loadData);

    // Hızlı Arama (Filtre)
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        if(!query) {
            renderTable(currentData);
            return;
        }

        const filtered = currentData.filter(f => {
            return (f.code && f.code.toLowerCase().includes(query)) || 
                   (f.name && f.name.toLowerCase().includes(query));
        });
        
        renderTable(filtered);
    });

    // Opsiyonel: İlk girişte varsayılan günü otomatik yüklesin
    // loadData();
});
</script>
@endsection