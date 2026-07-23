@extends('layouts.app')

@section('title', 'Daftar Akun - YouTube Shorts Automation')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl gradient-bg shadow-lg shadow-purple-500/20 mb-4">
                <i class="fa-brands fa-youtube text-3xl text-white"></i>
            </div>
            <h1 class="font-outfit text-3xl font-bold tracking-tight text-white">Shorts Automation</h1>
            <p class="text-gray-400 text-sm mt-1">Platform Otomatisasi & Scraping YouTube Shorts</p>
        </div>

        <!-- Auth Form Card -->
        <div class="glass rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            
            <form id="form-register" onsubmit="handleRegister(event)" class="space-y-5">
                <h2 class="font-outfit text-xl font-bold text-white mb-2">Buat Akun Baru</h2>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">Nama Pengguna</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-regular fa-user"></i>
                        </span>
                        <input type="text" id="reg-name" required placeholder="Username"
                            class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" id="reg-email" required placeholder="nama@email.com"
                            class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">Kata Sandi (Min. 6 Karakter)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="reg-password" required minlength="6" placeholder="••••••••"
                            class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                    </div>
                </div>

                <button type="submit" id="btn-register"
                    class="w-full gradient-bg hover:opacity-90 text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg shadow-purple-500/25 transition duration-200 flex items-center justify-center gap-2">
                    <span>Daftar Akun</span>
                    <i class="fa-solid fa-user-plus text-xs"></i>
                </button>

                <p class="text-center text-sm text-gray-400 mt-4">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-brand-500 font-semibold hover:underline">Masuk Sekarang</a>
                </p>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('auth_token')) {
            window.location.href = "{{ route('dashboard') }}";
        }
    });

    async function handleRegister(e) {
        e.preventDefault();
        const name = document.getElementById('reg-name').value;
        const email = document.getElementById('reg-email').value;
        const password = document.getElementById('reg-password').value;
        const btn = document.getElementById('btn-register');

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Memproses...`;

        try {
            const res = await fetch('/api/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ username: name, name, email, password })
            });

            const data = await res.json();

            if (res.ok && data.access_token) {
                localStorage.setItem('auth_token', data.access_token);
                localStorage.setItem('access_token', data.access_token);
                if (data.refresh_token) localStorage.setItem('refresh_token', data.refresh_token);
                localStorage.setItem('auth_user', JSON.stringify(data.user));
                localStorage.setItem('user', JSON.stringify(data.user));
                
                showToast('Pendaftaran akun berhasil!', 'success');
                setTimeout(() => {
                    window.location.href = "{{ route('dashboard') }}";
                }, 500);
            } else {
                showToast(data.message || 'Registrasi gagal', 'error');
            }
        } catch (err) {
            showToast('Terjadi kesalahan koneksi', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<span>Daftar Akun</span> <i class="fa-solid fa-user-plus text-xs"></i>`;
        }
    }
</script>
@endpush
