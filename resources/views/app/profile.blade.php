@extends('layouts.app')

@section('title', 'Profilim')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Profilim</h2>
    <p class="text-gray-500 mt-1">Hesap bilgilerinizi ve takip ettiğiniz favori fonları yönetin.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Kullanıcı Bilgileri Kartı (Sol Kolon) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm flex flex-col items-center text-center relative overflow-hidden">
            <!-- Arka Plan Dekoru -->
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-brand-teal to-blue-500 opacity-10"></div>
            
            <!-- Avatar -->
            <div class="w-24 h-24 rounded-full bg-white border-4 border-white shadow-md flex items-center justify-center text-3xl font-black text-brand-teal relative z-10 mb-4" id="userAvatar">
                ?
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-1 relative z-10" id="userName">Yükleniyor...</h3>
            <p class="text-sm font-semibold text-gray-400 mb-8 relative z-10" id="userEmail">Lütfen bekleyin</p>
            
            <div class="w-full h-px bg-gray-100 mb-8"></div>
            
            <!-- Çıkış Butonu -->
            <button id="logoutBtn" class="w-full bg-red-50 hover:bg-red-500 text-red-500 hover:text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Hesaptan Çıkış Yap
            </button>
        </div>
    </div>

    <!-- Favori Fonlar Kartı (Sağ Kolon) -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm h-full flex flex-col">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        Favori Fonlarım
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Sıkı takip etmek istediğiniz fonlar burada listelenir.</p>
                </div>
                <div class="bg-amber-50 text-amber-600 text-xs font-bold px-3 py-1.5 rounded-lg" id="favCount">0 Fon</div>
            </div>

            <!-- Loading Göstergesi -->
            <div id="favLoading" class="hidden flex-col items-center justify-center py-10">
                <div class="w-10 h-10 rounded-full border-4 border-gray-100 border-t-brand-teal animate-spin mb-3"></div>
                <span class="text-sm font-semibold text-gray-400 animate-pulse">Favoriler yükleniyor...</span>
            </div>

            <!-- Favoriler Listesi -->
            <div id="favList" class="flex-1 overflow-y-auto pr-2 flex flex-col gap-3">
                <!-- JS ile Doldurulacak -->
            </div>
            
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('auth_token');
    if(!token) {
        window.location.href = '/login';
        return;
    }

    const userNameEl = document.getElementById('userName');
    const userEmailEl = document.getElementById('userEmail');
    const userAvatarEl = document.getElementById('userAvatar');
    
    const favListEl = document.getElementById('favList');
    const favLoadingEl = document.getElementById('favLoading');
    const favCountEl = document.getElementById('favCount');

    // 1. Kullanıcı Bilgilerini Çek
    const loadUser = async () => {
        try {
            // /api/user rotasına token ile istek atıyoruz
            const res = await fetch('/api/user', {
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if(res.ok) {
                const user = await res.json();
                userNameEl.textContent = user.name || 'İsimsiz Kullanıcı';
                userEmailEl.textContent = user.email || 'Email Yok';
                
                // İsimden baş harf oluşturma (Avatar için)
                if(user.name) {
                    userAvatarEl.textContent = user.name.charAt(0).toUpperCase();
                } else {
                    userAvatarEl.textContent = '👤';
                }
            } else {
                userNameEl.textContent = "Bağlantı Hatası";
                userEmailEl.textContent = "-";
            }
        } catch(err) {
            console.error("Kullanıcı verisi alınamadı:", err);
        }
    };

    // 2. Favorileri Çek
    const loadFavorites = async () => {
        favListEl.innerHTML = '';
        favLoadingEl.classList.remove('hidden');
        favLoadingEl.classList.add('flex');
        
        try {
            const res = await fetch('/api/favorites', {
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            
            favLoadingEl.classList.add('hidden');
            favLoadingEl.classList.remove('flex');

            const favorites = (data && data.success && data.data) ? data.data : (Array.isArray(data) ? data : []);
            
            renderFavorites(favorites);
        } catch(err) {
            console.error("Favoriler alınamadı:", err);
            favLoadingEl.classList.add('hidden');
            favListEl.innerHTML = `<div class="text-center p-6 bg-red-50 text-red-500 rounded-2xl text-sm font-semibold">Favoriler yüklenirken bir hata oluştu.</div>`;
        }
    };

    // 3. Favorileri Ekrana Çiz
    const renderFavorites = (favorites) => {
        favListEl.innerHTML = '';
        favCountEl.textContent = `${favorites.length} Fon`;

        if(favorites.length === 0) {
            favListEl.innerHTML = `
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                    <span class="text-gray-400 text-sm font-semibold">Henüz favori listenizde fon bulunmuyor.</span>
                    <span class="text-gray-400 text-xs mt-1">Fonları inceleyerek yıldız ikonuna tıklayıp favorilere ekleyebilirsiniz.</span>
                </div>
            `;
            return;
        }

        favorites.forEach(fav => {
            // API yapınıza göre fav objesinin yapısı farklı olabilir. 
            // Genelde fav.fund_code ve fav.fund.name olarak gelir.
            const fundCode = fav.fund_code || fav.code || 'Bilinmiyor';
            const fundName = (fav.fund && fav.fund.name) ? fav.fund.name : (fav.name || 'Fon adı bulunamadı');
            
            const item = document.createElement('div');
            item.className = "group flex items-center justify-between p-4 bg-gray-50/50 hover:bg-white border border-transparent hover:border-brand-teal/30 rounded-2xl transition-all shadow-sm cursor-pointer";
            
            // Satıra tıklayınca detay sayfasına git
            item.addEventListener('click', () => {
                window.location.href = `/funds/${fundCode}`;
            });
            
            item.innerHTML = `
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-teal/10 to-blue-500/10 text-brand-teal flex items-center justify-center font-black tracking-widest text-sm border border-brand-teal/20 group-hover:bg-brand-teal group-hover:text-white transition-colors">
                        ${fundCode}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm line-clamp-1">${fundName}</h4>
                        <div class="text-[10px] text-brand-teal font-semibold uppercase mt-0.5 tracking-wider flex items-center gap-1">
                            İncele <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </div>
                
                <button class="remove-fav-btn p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors focus:outline-none" data-code="${fundCode}" title="Favorilerden Çıkar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            `;
            
            // Kaldır butonuna Event ekleme
            const removeBtn = item.querySelector('.remove-fav-btn');
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Detay sayfasına yönlenmeyi engelle
                removeFavorite(fundCode);
            });
            
            favListEl.appendChild(item);
        });
    };

    // 4. Favoriyi Kaldır
    const removeFavorite = async (fundCode) => {
        // Optimistik olarak UI'da siliniyor hissi vermek için butonu disabled yapabiliriz
        const confirmDelete = confirm(`${fundCode} fonunu favorilerinden çıkarmak istediğine emin misin?`);
        if(!confirmDelete) return;

        try {
            const res = await fetch(`/api/favorites/${fundCode}`, {
                method: 'DELETE',
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if(res.ok) {
                // Silme başarılı, listeyi yenile
                loadFavorites();
            } else {
                alert('Silme işlemi başarısız oldu.');
            }
        } catch(err) {
            console.error("Favori silinirken hata:", err);
            alert('Sunucu ile bağlantı kurulamadı.');
        }
    };

    // Başlangıç Yüklemeleri
    loadUser();
    loadFavorites();
});
</script>
@endsection