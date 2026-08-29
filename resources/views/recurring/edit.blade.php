<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('recurring-rules.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400">&larr; Kembali ke Tagihan Berulang</a>
            <span class="text-gray-300">/</span>
            <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Edit Aturan Tagihan Berulang') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xs">
                <form method="POST" action="{{ route('recurring-rules.update', $recurringRule) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="catatan" value="Nama Tagihan / Keterangan" />
                        <x-text-input id="catatan" name="catatan" type="text" class="mt-1 block w-full" :value="old('catatan', $recurringRule->catatan)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="account_id" value="Akun Sumber / Tujuan" />
                            <select id="account_id" name="account_id" required class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                            <select id="category_id" name="category_id" required class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $recurringRule->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }} ({{ ucfirst($cat->tipe) }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="jumlah" value="Nominal Transaksi (Rp)" />
                            <x-text-input id="jumlah" name="jumlah" type="number" min="1" step="1" class="mt-1 block w-full" :value="old('jumlah', (int)$recurringRule->jumlah)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('jumlah')" />
                        </div>

                        <div>
                            <x-input-label for="frekuensi" value="Frekuensi Perulangan" />
                            <select id="frekuensi" name="frekuensi" required class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                        <x-text-input id="tanggal_berikutnya" name="tanggal_berikutnya" type="date" class="mt-1 block w-full" :value="old('tanggal_berikutnya', \Carbon\Carbon::parse($recurringRule->tanggal_berikutnya)->format('Y-m-d'))" required />
                        <x-input-error class="mt-2" :messages="$errors->get('tanggal_berikutnya')" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $recurringRule->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="is_active" class="text-xs font-semibold text-gray-700 dark:text-gray-300">Aktifkan otomatisasi tagihan ini</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('recurring-rules.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition">Batal</a>
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
