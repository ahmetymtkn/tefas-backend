import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {

    // --- LOGİN İŞLEMLERİ ---
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            const btn = document.getElementById('loginBtn');
            const spinner = document.getElementById('loginSpinner');
            const alertBox = document.getElementById('loginAlert');
            
            // UI Loading state
            btn.disabled = true;
            btn.classList.add('opacity-75');
            spinner.classList.remove('hidden');
            alertBox.classList.add('hidden');
            
            try {
                const response = await window.axios.post('/api/login', {
                    email,
                    password
                });
                
                // Başarılı giriş
                if (response.data.success && response.data.token) {
                    // Token'i LocalStorage'a kaydet
                    localStorage.setItem('auth_token', response.data.token);
                    localStorage.setItem('user_data', JSON.stringify(response.data.user));
                    
                    alertBox.textContent = 'Giriş başarılı! Yönlendiriliyorsunuz...';
                    alertBox.className = 'bg-green-50 text-green-600 border border-green-200 p-3 rounded-lg text-sm text-center mb-4 block';
                    
                    // Ana sayfaya yönlendir
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1000);
                }
            } catch (error) {
                // Hata durumu
                btn.disabled = false;
                btn.classList.remove('opacity-75');
                spinner.classList.add('hidden');
                
                let errorMsg = 'Giriş yapılamadı. Bilgilerinizi kontrol ediniz.';
                if (error.response && error.response.data && error.response.data.message) {
                    errorMsg = error.response.data.message;
                }
                
                alertBox.textContent = errorMsg;
                alertBox.className = 'bg-red-50 text-red-600 border border-red-200 p-3 rounded-lg text-sm text-center mb-4 block';
            }
        });
    }


    // --- REGİSTER İŞLEMLERİ ---
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            
            const btn = document.getElementById('registerBtn');
            const spinner = document.getElementById('registerSpinner');
            const alertBox = document.getElementById('registerAlert');
            
            // Basit şifre kontrolü
            if (password !== password_confirmation) {
                alertBox.textContent = 'Şifreler eşleşmiyor!';
                alertBox.className = 'bg-red-50 text-red-600 border border-red-200 p-3 rounded-lg text-sm text-center mb-4 block';
                return;
            }
            
            // UI Loading state
            btn.disabled = true;
            btn.classList.add('opacity-75');
            spinner.classList.remove('hidden');
            alertBox.classList.add('hidden');
            
            try {
                const response = await window.axios.post('/api/register', {
                    name,
                    email,
                    password,
                    password_confirmation
                });
                
                // Kayıt başarılı
                if (response.data.success) {
                    alertBox.textContent = 'Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...';
                    alertBox.className = 'bg-green-50 text-green-600 border border-green-200 p-3 rounded-lg text-sm text-center mb-4 block';
                    
                    // Giriş sayfasına yönlendir
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 1500);
                }
            } catch (error) {
                btn.disabled = false;
                btn.classList.remove('opacity-75');
                spinner.classList.add('hidden');
                
                let errorMsg = 'Kayıt sırasında bir hata oluştu.';
                
                // API'den dönen validasyon hataları
                if (error.response && error.response.data && error.response.data.errors) {
                    const firstErrorKey = Object.keys(error.response.data.errors)[0];
                    errorMsg = error.response.data.errors[firstErrorKey][0];
                } else if (error.response && error.response.data && error.response.data.message) {
                    errorMsg = error.response.data.message;
                }
                
                alertBox.textContent = errorMsg;
                alertBox.className = 'bg-red-50 text-red-600 border border-red-200 p-3 rounded-lg text-sm text-center mb-4 block';
            }
        });
    }

});
