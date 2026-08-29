<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.index') }}" class="p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Catat Transaksi</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Pilih tipe transaksi untuk mencatat pemasukan, pengeluaran, transfer, atau pecah kategori.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ 
        tipe: '{{ old('tipe', $defaultType) }}',
        isSplit: false,
        splits: [
            { category_id: '', jumlah: '', catatan: '' },
            { category_id: '', jumlah: '', catatan: '' }
        ],
        addSplit() {
            this.splits.push({ category_id: '', jumlah: '', catatan: '' });
        },
        removeSplit(index) {
            if (this.splits.length > 1) {
                this.splits.splice(index, 1);
            }
        },
        get totalSplit() {
            return this.splits.reduce((sum, item) => sum + (parseFloat(item.jumlah) || 0), 0);
        }
    }">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Tipe Transaksi Tab Selector -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tipe Transaksi</label>
                    <div class="grid grid-cols-3 gap-2 p-1 rounded-xl bg-gray-100 dark:bg-gray-900">
                        <button type="button" @click="tipe = 'expense'" :class="tipe === 'expense' ? 'bg-white dark:bg-gray-800 text-rose-600 dark:text-rose-400 font-bold shadow-xs' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 font-medium'" class="py-2.5 px-3 rounded-lg text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Pengeluaran</span>
                        </button>
                        <button type="button" @click="tipe = 'income'; isSplit = false" :class="tipe === 'income' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 font-medium'" class="py-2.5 px-3 rounded-lg text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Pemasukan</span>
                        </button>
                        <button type="button" @click="tipe = 'transfer'; isSplit = false" :class="tipe === 'transfer' ? 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 font-bold shadow-xs' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 font-medium'" class="py-2.5 px-3 rounded-lg text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Transfer</span>
                        </button>
                    </div>
                    <input type="hidden" name="tipe" :value="tipe" />
                    <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
                </div>

                <!-- Jumlah / Nominal -->
                <div>
                    <label for="jumlah" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nominal Total Transaksi (Rp)</label>
                    <div class="relative mt-1.5 rounded-xl shadow-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-gray-400 sm:text-sm font-bold">Rp</span>
                        </div>
                        <input type="number" step="0.01" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" placeholder="0.00" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white pl-12 pr-4 shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-base font-semibold" required />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('jumlah')" />
                </div>

                <!-- Akun Asal -->
                <div>
                    <label for="account_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <span x-show="tipe === 'transfer'">Akun Sumber / Asal</span>
                        <span x-show="tipe !== 'transfer'">Akun Dompet</span>
                    </label>
                    <select id="account_id" name="account_id" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        <option value="">-- Pilih Akun --</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('account_id', $defaultAccountId) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->nama_akun }} (Saldo: Rp {{ number_format($acc->saldo, 2, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('account_id')" />
                </div>

                <!-- Akun Tujuan (Khusus Transfer) -->
                <div x-show="tipe === 'transfer'" style="display: none;">
                    <label for="destination_account_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Akun Tujuan</label>
                    <select id="destination_account_id" name="destination_account_id" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">-- Pilih Akun Penerima --</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('destination_account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->nama_akun }} (Saldo: Rp {{ number_format($acc->saldo, 2, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('destination_account_id')" />
                </div>

                <!-- Split Transaction Toggle (Khusus Expense) -->
                <div x-show="tipe === 'expense'" class="pt-2">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                        <div>
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Pecah Transaksi (Split Categories)</span>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Bagi total belanja ini ke beberapa kategori berbeda</p>
                        </div>
                        <input type="checkbox" x-model="isSplit" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Single Category Select (When not split and not transfer) -->
                <div x-show="tipe !== 'transfer' && !isSplit">
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Kategori</label>
                    
                    <!-- Expense Categories Select -->
                    <select x-show="tipe === 'expense'" id="category_id_expense" name="category_id" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-sm" :disabled="tipe !== 'expense' || isSplit">
                        <option value="">-- Pilih Kategori Pengeluaran --</option>
                        @foreach ($expenseCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Income Categories Select -->
                    <select x-show="tipe === 'income'" id="category_id_income" name="category_id" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-sm" :disabled="tipe !== 'income'" style="display: none;">
                        <option value="">-- Pilih Kategori Pemasukan --</option>
                        @foreach ($incomeCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                </div>

                <!-- Split Categories Subform -->
                <div x-show="isSplit && tipe === 'expense'" class="space-y-3 p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 tracking-wider">Rincian Pembagian Kategori</span>
                        <button type="button" @click="addSplit()" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">+ Tambah Baris</button>
                    </div>

                    <template x-for="(split, index) in splits" :key="index">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center bg-white dark:bg-gray-800 p-2.5 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="sm:col-span-5">
                                <select :name="'splits[' + index + '][category_id]'" x-model="split.category_id" required class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($expenseCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-4">
                                <input type="number" :name="'splits[' + index + '][jumlah]'" x-model="split.jumlah" min="1" step="0.01" required placeholder="Nominal (Rp)" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
                            </div>
                            <div class="sm:col-span-2">
                                <input type="text" :name="'splits[' + index + '][catatan]'" x-model="split.catatan" placeholder="Item" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
                            </div>
                            <div class="sm:col-span-1 text-center">
                                <button type="button" @click="removeSplit(index)" class="text-rose-400 hover:text-rose-600 text-sm font-bold">&times;</button>
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center justify-between text-xs pt-2">
                        <span class="text-gray-500">Total Rincian: Rp <span x-text="Number(totalSplit).toLocaleString('id-ID')"></span></span>
                    </div>
                </div>

                <!-- Tanggal Transaksi -->
                <div>
                    <label for="tanggal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tanggal Transaksi</label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-sm" required />
                    <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
                </div>

                <!-- Catatan / Deskripsi -->
                <div>
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Catatan (Opsional)</label>
                    <textarea id="catatan" name="catatan" rows="3" placeholder="Contoh: Belanja bulanan di supermarket..." class="mt-1.5 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('catatan') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                </div>

                <!-- Upload Lampiran Struk / Nota -->
                <div>
                    <label for="attachment" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Lampiran Struk / Bukti (Opsional)</label>
                    <input type="file" id="attachment" name="attachment" accept="image/*,.pdf" class="mt-1.5 block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-300" />
                    <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                    <p class="text-xs text-gray-400 mt-1">Format gambar (JPG, PNG) atau PDF. Maksimal 5MB.</p>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-xs transition">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
