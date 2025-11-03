<?php

namespace App\Http\Controllers;

use App\Models\Kejadian;
use App\Models\JenisBencana;
use App\Models\KategoriKorban;
use App\Models\KategoriUmur;
use App\Models\Korban;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KejadianController extends Controller
{
    public function index()
    {
        $kejadian = Kejadian::with(['namaKejadian', 'kecamatan'])->get(); 
        return view('formKejadian', compact('kejadian'));
    }

    public function create()
    {
        $kejadianList   = Kejadian::all();
        $jenisBencana   = JenisBencana::all();
        $kategoriKorban = KategoriKorban::all();
        $kategoriUmur   = KategoriUmur::all();
        $kecamatan = Kecamatan::all();
        $namaKejadian = \App\Models\NamaKejadian::all();
        $statusDarurat = \App\Models\StatusDarurat::all();
        $pengawas = \App\Models\Pengawas::all();

        return view('create', compact(
            'kejadianList',
            'jenisBencana',
            'kategoriKorban',
            'kategoriUmur',
            'namaKejadian',
            'statusDarurat',
            'pengawas',
            'kecamatan'
        ));
    }

    /**
     * Simpan data kejadian ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jenis_bencana' => 'required|integer',
            'id_nama_kejadian' => 'required|integer',
            'tanggal'          => 'required|date',
            'waktu'            => 'required',
            'id_provinsi'      => 'required|integer',
            'id_kabupaten'     => 'required|integer',
            'id_kecamatan'     => 'required|integer',
            'id_desa'          => 'required|integer',
            'longitude'        => 'required|string|max:255',
            'latitude'         => 'required|string|max:255',
            'penyebab'         => 'required|string',
            'kronologi'        => 'required|string',
            'deskripsi'        => 'required|string',
            'sumber'           => 'required|string|max:255',
            'kondisi_mutakhir' => 'required|string',
            'id_status_darurat'=> 'required|integer',
            'upaya'            => 'required|string',
            'dokumentasi'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'sebaran_dampak'   => 'required|string',
            'kib'              => 'required|string',
            'nip_pengawas'     => 'required|string',  
        ]);

        DB::transaction(function () use ($request, $validated) {
            // Simpan file dokumentasi
            if ($request->hasFile('dokumentasi')) {
                $validated['dokumentasi'] = $request->file('dokumentasi')->store('dokumentasi', 'public');
            }

            // Simpan data kejadian
            $kejadian = Kejadian::create($validated);

            /**
             * Simpan data korban (banyak kategori sekaligus)
             * Format input form harus seperti ini:
             * korban[kategori_korban_id][kategori_umur_id][L]
             * korban[kategori_korban_id][kategori_umur_id][P]
             */
            if ($request->has('korban')) {
    foreach ($request->korban as $dataKorban) {
        Korban::create([
            'id_kejadian'        => $kejadian->id_kejadian,
            'id_kategori_korban' => (int) $dataKorban['id_kategori_korban'],
            'id_kategori_umur'   => (int) $dataKorban['id_kategori_umur'],
            'L'                  => (int) ($dataKorban['L'] ?? 0),
            'P'                  => (int) ($dataKorban['P'] ?? 0),
        ]);
    }

// Simpan data rumah
\App\Models\Rumah::create([
    'id_kejadian' => $kejadian->id_kejadian,
    'rmh_rr'      => (int) ($request->rmh_rr ?? 0),
    'rmh_rs'      => (int) ($request->rmh_rs ?? 0),
    'rmh_rb'      => (int) ($request->rmh_rb ?? 0),
    'terendam'    => (int) ($request->terendam ?? 0),
    'kerugian'    => 0,
]);

// === Simpan data sosek ===
\App\Models\Sosek::create([
    'id_kejadian' => $kejadian->id_kejadian,
    'id_jenis_kerusakan_sosek' => $request->id_jenis_kerusakan_sosek ?? 1,
    'sosek_rr'          => (int) ($request->sosek_rr ?? 0),
    'sosek_rs'          => (int) ($request->sosek_rs ?? 0),
    'sosek_rb'          => (int) ($request->sosek_rb ?? 0),
    'sosek_terendam'    => (int) ($request->sosek_terendam ?? 0),
    'kerugian'          => 0,
]);

// === Simpan data sarpras ===
// Sarpras
\App\Models\Sarpras::create([
    'id_kejadian' => $kejadian->id_kejadian,
    'id_jenis_kerusakan_sarpras' => $request->id_jenis_kerusakan_sarpras ?? 1,
    'sarpras_rr'       => (int) ($request->sarpras_rr ?? 0),
    'sarpras_rs'       => (int) ($request->sarpras_rs ?? 0),
    'sarpras_rb'       => (int) ($request->sarpras_rb ?? 0),
    'sarpras_terendam' => (int) ($request->sarpras_terendam ?? 0),
    'taksiran'         => 0,
]);

// Pelayanan
\App\Models\Pelayanan::create([
    'id_kejadian' => $kejadian->id_kejadian,
    'id_jenis_kerusakan_pelayanandasar' => $request->id_jenis_kerusakan_pelayanandasar ?? 1,
    'pelayanan_rr'       => (int) ($request->pelayanan_rr ?? 0),
    'pelayanan_rs'       => (int) ($request->pelayanan_rs ?? 0),
    'pelayanan_rb'       => (int) ($request->pelayanan_rb ?? 0),
    'pelayanan_terendam' => (int) ($request->pelayanan_terendam ?? 0),
    'taksiran'           => 0,
]);

}

        });

        return redirect()->route('kejadian')->with('success', 'Data kejadian berhasil ditambahkan!');
    }

    public function edit($id_kejadian)
{
    $kejadian        = Kejadian::with(['korban', 'rumah', 'sosek', 'sarpras', 'pelayanan'])
                        ->findOrFail($id_kejadian);

    $jenisBencana    = JenisBencana::all();
    $namaKejadian    = \App\Models\NamaKejadian::all();
    $kategoriKorban  = KategoriKorban::all();
    $kategoriUmur    = KategoriUmur::all();
    $kecamatan = Kecamatan::all();
    $statusDarurat   = \App\Models\StatusDarurat::all();
    $pengawas        = \App\Models\Pengawas::all();

    // definisikan variabel dengan nilai default null
    $sosek     = $kejadian->sosek()->first() ?? null;
    $sarpras   = $kejadian->sarpras()->first() ?? null;
    $pelayanan = $kejadian->pelayanan()->first() ?? null;

    return view('edit', compact(
        'kejadian',
        'jenisBencana',
        'namaKejadian',
        'kategoriKorban',
        'kategoriUmur',
        'statusDarurat',
        'pengawas',
        'sosek',
        'sarpras',
        'pelayanan',
        'kecamatan'
    ));
}



public function update(Request $request, $id_kejadian)
{
    $validated = $request->validate([
        'id_jenis_bencana' => 'required|integer',
        'id_nama_kejadian' => 'required|integer',
        'tanggal'          => 'required|date',
        'waktu'            => 'required',
        'id_provinsi'      => 'required|integer',
        'id_kabupaten'     => 'required|integer',
        'id_kecamatan'     => 'required|integer',
        'id_desa'          => 'required|integer',
        'longitude'        => 'required|string|max:255',
        'latitude'         => 'required|string|max:255',
        'penyebab'         => 'required|string',
        'kronologi'        => 'required|string',
        'deskripsi'        => 'required|string',
        'sumber'           => 'required|string|max:255',
        'kondisi_mutakhir' => 'required|string',
        'id_status_darurat'=> 'required|integer',
        'upaya'            => 'required|string',
        'sebaran_dampak'   => 'required|string',
        'kib'              => 'required|string',
        'dokumentasi'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'nip_pengawas'     => 'nullable|string|exists:tb_pengawas,nip_pengawas',

        // validasi tambahan untuk korban & rumah
        'korban'           => 'array',
        'korban.*.id_kategori_korban' => 'nullable|integer',
        'korban.*.id_kategori_umur'   => 'nullable|integer',
        'korban.*.L'                  => 'nullable|integer',
        'korban.*.P'                  => 'nullable|integer',

        // rumah
        'rmh_rr'   => 'nullable|integer',
        'rmh_rs'   => 'nullable|integer',
        'rmh_rb'   => 'nullable|integer',
        'terendam' => 'nullable|integer',

        // sosek
        'sosek_rr'   => 'nullable|integer',
        'sosek_rs'   => 'nullable|integer',
        'sosek_rb'   => 'nullable|integer',
        'sosek_terendam' => 'nullable|integer',

        // sarpras
        'sarpras_rr'   => 'nullable|integer',
        'sarpras_rs'   => 'nullable|integer',
        'sarpras_rb'   => 'nullable|integer',
        'sarpras_terendam' => 'nullable|integer',

        // pelayanan dasar
        'pelayanan_rr'   => 'nullable|integer',
        'pelayanan_rs'   => 'nullable|integer',
        'pelayanan_rb'   => 'nullable|integer',
        'pelayanan_terendam' => 'nullable|integer',

        // Sosial Ekonomi
        'sosek_rr'         => 'nullable|integer|min:0',
        'sosek_rs'         => 'nullable|integer|min:0',
        'sosek_rb'         => 'nullable|integer|min:0',
        'sosek_terendam'   => 'nullable|integer|min:0',

        // Sarpras
        'sarpras_rr'       => 'nullable|integer|min:0',
        'sarpras_rs'       => 'nullable|integer|min:0',
        'sarpras_rb'       => 'nullable|integer|min:0',
        'sarpras_terendam' => 'nullable|integer|min:0',

        // Pelayanan Dasar
        'pelayanan_rr'       => 'nullable|integer|min:0',
        'pelayanan_rs'       => 'nullable|integer|min:0',
        'pelayanan_rb'       => 'nullable|integer|min:0',
        'pelayanan_terendam' => 'nullable|integer|min:0',

        'kerugian'         => 'nullable|integer|min:0',
        'taksiran'         => 'nullable|integer|min:0',
    ]);

    $kejadian = Kejadian::findOrFail($id_kejadian);

    // upload file baru
    if ($request->hasFile('dokumentasi')) {
        $validated['dokumentasi'] = $request->file('dokumentasi')->store('dokumentasi', 'public');
    }

    // update data utama
    $kejadian->update($validated);

    // === Update Data Korban ===
    $kejadian->korban()->delete();
    if ($request->has('korban')) {
        foreach ($request->korban as $k) {
            $kejadian->korban()->create([
                'id_kategori_korban' => $k['id_kategori_korban'] ?? null,
                'id_kategori_umur'   => $k['id_kategori_umur'] ?? null,
                'L'                  => $k['L'] ?? 0,
                'P'                  => $k['P'] ?? 0,
            ]);
        }
    }

    // === Update Data Rumah ===
    $kejadian->rumah()->delete();
    $kejadian->rumah()->create([
        'rmh_rr'   => $request->rmh_rr ?? 0,
        'rmh_rs'   => $request->rmh_rs ?? 0,
        'rmh_rb'   => $request->rmh_rb ?? 0,
        'terendam' => $request->terendam ?? 0,
        'kerugian' => $request->kerugian ?? 0,
    ]);

    // === Update Data Sosek ===
    if ($kejadian->sosek) {
        $kejadian->sosek->update([
            'sosek_rr'       => (int) ($request->sosek_rusak_ringan ?? 0),
            'sosek_rs'       => (int) ($request->sosek_rusak_sedang ?? 0),
            'sosek_rb'       => (int) ($request->sosek_rusak_berat ?? 0),
            'sosek_terendam' => (int) ($request->sosek_terendam ?? 0),
        ]);
    } else {
        $kejadian->sosek()->create([
            'sosek_rr'       => $validated['sosek_rr'] ?? 0,
            'sosek_rs'       => $validated['sosek_rs'] ?? 0,
            'sosek_rb'       => $validated['sosek_rb'] ?? 0,
            'sosek_terendam' => $validated['sosek_terendam'] ?? 0,
            'kerugian'       => $validated['kerugian'] ?? 0,
        ]);
    }

    // === Update Data Sarpras ===
    if ($kejadian->sarpras) {
        $kejadian->sarpras->update([
            'sarpras_rr'       => (int) ($request->sarpras_rusak_ringan ?? 0),
            'sarpras_rs'       => (int) ($request->sarpras_rusak_sedang ?? 0),
            'sarpras_rb'       => (int) ($request->sarpras_rusak_berat ?? 0),
            'sarpras_terendam' => (int) ($request->sarpras_terendam ?? 0),
        ]);
    } else {
        $kejadian->sarpras()->create([
            'sarpras_rr'       => $validated['sarpras_rr'] ?? 0,
            'sarpras_rs'       => $validated['sarpras_rs'] ?? 0,
            'sarpras_rb'       => $validated['sarpras_rb'] ?? 0,
            'sarpras_terendam' => $validated['sarpras_terendam'] ?? 0,
            'taksiran'         => $validated['taksiran'] ?? 0,
        ]);
    }

    // === Update Data Pelayanan Dasar ===
    if ($kejadian->pelayanan) {
        $kejadian->pelayanan->update([
            'pelayanan_rr'       => (int) ($request->pelayanan_rusak_ringan ?? 0),
            'pelayanan_rs'       => (int) ($request->pelayanan_rusak_sedang ?? 0),
            'pelayanan_rb'       => (int) ($request->pelayanan_rusak_berat ?? 0),
            'pelayanan_terendam' => (int) ($request->pelayanan_terendam ?? 0),
        ]);
    } else {
        $kejadian->pelayanan()->create([
            'pelayanan_rr'       => $validated['pelayanan_rr'] ?? 0,
            'pelayanan_rs'       => $validated['pelayanan_rs'] ?? 0,
            'pelayanan_rb'       => $validated['pelayanan_rb'] ?? 0,
            'pelayanan_terendam' => $validated['pelayanan_terendam'] ?? 0,
            'taksiran'           => $validated['taksiran'] ?? 0,
        ]);
    }

    return redirect()->route('kejadian')->with('success', 'Data kejadian berhasil diperbarui!');
}



    public function destroy($id_kejadian)
{
    $kejadian = Kejadian::findOrFail($id_kejadian);

    // hapus korban dulu
    \App\Models\Korban::where('id_kejadian', $id_kejadian)->delete();

    // kalau ada rumah juga
    \App\Models\Rumah::where('id_kejadian', $id_kejadian)->delete();
    \App\Models\Sosek::where('id_kejadian', $id_kejadian)->delete();
    \App\Models\Sarpras::where('id_kejadian', $id_kejadian)->delete();
    \App\Models\Pelayanan::where('id_kejadian', $id_kejadian)->delete();

    // terakhir hapus kejadian
    $kejadian->delete();

    return redirect()->route('kejadian')->with('success', 'Data berhasil dihapus');
}

public function filter(Request $request)
{
    $tanggal = $request->input('tanggal');
    $kejadian = Kejadian::when($tanggal, function($query, $tanggal) {
        $query->whereDate('tanggal', $tanggal);
    })->get();

    return view('formKejadian', compact('kejadian', 'tanggal'));
}

public function print(Request $request)
{
    $tanggal = $request->input('tanggal');

    // ambil semua kejadian sesuai tanggal + relasi
    $kejadian = Kejadian::with(['jenisBencana', 'namaKejadian'])
        ->whereDate('tanggal', $tanggal)
        ->get();

    return view('print', compact('kejadian', 'tanggal'));
}
public function show($id)
{
    $kejadian = Kejadian::with([
        'korban',
        'rumah',
        'sosek',
        'sarpras',
        'pelayanan',
        'pengawas'
    ])->findOrFail($id);

    return view('show', compact('kejadian'));
}

}