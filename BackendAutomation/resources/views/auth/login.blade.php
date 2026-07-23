@extends('layouts.app')

@section('title', 'Masuk Akun - YouTube Shorts Automation')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        
        <!-- Logo Header (Sleek & Compact) -->
        <div class="text-center mb-5">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-r from-red-600 to-rose-500 shadow-md shadow-red-900/30 mb-2.5">
                <i class="fa-brands fa-youtube text-2xl text-white"></i>
            </div>
            <h1 class="font-outfit text-2xl font-bold tracking-tight text-white">Shorts Automation</h1>
            <p class="text-gray-400 text-xs mt-0.5">Platform Otomatisasi & Scraping YouTube Shorts</p>
        </div>

        <!-- Auth Form Card -->
        <div class="glass rounded-3xl p-7 shadow-2xl relative overflow-hidden">
            
            <form id="form-login" onsubmit="handleLogin(event)" class="space-y-4">
                <h2 class="font-outfit text-lg font-bold text-white mb-1">Masuk ke Akun Anda</h2>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" id="login-email" required placeholder="nama@email.com"
                            class="w-full bg-slate-900/50 border border-slate-700/60 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-white text-sm rounded-xl pl-10 pr-4 py-2.5 outline-none transition placeholder-gray-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="login-password" required placeholder="••••••••"
                            class="w-full bg-slate-900/50 border border-slate-700/60 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-white text-sm rounded-xl pl-10 pr-4 py-2.5 outline-none transition placeholder-gray-500">
                    </div>
                </div>

                <button type="submit" id="btn-login"
                    class="w-full bg-gradient-to-r from-red-600 to-rose-500 hover:from-red-500 hover:to-rose-400 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-red-900/30 transition-all flex items-center justify-center gap-2 mt-2">
                    <span>Masuk Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                <p class="text-center text-xs text-gray-400 mt-3">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-red-400 font-semibold hover:underline">Daftar Akun Baru</a>
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

    async function handleLogin(e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const btn = document.getElementById('btn-login');

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Memproses...`;

        try {
            const res = await fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, password })
            });

            const data = await res.json();

            if (res.ok && data.access_token) {
                localStorage.setItem('auth_token', data.access_token);
                localStorage.setItem('access_token', data.access_token);
                if (data.refresh_token) localStorage.setItem('refresh_token', data.refresh_token);
                localStorage.setItem('auth_user', JSON.stringify(data.user));
                localStorage.setItem('user', JSON.stringify(data.user));
                
                showToast('Berhasil masuk!', 'success');
                setTimeout(() => {
                    window.location.href = "{{ route('dashboard') }}";
                }, 500);
            } else {
                showToast(data.message || 'Login gagal', 'error');
            }
        } catch (err) {
            showToast('Terjadi kesalahan koneksi', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<span>Masuk Sekarang</span> <i class="fa-solid fa-arrow-right text-xs"></i>`;
        }
    }
</script>
@endpush
