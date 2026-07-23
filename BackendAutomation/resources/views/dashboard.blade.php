@extends('layouts.app')

@section('title', 'Dashboard Otomatisasi - YouTube Shorts')

@section('content')
<div class="min-h-screen flex flex-col">
    
    <!-- Navigation Header -->
    <header class="glass sticky top-0 z-40 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl gradient-bg flex items-center justify-center shadow-md shadow-purple-500/20">
                    <i class="fa-brands fa-youtube text-xl text-white"></i>
                </div>
                <div>
                    <span class="font-outfit text-xl font-bold tracking-tight text-white block">ShortsAutomation</span>
                    <span class="text-xs text-emerald-400 flex items-center gap-1.5 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Systems Operational
                    </span>
                </div>
            </div>

            <!-- User Bar & Logout -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-3 bg-slate-900/60 border border-slate-800 rounded-full py-1.5 px-4">
                    <div class="w-7 h-7 rounded-full bg-brand-500 flex items-center justify-center text-xs font-bold text-white">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="text-left">
                        <span id="user-display-name" class="text-sm font-semibold text-white block leading-none">User</span>
                        <span id="user-display-email" class="text-xs text-gray-400 block leading-tight">user@email.com</span>
                    </div>
                </div>

                <button onclick="handleLogout()" title="Keluar Akun"
                    class="bg-slate-800 hover:bg-rose-600/20 hover:text-rose-400 text-gray-300 px-4 py-2 rounded-xl text-sm font-medium transition border border-slate-700 hover:border-rose-500/30 flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hidden md:inline">Keluar</span>
                </button>
            </div>

        </div>
    </header>

    <!-- Main Body Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
        
        <!-- Quick Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass-card rounded-2xl p-6 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Sesi Otomatisasi</p>
                        <h3 id="stat-total-runs" class="text-3xl font-bold font-outfit text-white mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xl">
                        <i class="fa-solid fa-rotate"></i>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Video Discrape</p>
                        <h3 id="stat-total-videos" class="text-3xl font-bold font-outfit text-emerald-400 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl">
                        <i class="fa-solid fa-video"></i>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Status Terakhir</p>
                        <h3 id="stat-last-status" class="text-xl font-bold font-outfit text-gray-300 mt-1">Belum ada</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-xl">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex border-b border-slate-800 mb-8 gap-8">
            <button onclick="switchTab('automation')" id="tab-btn-automation"
                class="pb-4 text-sm font-semibold border-b-2 border-brand-500 text-white flex items-center gap-2 transition">
                <i class="fa-solid fa-sliders"></i>
                <span>Pengaturan Otomatisasi</span>
            </button>

            <button onclick="switchTab('history')" id="tab-btn-history"
                class="pb-4 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-white flex items-center gap-2 transition">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat Scraping</span>
                <span id="history-badge" class="bg-slate-800 text-xs px-2 py-0.5 rounded-full font-normal text-gray-300">0</span>
            </button>
        </div>

        <!-- TAB 1: AUTOMATION SETTINGS -->
        <div id="tab-content-automation" class="space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Form Section -->
                <div class="lg:col-span-2 glass-card rounded-3xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="font-outfit text-2xl font-bold text-white">Mulai Otomatisasi</h2>
                            <p class="text-sm text-gray-400">Atur durasi scroll otomatis YouTube Shorts & pengambilan data video</p>
                        </div>
                        <span class="p-3 bg-brand-500/10 border border-brand-500/20 rounded-2xl text-brand-500 text-xl">
                            <i class="fa-solid fa-play"></i>
                        </span>
                    </div>

                    <form onsubmit="handleStartAutomation(event)" class="space-y-6">
                        
                        <!-- Duration Input -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                                Durasi Otomatisasi (Detik)
                            </label>
                            <div class="relative">
                                <input type="number" id="automation-duration" min="5" max="600" value="30" required
                                    class="w-full bg-slate-900/90 border border-slate-700 rounded-2xl px-5 py-4 text-lg font-bold text-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                                <span class="absolute right-5 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Detik</span>
                            </div>
                        </div>

                        <!-- Quick Presets -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pilihan Cepat Durasi</label>
                            <div class="grid grid-cols-4 gap-3">
                                <button type="button" onclick="setDurationPreset(30)" class="preset-btn bg-slate-800 hover:bg-brand-600 text-gray-200 py-2.5 px-3 rounded-xl text-xs font-semibold transition border border-slate-700">30 Detik</button>
                                <button type="button" onclick="setDurationPreset(60)" class="preset-btn bg-slate-800 hover:bg-brand-600 text-gray-200 py-2.5 px-3 rounded-xl text-xs font-semibold transition border border-slate-700">1 Menit</button>
                                <button type="button" onclick="setDurationPreset(120)" class="preset-btn bg-slate-800 hover:bg-brand-600 text-gray-200 py-2.5 px-3 rounded-xl text-xs font-semibold transition border border-slate-700">2 Menit</button>
                                <button type="button" onclick="setDurationPreset(300)" class="preset-btn bg-slate-800 hover:bg-brand-600 text-gray-200 py-2.5 px-3 rounded-xl text-xs font-semibold transition border border-slate-700">5 Menit</button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btn-start-automation"
                            class="w-full gradient-bg hover:opacity-90 text-white font-bold py-4 px-6 rounded-2xl shadow-xl shadow-purple-500/20 transition duration-200 flex items-center justify-center gap-3 text-base">
                            <i class="fa-solid fa-rocket"></i>
                            <span>Jalankan Otomatisasi Sekarang</span>
                        </button>
                    </form>
                </div>

                <!-- Right Information Panel -->
                <div class="glass-card rounded-3xl p-8 flex flex-col justify-between">
                    <div>
                        <h3 class="font-outfit text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-brand-500"></i> Cara Kerja Sistem
                        </h3>
                        <ul class="space-y-4 text-xs text-gray-300">
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-brand-500 shrink-0">1</span>
                                <span>Browser Playwright otomatis membuka YouTube Shorts tanpa interaksi manual.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-brand-500 shrink-0">2</span>
                                <span>Script otomatis mensimulasikan tontonan dan melakukan scroll dengan tombol <code>ArrowDown</code>.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-brand-500 shrink-0">3</span>
                                <span>Judul video, nama channel, URL, dan timestamp secara otomatis disimpan ke Database MySQL backend.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-xs text-indigo-300">
                        <p class="font-semibold mb-1"><i class="fa-solid fa-shield-halved mr-1"></i> Mode Otomatisasi</p>
                        <span>Eksekusi langsung membuka browser Chromium di desktop Anda untuk melakukan tontonan otomatis.</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- TAB 2: HISTORY LIST -->
        <div id="tab-content-history" class="space-y-6 hidden">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="font-outfit text-2xl font-bold text-white">Riwayat Otomatisasi</h2>
                    <p class="text-sm text-gray-400">Daftar sesi otomatisasi & hasil scraping video</p>
                </div>

                <button onclick="fetchHistory()"
                    class="bg-slate-800 hover:bg-slate-700 text-gray-200 px-4 py-2.5 rounded-xl text-xs font-semibold border border-slate-700 transition flex items-center gap-2 self-start">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Muat Ulang Riwayat</span>
                </button>
            </div>

            <!-- History Table Container -->
            <div class="glass-card rounded-3xl overflow-hidden border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-300">
                        <thead class="bg-slate-900/80 text-xs uppercase tracking-wider text-gray-400 border-b border-slate-800">
                            <tr>
                                <th class="py-4 px-6">ID Sesi</th>
                                <th class="py-4 px-6">Waktu Mulai</th>
                                <th class="py-4 px-6">Durasi</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6">Total Video</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body" class="divide-y divide-slate-800/60">
                            <tr>
                                <td colspan="6" class="text-center py-12 text-gray-500">
                                    <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 block"></i>
                                    <span>Memuat data riwayat...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>

</div>

<!-- HISTORY DETAIL MODAL -->
<div id="modal-detail" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md hidden opacity-0 transition-opacity duration-300">
    <div class="glass rounded-3xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl border border-slate-700/80 overflow-hidden transform scale-95 transition-transform duration-300" id="modal-panel">
        
        <!-- Modal Header -->
        <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/60">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-500 flex items-center justify-center">
                    <i class="fa-solid fa-list-check text-lg"></i>
                </div>
                <div>
                    <h3 class="font-outfit text-xl font-bold text-white flex items-center gap-2">
                        <span>Detail Otomatisasi</span>
                        <span id="modal-history-id" class="text-xs bg-brand-500/20 text-brand-300 px-2.5 py-0.5 rounded-full border border-brand-500/30">#0</span>
                    </h3>
                    <p class="text-xs text-gray-400" id="modal-history-date">-</p>
                </div>
            </div>

            <button onclick="closeModalDetail()" class="text-gray-400 hover:text-white w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Content Body -->
        <div class="p-8 overflow-y-auto custom-scrollbar flex-1 space-y-6">
            
            <!-- Stat Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-slate-900/60 border border-slate-800 p-4 rounded-2xl">
                    <span class="text-xs text-gray-400 block mb-1">Status</span>
                    <span id="modal-status-badge" class="inline-block font-semibold text-xs px-2.5 py-1 rounded-full bg-slate-800 text-gray-300">Pending</span>
                </div>
                <div class="bg-slate-900/60 border border-slate-800 p-4 rounded-2xl">
                    <span class="text-xs text-gray-400 block mb-1">Target Durasi</span>
                    <span id="modal-duration" class="font-bold text-white text-base">30 Detik</span>
                </div>
                <div class="bg-slate-900/60 border border-slate-800 p-4 rounded-2xl">
                    <span class="text-xs text-gray-400 block mb-1">Total Video</span>
                    <span id="modal-total-videos" class="font-bold text-emerald-400 text-base">0 Video</span>
                </div>
                <div class="bg-slate-900/60 border border-slate-800 p-4 rounded-2xl">
                    <span class="text-xs text-gray-400 block mb-1">Waktu Selesai</span>
                    <span id="modal-completed-at" class="font-semibold text-gray-300 text-xs">-</span>
                </div>
            </div>

            <!-- Videos Scraped List -->
            <div>
                <h4 class="font-outfit text-lg font-bold text-white mb-4 flex items-center justify-between">
                    <span><i class="fa-solid fa-film text-brand-500 mr-2"></i>Daftar Video Discrape</span>
                    <span id="modal-video-count-tag" class="text-xs font-normal text-gray-400">0 Item</span>
                </h4>

                <div class="border border-slate-800 rounded-2xl overflow-hidden bg-slate-900/40">
                    <div class="max-h-72 overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left text-xs text-gray-300">
                            <thead class="bg-slate-900 sticky top-0 text-gray-400 border-b border-slate-800 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="py-3.5 px-4 w-12 text-center">#</th>
                                    <th class="py-3.5 px-4">Judul Video</th>
                                    <th class="py-3.5 px-4">Channel</th>
                                    <th class="py-3.5 px-4">Waktu Scrape</th>
                                    <th class="py-3.5 px-4 text-center">Tautan</th>
                                </tr>
                            </thead>
                            <tbody id="modal-videos-tbody" class="divide-y divide-slate-800/60">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-8 py-4 border-t border-slate-800 bg-slate-900/60 flex justify-end">
            <button onclick="closeModalDetail()" class="bg-slate-800 hover:bg-slate-700 text-white font-semibold px-6 py-2.5 rounded-xl text-xs transition">
                Tutup Detail
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    let appState = {
        token: localStorage.getItem('auth_token') || null,
        user: JSON.parse(localStorage.getItem('auth_user') || 'null'),
        histories: []
    };

    document.addEventListener('DOMContentLoaded', () => {
        if (!appState.token || !appState.user) {
            window.location.href = "{{ route('login') }}";
            return;
        }

        document.getElementById('user-display-name').innerText = appState.user?.name || appState.user?.username || 'User';
        document.getElementById('user-display-email').innerText = appState.user?.email || '';

        fetchHistory();
    });

    async function handleLogout() {
        try {
            await fetch('/api/auth/logout', {
                method: 'POST',
                headers: { 
                    'Authorization': `Bearer ${appState.token}`,
                    'Accept': 'application/json'
                }
            });
        } catch (err) {}

        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        showToast('Anda telah keluar', 'info');
        setTimeout(() => {
            window.location.href = "{{ route('login') }}";
        }, 500);
    }

    function switchTab(tab) {
        const btnAutomation = document.getElementById('tab-btn-automation');
        const btnHistory = document.getElementById('tab-btn-history');
        const contentAutomation = document.getElementById('tab-content-automation');
        const contentHistory = document.getElementById('tab-content-history');

        if (tab === 'automation') {
            btnAutomation.className = "pb-4 text-sm font-semibold border-b-2 border-brand-500 text-white flex items-center gap-2 transition";
            btnHistory.className = "pb-4 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-white flex items-center gap-2 transition";
            contentAutomation.classList.remove('hidden');
            contentHistory.classList.add('hidden');
        } else {
            btnHistory.className = "pb-4 text-sm font-semibold border-b-2 border-brand-500 text-white flex items-center gap-2 transition";
            btnAutomation.className = "pb-4 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-white flex items-center gap-2 transition";
            contentHistory.classList.remove('hidden');
            contentAutomation.classList.add('hidden');
            fetchHistory();
        }
    }

    function setDurationPreset(sec) {
        document.getElementById('automation-duration').value = sec;
    }

    async function handleStartAutomation(e) {
        e.preventDefault();
        const duration = parseInt(document.getElementById('automation-duration').value);
        const btn = document.getElementById('btn-start-automation');

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Menjalankan Otomatisasi...`;

        try {
            const res = await fetch('/api/automation/start', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${appState.token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ duration })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                showToast(`Otomatisasi selesai! ${data.data?.total_videos || 0} video discrape`, 'success');
                switchTab('history');
            } else {
                showToast(data.message || 'Gagal memulai otomatisasi', 'error');
            }
        } catch (err) {
            showToast('Terjadi kesalahan koneksi backend', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-rocket"></i> <span>Jalankan Otomatisasi Sekarang</span>`;
        }
    }

    async function fetchHistory() {
        try {
            const res = await fetch('/api/history', {
                headers: {
                    'Authorization': `Bearer ${appState.token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (res.ok && data.success) {
                appState.histories = data.data || [];
                renderStats();
                renderHistoryTable();
            }
        } catch (err) {
            console.error('Fetch history error:', err);
        }
    }

    function renderStats() {
        const histories = appState.histories;
        document.getElementById('stat-total-runs').innerText = histories.length;
        document.getElementById('history-badge').innerText = histories.length;

        const totalVideos = histories.reduce((sum, item) => sum + (item.videos?.length || item.total_videos || 0), 0);
        document.getElementById('stat-total-videos').innerText = totalVideos;

        if (histories.length > 0) {
            const last = histories[0];
            document.getElementById('stat-last-status').innerHTML = getStatusBadgeHTML(last.status);
        } else {
            document.getElementById('stat-last-status').innerText = 'Belum ada';
        }
    }

    function renderHistoryTable() {
        const tbody = document.getElementById('history-table-body');
        const histories = appState.histories;

        if (histories.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-500">
                        <i class="fa-solid fa-folder-open text-3xl mb-3 block text-gray-600"></i>
                        <span>Belum ada riwayat otomatisasi. Mulai otomatisasi pertama Anda!</span>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = histories.map(item => {
            const videoCount = item.videos?.length || item.total_videos || 0;
            const createdDate = new Date(item.created_at || item.started_at).toLocaleString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });

            return `
                <tr class="hover:bg-slate-900/50 transition">
                    <td class="py-4 px-6 font-bold text-white">#${item.id}</td>
                    <td class="py-4 px-6 text-gray-300">${createdDate}</td>
                    <td class="py-4 px-6 text-gray-300 font-medium">${item.duration} Detik</td>
                    <td class="py-4 px-6">${getStatusBadgeHTML(item.status)}</td>
                    <td class="py-4 px-6 font-semibold text-emerald-400">${videoCount} Video</td>
                    <td class="py-4 px-6 text-right">
                        <button onclick="openModalDetail(${item.id})"
                            class="bg-brand-500/10 hover:bg-brand-500 text-brand-300 hover:text-white px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-brand-500/30 transition flex items-center gap-1.5 ml-auto">
                            <i class="fa-regular fa-eye"></i>
                            <span>Lihat Detail</span>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function getStatusBadgeHTML(status) {
        switch(status) {
            case 'completed':
                return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                            <i class="fa-solid fa-circle-check"></i> Selesai
                        </span>`;
            case 'running':
                return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 border border-amber-500/30 text-amber-400">
                            <i class="fa-solid fa-spinner fa-spin"></i> Berjalan...
                        </span>`;
            case 'failed':
                return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/10 border border-rose-500/30 text-rose-400">
                            <i class="fa-solid fa-triangle-exclamation"></i> Gagal
                        </span>`;
            default:
                return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-gray-400 border border-slate-700">
                            <i class="fa-solid fa-clock"></i> Pending
                        </span>`;
        }
    }

    function openModalDetail(historyId) {
        const item = appState.histories.find(h => h.id === historyId);
        if (!item) return;

        document.getElementById('modal-history-id').innerText = `#${item.id}`;
        document.getElementById('modal-history-date').innerText = new Date(item.created_at || item.started_at).toLocaleString('id-ID', {
            weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });

        document.getElementById('modal-status-badge').innerHTML = getStatusBadgeHTML(item.status);
        document.getElementById('modal-duration').innerText = `${item.duration} Detik`;
        
        const videos = item.videos || item.results || [];
        document.getElementById('modal-total-videos').innerText = `${videos.length} Video`;
        document.getElementById('modal-video-count-tag').innerText = `${videos.length} Item`;

        const completedAt = item.completed_at ? new Date(item.completed_at).toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '-';
        document.getElementById('modal-completed-at').innerText = completedAt;

        const tbody = document.getElementById('modal-videos-tbody');
        if (videos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-500">
                        Tidak ada data video yang discrape dalam sesi ini.
                    </td>
                </tr>
            `;
        } else {
            tbody.innerHTML = videos.map((v, idx) => `
                <tr class="hover:bg-slate-900/60 transition">
                    <td class="py-3 px-4 text-center font-bold text-gray-400">${idx + 1}</td>
                    <td class="py-3 px-4 font-semibold text-white max-w-xs truncate" title="${v.title}">${v.title}</td>
                    <td class="py-3 px-4 text-gray-300">${v.channel || '-'}</td>
                    <td class="py-3 px-4 text-gray-400 font-mono text-[11px]">${v.scraped_at || '-'}</td>
                    <td class="py-3 px-4 text-center">
                        ${v.url ? `
                            <a href="${v.url}" target="_blank" class="inline-flex items-center gap-1 text-brand-500 hover:text-brand-400 font-semibold transition">
                                <i class="fa-brands fa-youtube"></i> Tonton
                            </a>
                        ` : '-'}
                    </td>
                </tr>
            `).join('');
        }

        const modal = document.getElementById('modal-detail');
        const panel = document.getElementById('modal-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            panel.classList.remove('scale-95');
            panel.classList.add('scale-100');
        });
    }

    function closeModalDetail() {
        const modal = document.getElementById('modal-detail');
        const panel = document.getElementById('modal-panel');

        modal.classList.add('opacity-0');
        panel.classList.remove('scale-100');
        panel.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
