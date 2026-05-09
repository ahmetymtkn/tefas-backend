@extends('layouts.app')

@section('title', 'Periyodik Kontrol')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Periyodik Kontrol</h2>
    <p class="text-gray-500 mt-1">Seçilen periyot içinde fonların toplam kaç gün yükseldiği ve düştüğü.</p>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex justify-end items-center bg-gray-50/50">
        <div class="flex gap-4 items-center">
            <div class="text-sm text-gray-500 font-medium">
                Tarih: <span id="latestDateMetric" class="text-gray-800 font-bold">--</span>
            </div>
            <div class="text-sm text-gray-500 font-medium">
                Sonuç: <span id="totalFundsMetric" class="text-brand-orange-dark font-bold">--</span>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto relative min-h-[400px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr id="tableHeaders" class="bg-white border-b border-gray-200"></tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-gray-100">
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <svg class="animate-spin h-6 w-6 text-brand-teal mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Veriler Yükleniyor...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="floatingDropdown" class="hidden fixed z-50 bg-white border border-gray-200 rounded-xl shadow-xl min-w-[220px]" style="top:0;left:0;">
    <div id="dropList" class="max-h-64 overflow-y-auto"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('auth_token');
    if (!token) return;

    const PERIODS = [3, 5, 7, 14, 21, 30];
    let rawData = [];
    let sortDesc = true;
    let activePeriod = 30;
    let allCategories = [];
    let selectedCategories = new Set();
    let openDropType = null;

    const tableHeaders = document.getElementById('tableHeaders');
    const tableBody = document.getElementById('tableBody');
    const dropdown = document.getElementById('floatingDropdown');
    const dropList = document.getElementById('dropList');

    const openPeriodDropdown = (anchorEl, items, onSelect, selectedVal) => {
        dropList.innerHTML = '';
        items.forEach(item => {
            const isActive = String(item.value) === String(selectedVal);
            const div = document.createElement('div');
            div.className = `px-4 py-2.5 text-sm cursor-pointer transition-colors flex items-center justify-between gap-3 ${isActive ? 'bg-brand-teal/10 text-brand-teal font-semibold' : 'text-gray-700 hover:bg-gray-50'}`;
            div.innerHTML = `<span>${item.label}</span>${isActive ? '<svg class="w-4 h-4 flex-shrink-0 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>' : ''}`;
            div.addEventListener('click', (e) => { e.stopPropagation(); onSelect(item.value); closeDropdown(); });
            dropList.appendChild(div);
        });
        positionAndShow(anchorEl);
    };

    const openCategoryDropdown = (anchorEl) => {
        dropList.innerHTML = '';
        allCategories.forEach(cat => {
            const isChecked = selectedCategories.size === 0 || selectedCategories.has(cat);
            const row = document.createElement('label');
            row.className = 'flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50 transition-colors';
            row.innerHTML = `<input type="checkbox" class="w-4 h-4 rounded accent-brand-teal cursor-pointer" ${isChecked ? 'checked' : ''} data-cat="${cat}"><span class="text-sm text-gray-700 flex-1">${cat}</span>`;
            row.addEventListener('click', (e) => {
                e.stopPropagation();
                const cb = row.querySelector('input');
                setTimeout(() => toggleCategory(cat, cb.checked), 0);
            });
            dropList.appendChild(row);
        });
        positionAndShow(anchorEl);
    };

    const toggleCategory = (cat, checked) => {
        if (selectedCategories.size === 0) {
            allCategories.forEach(c => { if (c !== cat) selectedCategories.add(c); });
        } else {
            if (checked) {
                selectedCategories.add(cat);
                if (selectedCategories.size === allCategories.length) selectedCategories.clear();
            } else {
                selectedCategories.delete(cat);
                if (selectedCategories.size === 0) selectedCategories.add(allCategories[0]);
            }
        }
        applyFilterAndRender();
        renderHeaders();
    };

    dropList.addEventListener('click', (e) => e.stopPropagation());

    const positionAndShow = (anchorEl) => {
        const rect = anchorEl.getBoundingClientRect();
        dropdown.style.top = `${rect.bottom + window.scrollY + 6}px`;
        dropdown.style.left = `${rect.left + window.scrollX}px`;
        dropdown.classList.remove('hidden');
    };

    const closeDropdown = () => { dropdown.classList.add('hidden'); openDropType = null; renderHeaders(); };
    document.addEventListener('click', (e) => { if (!dropdown.contains(e.target)) closeDropdown(); });

    const catLabel = () => selectedCategories.size === 0 || selectedCategories.size === allCategories.length ? 'Kategori' : `Kategori <span class="ml-1 px-1.5 py-0.5 rounded-full bg-brand-teal text-white text-[10px] font-bold normal-case">${selectedCategories.size}</span>`;

    const renderHeaders = () => {
        tableHeaders.innerHTML = '';
        const periodItems = PERIODS.map(p => ({ value: p, label: `${p} Günlük Periyot` }));

        const appendTh = (label, extraClass = '') => {
            const th = document.createElement('th'); th.className = `px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider ${extraClass}`; th.textContent = label; tableHeaders.appendChild(th);
        };
        const appendThClickable = (label, col, isOpen, onClick) => {
            const th = document.createElement('th'); th.className = 'px-6 py-4 text-xs font-bold uppercase tracking-wider cursor-pointer select-none group';
            th.innerHTML = `<div class="flex items-center gap-1.5 ${isOpen ? 'text-brand-teal' : 'text-gray-400 hover:text-gray-600'}"><span>${label}</span><svg class="w-3.5 h-3.5 transition-transform ${isOpen ? 'rotate-180 text-brand-teal' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg></div>`;
            th.addEventListener('click', (e) => { e.stopPropagation(); onClick(); }); tableHeaders.appendChild(th);
        };
        const appendThSort = (label, isDesc, alignClass = '', onClick) => {
            const th = document.createElement('th'); th.className = `px-6 py-4 text-xs font-bold uppercase tracking-wider cursor-pointer select-none ${alignClass}`;
            th.innerHTML = `<div class="flex items-center gap-1.5 justify-end text-brand-teal hover:text-brand-teal/80"><span>${label}</span><svg class="w-3.5 h-3.5 transition-transform ${isDesc ? '' : 'rotate-180'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg></div>`;
            th.addEventListener('click', onClick); tableHeaders.appendChild(th);
        };

        appendTh('Fon', 'w-56');
        appendThClickable(catLabel(), 'category', openDropType === 'category', () => { if (openDropType === 'category') { closeDropdown(); return; } openDropType = 'category'; renderHeaders(); openCategoryDropdown(tableHeaders.children[1]); });
        appendThClickable('Periyot', 'period', openDropType === 'period', () => { if (openDropType === 'period') { closeDropdown(); return; } openDropType = 'period'; renderHeaders(); openPeriodDropdown(tableHeaders.children[2], periodItems, (val) => { activePeriod = val; loadData(); }, activePeriod); });
        appendTh('Yükseliş / Düşüş');
        appendTh('Başarı Oranı');
        appendThSort('Toplam Getiri', sortDesc, 'text-right', () => { sortDesc = !sortDesc; applyFilterAndRender(); renderHeaders(); });
    };

    const loadData = async () => {
        tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-gray-500"><svg class="animate-spin h-6 w-6 text-brand-teal mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Yükleniyor...</td></tr>`;
        try {
            const response = await window.axios.get(`/api/tefas/trend-checks/${activePeriod}`, { headers: { 'Authorization': `Bearer ${token}` } });
            if (response.data?.success) {
                rawData = response.data.data;
                document.getElementById('totalFundsMetric').textContent = response.data.total_funds;
                document.getElementById('latestDateMetric').textContent = response.data.analysis_date;
                allCategories = [...new Set(rawData.map(f => f.category_name).filter(Boolean))].sort();
                selectedCategories.clear();
                applyFilterAndRender();
            }
        } catch (err) {
            tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-sm text-red-500">${err.response?.data?.message || 'Veri alınamadı.'}</td></tr>`;
            document.getElementById('totalFundsMetric').textContent = '--';
            document.getElementById('latestDateMetric').textContent = '--';
        }
    };

    const applyFilterAndRender = () => {
        let filtered = selectedCategories.size > 0 ? rawData.filter(f => selectedCategories.has(f.category_name)) : [...rawData];
        filtered.sort((a, b) => sortDesc ? b.total_return - a.total_return : a.total_return - b.total_return);
        document.getElementById('totalFundsMetric').textContent = filtered.length;
        renderHeaders();
        
        tableBody.innerHTML = '';
        if (filtered.length === 0) { tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Kayıt bulunamadı.</td></tr>'; return; }
        
        filtered.forEach(fund => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors cursor-pointer';
            tr.addEventListener('click', () => { window.location.href = `/funds/${fund.fund_code}`; });
            
            const totalDays  = fund.up_days_count + fund.down_days_count;
            const successRate = totalDays > 0 ? Math.round((fund.up_days_count / totalDays) * 100) : 0;
            
            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-800">${fund.fund_code}</div>
                    <div class="text-xs text-gray-500 truncate w-48 overflow-hidden whitespace-nowrap" title="${fund.fund_name}">${fund.fund_name}</div>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-600">${fund.category_name}</td>
                <td class="px-6 py-4"><span class="bg-brand-teal-light/30 text-brand-teal px-2.5 py-1 rounded-md text-xs font-bold">${fund.period_days} Günlük</span></td>
                <td class="px-6 py-4 text-sm font-medium"><span class="text-brand-teal font-bold">${fund.up_days_count}↑</span> <span class="text-gray-300 mx-1">/</span> <span class="text-red-500 font-bold">${fund.down_days_count}↓</span></td>
                <td class="px-6 py-4"><div class="w-full bg-gray-200 rounded-full h-2.5 max-w-[100px] mb-1"><div class="bg-brand-teal h-2.5 rounded-full" style="width:${successRate}%"></div></div><span class="text-xs font-bold text-gray-500">%${successRate}</span></td>
                <td class="px-6 py-4 text-right font-bold ${fund.total_return >= 0 ? 'text-brand-teal' : 'text-red-500'}">%${parseFloat(fund.total_return).toFixed(2)}</td>`;
            tableBody.appendChild(tr);
        });
    };

    renderHeaders();
    loadData();
});
</script>
@endsection
