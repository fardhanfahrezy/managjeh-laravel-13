<?php

namespace App\Http\Controllers;

use App\Services\CsvExportImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionExportImportController extends Controller
{
    public function export(Request $request, CsvExportImportService $service): StreamedResponse
    {
        return $service->export($request->user(), $request->all());
    }

    public function template(CsvExportImportService $service): StreamedResponse
    {
        return $service->getTemplate();
    }

    public function import(Request $request, CsvExportImportService $service): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $result = $service->import($request->user(), $request->file('file'));

        return redirect()->route('transactions.index')->with('success', "Berhasil mengimpor {$result['imported']} transaksi ke dalam akun Anda.");
    }
}
