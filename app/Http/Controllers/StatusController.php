<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatusDarurat;

class StatusController extends Controller
{
    /**
     * Simpan data status darurat
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        // Simpan ke database
        StatusDarurat::create([
            'status' => $request->status,
        ]);

        // Redirect kembali dengan pesan sukses
        return back()->with('success', 'Status Darurat berhasil ditambahkan!');
    }

    /**
     * Hapus data status darurat
     */
    public function destroy($id_status_darurat)
    {
        // Temukan data dan hapus
        $status = StatusDarurat::findOrFail($id_status_darurat);
        $status->delete();

        return back()->with('success', 'Status Darurat berhasil dihapus!');
    }
}
