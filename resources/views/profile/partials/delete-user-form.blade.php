<section class="space-y-5">
    <header>
        <h2 class="text-base font-bold text-red-600 dark:text-red-400">
            {{ __('Hapus Akun Permanen') }}
        </h2>

        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
            {{ __('Setelah akun Anda dihapus, semua data keuangan, transaksi, dan akun dompet akan dihapus secara permanen.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Hapus Akun Saya') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-7 space-y-4">
            @csrf
            @method('delete')

            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                {{ __('Apakah Anda yakin ingin menghapus akun ini?') }}
            </h3>

            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                {{ __('Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi akun Anda untuk mengonfirmasi penghapusan permanen.') }}
            </p>

            <div class="mt-4">
                <x-input-label for="password" value="{{ __('Kata Sandi Konfirmasi') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Masukkan kata sandi...') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('Ya, Hapus Akun') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>

