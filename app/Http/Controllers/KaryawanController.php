<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\StatusDarurat;
use App\Models\JenisBencana;
use App\Models\KlasifikasiBencana; // <-- tambahkan ini
use App\Models\TemplateTTD;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::all();
        $status = StatusDarurat::all();
        $jenis = JenisBencana::all();
        $klasifikasi = KlasifikasiBencana::all(); // <-- ambil data klasifikasi
        $template = TemplateTTD::first();
        $ttd = $template ? $template->karyawan : null;

        return view('karyawan.index', compact('karyawan', 'status', 'jenis', 'klasifikasi', 'template', 'ttd'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip_pengawas'       => 'required|unique:tb_pengawas,nip_pengawas',
            'nama_pengawas'      => 'required',
            'jabatan'            => 'required',
            'tugas'              => 'required',
            'id_status_darurat'  => 'required|exists:tb_status_darurat,id_status_darurat',
            'id_jenis_bencana'   => 'required|exists:tb_jenis_bencana,id_jenis_bencana',
        ]);

        Karyawan::create([
            'nip_pengawas'      => $request->nip_pengawas,
            'nama_pengawas'     => $request->nama_pengawas,
            'jabatan'           => $request->jabatan,
            'tugas'             => $request->tugas,
            'id_status_darurat' => $request->id_status_darurat,
            'id_jenis_bencana'  => $request->id_jenis_bencana,
        ]);

        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function storeTemplate(Request $request)
{
    // Tidak perlu validasi unik
    $request->validate([
        'nip_pengawas' => 'required',
        'nama_pengawas' => 'required',
        'jabatan' => 'required',
    ]);

    // Hapus template lama agar tidak terjadi duplicate
    TemplateTTD::truncate();

    // Simpan template baru
    TemplateTTD::create([
        'nip_pengawas'  => $request->nip_pengawas,
        'nama_pengawas' => $request->nama_pengawas,
        'jabatan'       => $request->jabatan,
        'jenis_template' => 'gelombang',
        'is_active' => 1,
    ]);

    return redirect()->back()->with('success', 'Template TTD berhasil disimpan!');
}

    public function destroy($nip_pengawas)
    {
        Karyawan::findOrFail($nip_pengawas)->delete();
        return redirect()->back()->with('success', 'Karyawan berhasil dihapus!');
    }

    public function destroyStatus($id_status_darurat)
{
    StatusDarurat::findOrFail($id_status_darurat)->delete();

    return redirect()->back()->with('success', 'Status darurat berhasil dihapus!');
}

public function destroyJenis($id_jenis_bencana)
{
    JenisBencana::findOrFail($id_jenis_bencana)->delete();

    return redirect()->back()->with('success', 'Jenis bencana berhasil dihapus!');
}

}
