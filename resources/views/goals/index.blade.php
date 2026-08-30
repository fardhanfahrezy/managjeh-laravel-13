<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-black text-2xl text-slate-900 dark:text-white leading-tight">
                    {{ __('Tujuan Finansial (Goals)') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Lacak tabungan impian, tetapkan target dana, dan setor tabungan langsung dari akun dompet Anda.
                </p>
            </div>
            <a href="{{ route('goals.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Buat Goal Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-4" x-data="{ depositModal: false, withdrawModal: false, activeGoal: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Target Tabungan</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1 tracking-tight">
                        Rp {{ number_format($totalTarget, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Terkumpul</span>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 tracking-tight">
                        Rp {{ number_format($totalProgres, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pencapaian Keseluruhan</span>
                    <div class="flex items-center gap-3 mt-1">
                        <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ $overallPercentage }}%
                        </p>
                        <div class="flex-1 bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $overallPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Goals Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($goals as $goal)
                    @php
                        $pct = $goal->percentage();
                        $isComplete = $goal->isCompleted();
                        $days = $goal->daysLeft();
                    @endphp
                    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border {{ $isComplete ? 'border-emerald-300 dark:border-emerald-800/80 bg-emerald-50/10' : 'border-slate-200/80 dark:border-slate-800' }} shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">{{ $goal->nama_goal }}</h3>
                                    @if($goal->catatan)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $goal->catatan }}</p>
                                    @endif
                                </div>
                                @if($isComplete)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase tracking-wider">
                                        ✓ Tercapai
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 text-[10px] font-black uppercase tracking-wider">
                                        {{ $pct }}%
                                    </span>
                                @endif
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-5 space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-slate-700 dark:text-slate-300">Rp {{ number_format($goal->progres, 0, ',', '.') }}</span>
                                    <span class="text-slate-400 font-medium">Target: Rp {{ number_format($goal->target, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                                    <div class="{{ $isComplete ? 'bg-emerald-500' : 'bg-blue-600' }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                                    <span>Sisa: Rp {{ number_format($goal->remainingAmount(), 0, ',', '.') }}</span>
                                    @if($days !== null)
                                        <span class="{{ $days < 0 ? 'text-red-500 font-bold' : ($days <= 30 ? 'text-amber-500 font-bold' : 'text-slate-400') }}">
                                            @if($days < 0)
                                                Lewat {{ abs($days) }} hari
                                            @elseif($days === 0)
                                                Jatuh tempo hari ini
                                            @else
                                                {{ $days }} hari lagi
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <button @click="activeGoal = { id: {{ $goal->id }}, nama: '{{ addslashes($goal->nama_goal) }}', maxWithdraw: {{ $goal->progres }} }; depositModal = true" class="px-3.5 py-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:hover:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-xl text-xs font-bold transition">
                                    + Setor Dana
                                </button>
                                @if((float)$goal->progres > 0)
                                    <button @click="activeGoal = { id: {{ $goal->id }}, nama: '{{ addslashes($goal->nama_goal) }}', maxWithdraw: {{ $goal->progres }} }; withdrawModal = true" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition">
                                        Tarik
                                    </button>
                                @endif
                            </div>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('goals.edit', $goal) }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-xl transition" title="Edit Goal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Hapus goal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-400 hover:text-red-600 rounded-xl transition" title="Hapus Goal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-12 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum Ada Tujuan Finansial</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                            Mulai rencanakan pembelian rumah, dana darurat, liburan, atau gadget impian Anda.
                        </p>
                        <a href="{{ route('goals.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-5 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl shadow-md">
                            + Buat Goal Sekarang
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Deposit Modal -->
            <div x-show="depositModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
                <div @click.away="depositModal = false" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-7 space-y-4 animate-glide">
                    <div class="flex items-center justify-between">
                        <h3 class="font-black text-lg text-slate-900 dark:text-white">Setor ke Goal: <span x-text="activeGoal?.nama" class="text-blue-600 dark:text-blue-400"></span></h3>
                        <button @click="depositModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                    </div>

                    <form :action="'/goals/' + activeGoal?.id + '/deposit'" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block uppercase text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Ambil dari Akun / Dompet</label>
                            <select name="account_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-sm font-semibold focus:border-blue-600 focus:ring-2 focus:ring-blue-600">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->nama_akun }} (Saldo: Rp {{ number_format($acc->saldo, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block uppercase text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nominal Setoran (Rp)</label>
                            <input type="number" name="jumlah" min="1" step="1" required placeholder="Contoh: 500000" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-sm font-bold focus:border-blue-600 focus:ring-2 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block uppercase text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Catatan (Opsional)</label>
                            <input type="text" name="catatan" placeholder="Contoh: Tabungan gaji bulan ini" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="depositModal = false" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">Batal</button>
                            <button type="submit" class="px-5 py-2.5 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow-md transition">Konfirmasi Setor</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Withdraw Modal -->
            <div x-show="withdrawModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
                <div @click.away="withdrawModal = false" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-7 space-y-4 animate-glide">
                    <div class="flex items-center justify-between">
                        <h3 class="font-black text-lg text-slate-900 dark:text-white">Tarik dari Goal: <span x-text="activeGoal?.nama" class="text-amber-500"></span></h3>
                        <button @click="withdrawModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                    </div>

                    <form :action="'/goals/' + activeGoal?.id + '/withdraw'" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block uppercase text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Transfer ke Akun / Dompet</label>
                            <select name="account_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-sm font-semibold focus:border-blue-600 focus:ring-2 focus:ring-blue-600">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->nama_akun }} (Saldo: Rp {{ number_format($acc->saldo, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block uppercase text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nominal Penarikan (Rp)</label>
                            <input type="number" name="jumlah" min="1" step="1" :max="activeGoal?.maxWithdraw" required placeholder="Contoh: 250000" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-sm font-bold focus:border-blue-600 focus:ring-2 focus:ring-blue-600">
                            <span class="text-[11px] text-slate-400 mt-1 block font-medium">Maksimal penarikan: Rp <span class="font-bold text-slate-700 dark:text-slate-300" x-text="Number(activeGoal?.maxWithdraw || 0).toLocaleString('id-ID')"></span></span>
                        </div>

                        <div>
                            <label class="block uppercase text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Catatan (Opsional)</label>
                            <input type="text" name="catatan" placeholder="Contoh: Tarik untuk kebutuhan darurat" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="withdrawModal = false" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">Batal</button>
                            <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-black rounded-xl shadow-md transition">Konfirmasi Tarik</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

