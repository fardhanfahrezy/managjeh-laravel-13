<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-black text-2xl text-slate-900 dark:text-white tracking-tight leading-tight">
                    {{ __('Pusat Notifikasi') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Peringatan limit budget, tagihan jatuh tempo, dan informasi sistem keuangan Anda.
                </p>
            </div>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl shadow-xs transition">
                        ✓ Tandai Semua Telah Dibaca
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm divide-y divide-slate-100 dark:divide-slate-800 overflow-hidden">
                @forelse($notifications as $notif)
                    <div class="p-5 {{ $notif->read_at ? 'opacity-70' : 'bg-blue-50/20 dark:bg-blue-950/20' }} hover:bg-slate-50 dark:hover:bg-slate-800/40 transition flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $notif->read_at ? 'bg-slate-100 dark:bg-slate-800 text-slate-400' : 'bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="font-bold text-sm text-slate-900 dark:text-white">{{ $notif->data['title'] ?? 'Notifikasi' }}</h2>
                                    @if(!$notif->read_at)
                                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">{{ $notif->data['message'] ?? '' }}</p>
                                <div class="flex items-center gap-4 mt-2 text-[11px] text-slate-400 font-medium">
                                    <span>{{ $notif->created_at->diffForHumans() }}</span>
                                    @if(isset($notif->data['url']))
                                        <a href="{{ $notif->data['url'] }}" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">Lihat Detail &rarr;</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if(!$notif->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                    @csrf
                                    <button type="submit" aria-label="Tandai dibaca" class="px-2.5 py-1 text-xs text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/60 rounded-lg transition font-bold">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="Hapus notifikasi" class="p-1.5 text-slate-300 hover:text-rose-500 rounded-lg transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 dark:text-slate-500 text-xs font-medium">
                        Tidak ada notifikasi saat ini.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
