<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('recurring-rules.index') }}" aria-label="Kembali ke daftar tagihan berulang" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Edit Aturan Tagihan Berulang</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Perbarui informasi aturan tagihan berulang.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('recurring-rules.update', $recurringRule) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nama Tagihan -->
                <div>
                    <x-input-label for="catatan" value="Nama Tagihan / Keterangan" />
                    <x-text-input id="catatan" name="catatan" type="text" class="mt-1.5 block w-full font-semibold" :value="old('catatan', $recurringRule->catatan)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="account_id" value="Akun Sumber / Tujuan" />
                        <select id="account_id" name="account_id" required class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm font-semibold focus:border-blue-600 focus:ring-2 focus:ring-blue-600">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('account_id', $recurringRule->account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->nama_akun }} (Rp {{ number_format($account->saldo, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('account_id')" />
                    </div>

                    <div>
                        <x-input-label for="category_id" value="Kategori" />
                        <select id="category_id" name="category_id" required class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm font-semibold focus:border-blue-600 focus:ring-2 focus:ring-blue-600">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $recurringRule->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama }} ({{ ucfirst($cat->tipe) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="jumlah" value="Nominal Transaksi (Rp)" />
                        <div class="relative mt-1.5 rounded-xl shadow-xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-slate-400 sm:text-sm font-bold">Rp</span>
                            </div>
                            <x-text-input id="jumlah" name="jumlah" type="number" min="1" step="1" class="block w-full pl-12 font-bold" :value="old('jumlah', (int)$recurringRule->jumlah)" required />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('jumlah')" />
                    </div>

                    <div>
                        <x-input-label for="frekuensi" value="Frekuensi Perulangan" />
                        <select id="frekuensi" name="frekuensi" required class="mt-1.5 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm font-semibold focus:border-blue-600 focus:ring-2 focus:ring-blue-600">
                            <option value="daily" {{ old('frekuensi', $recurringRule->frekuensi) === 'daily' ? 'selected' : '' }}>Setiap Hari (Harian)</option>
                            <option value="weekly" {{ old('frekuensi', $recurringRule->frekuensi) === 'weekly' ? 'selected' : '' }}>Setiap Minggu (Mingguan)</option>
                            <option value="monthly" {{ old('frekuensi', $recurringRule->frekuensi) === 'monthly' ? 'selected' : '' }}>Setiap Bulan (Bulanan)</option>
                            <option value="yearly" {{ old('frekuensi', $recurringRule->frekuensi) === 'yearly' ? 'selected' : '' }}>Setiap Tahun (Tahunan)</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('frekuensi')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="tanggal_berikutnya" value="Jatuh Tempo Berikutnya" />
                    <x-text-input id="tanggal_berikutnya" name="tanggal_berikutnya" type="date" class="mt-1.5 block w-full font-semibold" :value="old('tanggal_berikutnya', \Carbon\Carbon::parse($recurringRule->tanggal_berikutnya)->format('Y-m-d'))" required />
                    <x-input-error class="mt-2" :messages="$errors->get('tanggal_berikutnya')" />
                </div>

                <div class="flex items-center gap-2.5">
                    <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $recurringRule->is_active) ? 'checked' : '' }} class="rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-blue-600 focus:ring-blue-600">
                    <label for="is_active" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Aktifkan otomatisasi tagihan ini</label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('recurring-rules.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <x-primary-button>
                        Simpan Perubahan
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
