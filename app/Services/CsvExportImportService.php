<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportImportService
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /**
     * Export user transactions to CSV.
     *
     * @param  array<string, mixed>  $filters
     */
    public function export(User $user, array $filters = []): StreamedResponse
    {
        $filename = 'transaksi_'.Carbon::now()->format('Ymd_His').'.csv';

        $query = Transaction::with(['account', 'category', 'destinationAccount'])
            ->where('user_id', $user->id)
            ->whereNull('deleted_at');

        if (! empty($filters['tipe'])) {
            $query->where('tipe', $filters['tipe']);
        }
        if (! empty($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (! empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }

        $transactions = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($handle, ['Tanggal', 'Akun', 'Tipe', 'Kategori', 'Jumlah', 'Catatan']);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->tanggal ? Carbon::parse($t->tanggal)->format('Y-m-d') : '',
                    $t->account?->nama_akun ?? '-',
                    $t->tipe,
                    $t->category?->nama ?? '-',
                    $t->jumlah,
                    $t->catatan ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Download standard CSV template.
     */
    public function getTemplate(): StreamedResponse
    {
        $filename = 'template_import_transaksi.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Tanggal', 'Akun', 'Tipe', 'Kategori', 'Jumlah', 'Catatan']);
            fputcsv($handle, [Carbon::now()->format('Y-m-d'), 'Kas / Dompet Tunai', 'expense', 'Makanan & Minuman', '35000', 'Makan Siang']);
            fputcsv($handle, [Carbon::now()->format('Y-m-d'), 'Kas / Dompet Tunai', 'income', 'Gaji', '5000000', 'Gaji Bulanan']);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Import transactions from CSV file with exact-match tenant validation.
     *
     * @return array{imported: int, errors: array<int, string>}
     *
     * @throws ValidationException
     */
    public function import(User $user, UploadedFile $file): array
    {
        $accounts = Account::where('user_id', $user->id)->get();
        $categories = Category::where('user_id', $user->id)->get();

        // Exact match maps (case-insensitive trim)
        $accountMap = [];
        foreach ($accounts as $acc) {
            $accountMap[strtolower(trim($acc->nama_akun))] = $acc;
        }

        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[strtolower(trim($cat->nama))] = $cat;
        }

        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            throw ValidationException::withMessages(['file' => 'Gagal membaca file CSV yang diunggah.']);
        }

        $header = fgetcsv($handle);
        if (! $header || count($header) < 5) {
            fclose($handle);
            throw ValidationException::withMessages(['file' => 'Format file CSV tidak valid. Pastikan header sesuai template.']);
        }

        $rowsToInsert = [];
        $errors = [];
        $lineNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            $rawTanggal = trim($row[0] ?? '');
            $rawAkun = trim($row[1] ?? '');
            $rawTipe = strtolower(trim($row[2] ?? ''));
            $rawKategori = trim($row[3] ?? '');
            $rawJumlah = str_replace([',', ' '], '', trim($row[4] ?? ''));
            $rawCatatan = trim($row[5] ?? '');

            // 1. Validate Date
            try {
                $tanggal = Carbon::parse($rawTanggal)->format('Y-m-d');
            } catch (\Throwable) {
                $errors[] = "Baris {$lineNumber}: Format tanggal '{$rawTanggal}' tidak valid. Gunakan format YYYY-MM-DD.";

                continue;
            }

            // 2. Validate Account
            $accountKey = strtolower($rawAkun);
            if (! isset($accountMap[$accountKey])) {
                $errors[] = "Baris {$lineNumber}: Akun '{$rawAkun}' tidak ditemukan pada daftar akun Anda.";

                continue;
            }
            $account = $accountMap[$accountKey];

            // 3. Validate Type
            if (! in_array($rawTipe, ['income', 'expense'], true)) {
                $errors[] = "Baris {$lineNumber}: Tipe transaksi '{$rawTipe}' tidak valid. Harus 'income' atau 'expense'.";

                continue;
            }

            // 4. Validate Category
            $categoryKey = strtolower($rawKategori);
            if (! isset($categoryMap[$categoryKey])) {
                $errors[] = "Baris {$lineNumber}: Kategori '{$rawKategori}' tidak ditemukan pada daftar kategori Anda.";

                continue;
            }
            $category = $categoryMap[$categoryKey];

            // Category type match
            if ($category->tipe !== $rawTipe) {
                $errors[] = "Baris {$lineNumber}: Kategori '{$category->nama}' bertipe {$category->tipe}, tidak sesuai dengan tipe baris {$rawTipe}.";

                continue;
            }

            // 5. Validate Amount
            if (! is_numeric($rawJumlah) || (float) $rawJumlah <= 0) {
                $errors[] = "Baris {$lineNumber}: Nominal jumlah '{$rawJumlah}' tidak valid. Harus angka positif.";

                continue;
            }

            $rowsToInsert[] = [
                'account_id' => $account->id,
                'category_id' => $category->id,
                'tipe' => $rawTipe,
                'jumlah' => (float) $rawJumlah,
                'tanggal' => $tanggal,
                'catatan' => $rawCatatan ?: null,
            ];
        }

        fclose($handle);

        if (! empty($errors)) {
            throw ValidationException::withMessages(['file' => $errors]);
        }

        if (empty($rowsToInsert)) {
            throw ValidationException::withMessages(['file' => 'Tidak ada data transaksi yang dapat diimpor dari file CSV.']);
        }

        // Execute batch insert atomically
        $importedCount = 0;
        DB::transaction(function () use ($user, $rowsToInsert, &$importedCount) {
            foreach ($rowsToInsert as $data) {
                $this->transactionService->createTransaction($user, [
                    'account_id' => $data['account_id'],
                    'destination_account_id' => null,
                    'category_id' => $data['category_id'],
                    'jumlah' => $data['jumlah'],
                    'tipe' => $data['tipe'],
                    'tanggal' => $data['tanggal'],
                    'catatan' => $data['catatan'],
                ]);
                $importedCount++;
            }
        });

        return [
            'imported' => $importedCount,
            'errors' => [],
        ];
    }
}
