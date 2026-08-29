<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Pusat Notifikasi') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Peringatan limit budget, tagihan jatuh tempo, dan informasi sistem keuangan Anda.
                </p>
            </div>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-xl shadow-xs transition">
                        ✓ Tandai Semua Telah Dibaca
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs divide-y divide-gray-100 dark:divide-gray-750 overflow-hidden">
                @forelse($notifications as $notif)
                    <div class="p-5 {{ $notif->read_at ? 'opacity-70' : 'bg-emerald-50/30 dark:bg-emerald-950/20' }} hover:bg-gray-50 dark:hover:bg-gray-700/40 transition flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $notif->read_at ? 'bg-gray-100 dark:bg-gray-700 text-gray-400' : 'bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-300' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-white">{{ $notif->data['title'] ?? 'Notifikasi' }}</h4>
                                    @if(!$notif->read_at)
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">{{ $notif->data['message'] ?? '' }}</p>
                                <div class="flex items-center gap-4 mt-2 text-[11px] text-gray-400">
                                    <span>{{ $notif->created_at->diffForHumans() }}</span>
                                    @if(isset($notif->data['url']))
                                        <a href="{{ $notif->data['url'] }}" class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">Lihat Detail &rarr;</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if(!$notif->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-xs text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 rounded-lg transition font-medium">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-gray-300 hover:text-rose-500 transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-400 text-sm">
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
