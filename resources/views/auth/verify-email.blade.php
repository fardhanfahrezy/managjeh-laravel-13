<x-guest-layout>
    <div class="mb-4 text-xs font-medium text-slate-600 dark:text-slate-400 leading-relaxed">
        {{ __('Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-bold text-xs text-emerald-600 dark:text-emerald-400">
            {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda daftarkan.') }}
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none focus:underline transition">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>

