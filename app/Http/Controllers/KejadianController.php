<?php

namespace App\Http\Controllers;

use App\Models\Kejadian;
use App\Models\JenisBencana;
use App\Models\KategoriKorban;
use App\Models\KategoriUmur;
use App\Models\Korban;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Pelayanan;
use App\Models\layan;
use App\Models\sosekk;
use App\Models\sarprass;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Pengawas;
use App\Models\StatusDarurat;
use App\Models\Rumah;
use App\Models\TemplateTTD;
use Carbon\carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // JANGAN LUPA INI
use Illuminate\Support\Facades\Validator; // JANGAN LUPA INI

class KejadianController extends Controller
{
    public function index()
    {
        $kejadian = Kejadian::with(['namaKejadian', 'kecamatan'])->get();
        $user = Auth::user();
        return view('formKejadian', compact('kejadian', 'user'));
    }

    public function create()
    {

        $provinsi = DB::table('tb_provinsi')->where('id_provinsi', 1)->first();
        $kabupaten = DB::table('tb_kabupaten')->where('id_kabupaten', 1)->first();
        $jenisKerusakan = layan::all();
        $jenisKerusakan2 = sosekk::all();
        $jenisKerusakan3 = sarprass::all();
        return view('create', [
            'nama_kejadian' => $request->nama_kejadian ?? null, // aman jika null
            'kejadianList'   => Kejadian::all(),
            'jenisBencana'   => JenisBencana::all(),
            'kategoriKorban' => KategoriKorban::all(),
            'kategoriUmur'   => KategoriUmur::all(),
            'kecamatan'      => Kecamatan::all(),
            'namaKejadian'   => \App\Models\NamaKejadian::all(),
            'statusDarurat'  => \App\Models\StatusDarurat::all(),
            'pengawas'       => \App\Models\Pengawas::all(),
            'desa'           => Desa::all(),
            'provinsi'       => $provinsi,
            'kabupaten'      => $kabupaten,
            'jenisKerusakan' => $jenisKerusakan,
            'jenisKerusakan2' => $jenisKerusakan2,
            'jenisKerusakan3' => $jenisKerusakan3,
        ]);
    }

    public function getDesaByKecamatan($id_kecamatan)
    {
        try {
            $desa = Desa::where('id_kecamatan', $id_kecamatan)
                ->select('id_desa', 'desa')
                ->get();

            return response()->json($desa);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /** ===============================
     *  STORE DATA KEJADIAN BARU
     *  =============================== */
public function store(Request $request)
{
    // Validasi input (sama dengan update)
    $validated = $request->validate([
        // Sama seperti validasi di update method
        'id_jenis_bencana' => 'required|integer|exists:tb_jenis_bencana,id_jenis_bencana',
        'nama_kejadian' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'waktu' => 'required',
        'id_provinsi' => 'required|integer',
        'id_kabupaten' => 'required|integer',
        'id_kecamatan' => 'required|integer|exists:tb_kecamatan,id_kecamatan',
        'id_desa' => 'required|integer|exists:tb_desa,id_desa',
        'alamat' => 'nullable|string|max:500',
        'longitude' => 'required|string|max:20',
        'latitude' => 'required|string|max:20',
        'penyebab' => 'required|string',
        'kronologi' => 'required|string',
        'deskripsi' => 'required|string',
        'sumber' => 'required|string|max:255',
        'logistik' => 'nullable|string',
        'kondisi_mutakhir' => 'required|string',
        'id_status_darurat' => 'required|integer|exists:tb_status_darurat,id_status_darurat',
        'upaya' => 'required|string',
        'sebaran_dampak' => 'required|string',
        'unsur' => 'required|string',
        'kib' => 'nullable|string',
        'index_kejadian' => 'nullable|integer',
        
        // Data korban
        'korban' => 'nullable|array',
        'korban.*.id_kategori_korban' => 'nullable|integer|exists:tb_kategori_korban,id_kategori_korban',
        'korban.*.id_kategori_umur' => 'nullable|integer|exists:tb_kategori_umur,id_kategori_umur',
        'korban.*.L' => 'nullable|integer|min:0',
        'korban.*.P' => 'nullable|integer|min:0',
        
        // Data rumah
        'rmh_rr' => 'nullable|integer|min:0',
        'rmh_rs' => 'nullable|integer|min:0',
        'rmh_rb' => 'nullable|integer|min:0',
        'terendam' => 'nullable|integer|min:0',
        
        // Data sosek
        'sosek.luas' => 'nullable|numeric|min:0',
        'sosek.rr' => 'nullable|integer|min:0',
        'sosek.rs' => 'nullable|integer|min:0',
        'sosek.rb' => 'nullable|integer|min:0',
        'sosek.terendam' => 'nullable|integer|min:0',
        'id_jenis_kerusakan_sosek' => 'nullable|integer|exists:tb_jenis_kerusakan_sosek,id_jenis_kerusakan_sosek',
        
        // Data sarpras
        'sarpras.rr' => 'nullable|integer|min:0',
        'sarpras.rs' => 'nullable|integer|min:0',
        'sarpras.rb' => 'nullable|integer|min:0',
        'sarpras.terendam' => 'nullable|integer|min:0',
        'id_jenis_kerusakan_sarpras' => 'nullable|integer|exists:tb_jenis_kerusakan_sarpras,id_jenis_kerusakan_sarpras',
        
        // Data pelayanan
        'pelayanan_rr' => 'nullable|integer|min:0',
        'pelayanan_rs' => 'nullable|integer|min:0',
        'pelayanan_rb' => 'nullable|integer|min:0',
        'pelayanan_terendam' => 'nullable|integer|min:0',
        'taksiran' => 'nullable|numeric|min:0',
        'id_jenis_kerusakan_pelayanandasar' => 'nullable|integer|exists:tb_jenis_kerusakan_pelayanandasar,id_jenis_kerusakan_pelayanandasar',
        
        // Petugas piket
        'nip_pengawas' => 'required|array|min:1',
        'nip_pengawas.*' => 'string|exists:tb_pengawas,nip_pengawas',
        
        // Multiple file
        'dokumentasi' => 'nullable|array',
        'dokumentasi.*' => 'nullable|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480',
    ]);

    // Cek duplikasi
    $existing = Kejadian::where('nama_kejadian', $validated['nama_kejadian'])
        ->where('tanggal', $validated['tanggal'])
        ->where('id_desa', $validated['id_desa'])
        ->first();

    if ($existing) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['nama_kejadian' => 'Duplikasi: Kejadian sudah tercatat pada tanggal dan desa yang sama.']);
    }

    DB::beginTransaction();
    
    try {
        // ==========================
        //  HANDLE DOKUMENTASI
        // ==========================
        $paths = [];
        
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                if ($file->isValid()) {
                    $paths[] = $file->store('dokumentasi', 'public');
                }
            }
        }
        
        $validated['dokumentasi'] = !empty($paths) ? json_encode($paths) : null;

        // ==========================
        //  HANDLE PETUGAS PIKET
        // ==========================
        if ($request->has('nip_pengawas') && is_array($request->nip_pengawas)) {
            $nipPengawas = array_filter($request->nip_pengawas);
            $validated['nip_pengawas'] = !empty($nipPengawas) ? implode(',', $nipPengawas) : '';
        } else {
            $validated['nip_pengawas'] = '';
        }

        // Generate KIB jika belum ada
        if (empty($validated['kib'])) {
            $kodeProvinsi = str_pad($request->id_provinsi, 2, '0', STR_PAD_LEFT);
            $kodeKabupaten = str_pad($request->id_kabupaten, 2, '0', STR_PAD_LEFT);
            $kodeBencana = str_pad($request->id_jenis_bencana, 3, '0', STR_PAD_LEFT);
            $tanggalStr = date('Ymd', strtotime($request->tanggal));
            $countToday = Kejadian::whereDate('tanggal', $request->tanggal)->count() + 1;
            $index = str_pad($countToday, 2, '0', STR_PAD_LEFT);
            $validated['kib'] = "{$kodeProvinsi}{$kodeKabupaten}{$kodeBencana}{$tanggalStr}{$index}";
            $validated['index_kejadian'] = $countToday;
        }

        // ==========================
        //  SIMPAN DATA UTAMA
        // ==========================
        $kejadian = Kejadian::create($validated);
        $idKejadian = $kejadian->id_kejadian;

        // ==========================
        //  SIMPAN RELASI
        // ==========================
        // Simpan korban
        if ($request->has('korban') && is_array($request->korban)) {
            foreach ($request->korban as $korbanData) {
                if (!empty($korbanData['id_kategori_korban']) && !empty($korbanData['id_kategori_umur'])) {
                    Korban::create([
                        'id_kejadian' => $idKejadian,
                        'id_kategori_korban' => $korbanData['id_kategori_korban'],
                        'id_kategori_umur' => $korbanData['id_kategori_umur'],
                        'L' => $korbanData['L'] ?? 0,
                        'P' => $korbanData['P'] ?? 0,
                    ]);
                }
            }
        }

        // Simpan rumah
        Rumah::create([
            'id_kejadian' => $idKejadian,
            'rmh_rr' => $request->rmh_rr ?? 0,
            'rmh_rs' => $request->rmh_rs ?? 0,
            'rmh_rb' => $request->rmh_rb ?? 0,
            'terendam' => $request->terendam ?? 0,
            'kerugian' => 0,
        ]);

        // Simpan sosek
        if ($request->filled('id_jenis_kerusakan_sosek')) {
            DB::table('tb_kerusakan_sosek')->insert([
                'id_jenis_kerusakan_sosek' => $request->id_jenis_kerusakan_sosek,
                'luas' => $request->input('sosek.luas') ?? 0,
                'sosek_rr' => $request->input('sosek.rr') ?? 0,
                'sosek_rs' => $request->input('sosek.rs') ?? 0,
                'sosek_rb' => $request->input('sosek.rb') ?? 0,
                'sosek_terendam' => $request->input('sosek.terendam') ?? 0,
                'kerugian' => 0,
                'id_kejadian' => $idKejadian,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Simpan sarpras
        if ($request->filled('id_jenis_kerusakan_sarpras')) {
            DB::table('tb_kerusakan_sarpras')->insert([
                'id_jenis_kerusakan_sarpras' => $request->id_jenis_kerusakan_sarpras,
                'sarpras_rr' => $request->input('sarpras.rr') ?? 0,
                'sarpras_rs' => $request->input('sarpras.rs') ?? 0,
                'sarpras_rb' => $request->input('sarpras.rb') ?? 0,
                'sarpras_terendam' => $request->input('sarpras.terendam') ?? 0,
                'id_kejadian' => $idKejadian,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Simpan pelayanan
        if ($request->filled('id_jenis_kerusakan_pelayanandasar')) {
            \App\Models\Pelayanan::create([
                'id_kejadian' => $idKejadian,
                'id_jenis_kerusakan_pelayanandasar' => $request->id_jenis_kerusakan_pelayanandasar,
                'pelayanan_rr' => $request->pelayanan_rr ?? 0,
                'pelayanan_rs' => $request->pelayanan_rs ?? 0,
                'pelayanan_rb' => $request->pelayanan_rb ?? 0,
                'pelayanan_terendam' => $request->pelayanan_terendam ?? 0,
                'taksiran' => $request->taksiran ?? 0,
            ]);
        }

        DB::commit();

        return redirect()->route('kejadian')->with('success', 'Data kejadian berhasil ditambahkan!');

    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Store kejadian error: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

    /** ===============================
     *  EDIT KEJADIAN
     *  =============================== */
     public function edit($id_kejadian)
    {
        $kejadian = Kejadian::with([
            'korban.kategoriKorban',
            'korban.kategoriUmur',
            'rumah', 
            'sosek',
            'sarpras',
            'pelayanan',
            'pengawas'
        ])->findOrFail($id_kejadian);

        $jenisKerusakan = layan::all();
        $jenisKerusakan2 = sosekk::all();
        $jenisKerusakan3 = sarprass::all();

        // Get desa data for the current kecamatan
        $desa = Desa::where('id_kecamatan', $kejadian->id_kecamatan)->get();

        return view('edit', [
            'kejadian'       => $kejadian,
            'jenisBencana'   => JenisBencana::all(),
            'kategoriKorban' => KategoriKorban::all(),
            'kategoriUmur'   => KategoriUmur::all(),
            'statusDarurat'  => StatusDarurat::all(),
            'pengawas'       => Pengawas::all(),
            'kecamatan'      => Kecamatan::all(),
            'desa'           => $desa,
            'sosek'          => $kejadian->sosek()->first(),
            'sarpras'        => $kejadian->sarpras()->first(),
            'pelayanan'      => $kejadian->pelayanan()->first(),
            'jenisKerusakan'  => $jenisKerusakan,
            'jenisKerusakan2' => $jenisKerusakan2,
            'jenisKerusakan3' => $jenisKerusakan3,
        ]);
    }
    /** ===============================
 *  UPDATE KEJADIAN
 *  =============================== */
/** ===============================
 *  UPDATE KEJADIAN
 *  =============================== */
public function update(Request $request, $id_kejadian)
{
    $kejadian = Kejadian::findOrFail($id_kejadian);

    // ==========================
    //  PRE-VALIDATION NORMALIZATION
    // ==========================
    // Normalisasi field pelayanan sebelum validasi
    $request->merge([
        'pelayanan_rr' => $this->normalizeForValidation($request->pelayanan_rr),
        'pelayanan_rs' => $this->normalizeForValidation($request->pelayanan_rs),
        'pelayanan_rb' => $this->normalizeForValidation($request->pelayanan_rb),
        'pelayanan_terendam' => $this->normalizeForValidation($request->pelayanan_terendam),
        'taksiran' => $this->normalizeForValidation($request->taksiran),
        
        // Normalisasi field numerik lainnya juga
        'rmh_rr' => $this->normalizeForValidation($request->rmh_rr),
        'rmh_rs' => $this->normalizeForValidation($request->rmh_rs),
        'rmh_rb' => $this->normalizeForValidation($request->rmh_rb),
        'terendam' => $this->normalizeForValidation($request->terendam),
    ]);

    // Normalisasi field array (sosek, sarpras)
    if ($request->has('sosek.rr')) {
        $request->merge([
            'sosek.rr' => $this->normalizeForValidation($request->input('sosek.rr')),
            'sosek.rs' => $this->normalizeForValidation($request->input('sosek.rs')),
            'sosek.rb' => $this->normalizeForValidation($request->input('sosek.rb')),
            'sosek.terendam' => $this->normalizeForValidation($request->input('sosek.terendam')),
            'sosek.luas' => $this->normalizeForValidation($request->input('sosek.luas')),
        ]);
    }

    if ($request->has('sarpras.rr')) {
        $request->merge([
            'sarpras.rr' => $this->normalizeForValidation($request->input('sarpras.rr')),
            'sarpras.rs' => $this->normalizeForValidation($request->input('sarpras.rs')),
            'sarpras.rb' => $this->normalizeForValidation($request->input('sarpras.rb')),
            'sarpras.terendam' => $this->normalizeForValidation($request->input('sarpras.terendam')),
        ]);
    }

    // Validasi yang lengkap sesuai form
    $validated = $request->validate([
        // Data utama
        'id_jenis_bencana' => 'required|integer|exists:tb_jenis_bencana,id_jenis_bencana',
        'nama_kejadian' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'waktu' => 'required',
        'id_provinsi' => 'required|integer',
        'id_kabupaten' => 'required|integer',
        'id_kecamatan' => 'required|integer|exists:tb_kecamatan,id_kecamatan',
        'id_desa' => 'required|integer|exists:tb_desa,id_desa',
        'alamat' => 'nullable|string|max:500',
        'longitude' => 'required|string|max:20',
        'latitude' => 'required|string|max:20',
        'penyebab' => 'required|string',
        'kronologi' => 'required|string',
        'deskripsi' => 'required|string',
        'sumber' => 'required|string|max:255',
        'logistik' => 'nullable|string',
        'kondisi_mutakhir' => 'required|string',
        'id_status_darurat' => 'required|integer|exists:tb_status_darurat,id_status_darurat',
        'upaya' => 'required|string',
        'sebaran_dampak' => 'required|string',
        'unsur' => 'required|string',
        'kib' => 'nullable|string',
        'index_kejadian' => 'nullable|integer',
        
        // Data korban sebagai array
        'korban' => 'nullable|array',
        'korban.*.id_kategori_korban' => 'nullable|integer|exists:tb_kategori_korban,id_kategori_korban',
        'korban.*.id_kategori_umur' => 'nullable|integer|exists:tb_kategori_umur,id_kategori_umur',
        'korban.*.L' => 'nullable|integer|min:0',
        'korban.*.P' => 'nullable|integer|min:0',
        
        // Data rumah - PERBAIKAN: ganti integer dengan numeric
        'rmh_rr' => 'nullable|numeric|min:0',
        'rmh_rs' => 'nullable|numeric|min:0',
        'rmh_rb' => 'nullable|numeric|min:0',
        'terendam' => 'nullable|numeric|min:0',
        
        // Data sosek
        'sosek.luas' => 'nullable|numeric|min:0',
        'sosek.rr' => 'nullable|numeric|min:0',
        'sosek.rs' => 'nullable|numeric|min:0',
        'sosek.rb' => 'nullable|numeric|min:0',
        'sosek.terendam' => 'nullable|numeric|min:0',
        'id_jenis_kerusakan_sosek' => 'nullable|integer|exists:tb_jenis_kerusakan_sosek,id_jenis_kerusakan_sosek',
        
        // Data sarpras
        'sarpras.rr' => 'nullable|numeric|min:0',
        'sarpras.rs' => 'nullable|numeric|min:0',
        'sarpras.rb' => 'nullable|numeric|min:0',
        'sarpras.terendam' => 'nullable|numeric|min:0',
        'id_jenis_kerusakan_sarpras' => 'nullable|integer|exists:tb_jenis_kerusakan_sarpras,id_jenis_kerusakan_sarpras',
        
        // Data pelayanan - PERBAIKAN: gunakan numeric bukan integer
        'pelayanan_rr' => 'nullable|numeric|min:0',
        'pelayanan_rs' => 'nullable|numeric|min:0',
        'pelayanan_rb' => 'nullable|numeric|min:0',
        'pelayanan_terendam' => 'nullable|numeric|min:0',
        'taksiran' => 'nullable|numeric|min:0',
        'id_jenis_kerusakan_pelayanandasar' => 'nullable|integer|exists:tb_jenis_kerusakan_pelayanandasar,id_jenis_kerusakan_pelayanandasar',
        
        // Petugas piket
        'nip_pengawas' => 'required|array|min:1',
        'nip_pengawas.*' => 'string|exists:tb_pengawas,nip_pengawas',
        
        // Multiple file
        'dokumentasi' => 'nullable|array',
        'dokumentasi.*' => 'nullable|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480',
        'removed_docs' => 'nullable|string',
    ]);

    // Gunakan transaction
    DB::beginTransaction();
    
    try {
        // ==========================
        //  HANDLE DOKUMENTASI
        // ==========================
        // Pastikan existingDocs selalu array
        $existingDocs = [];
        if (!empty($kejadian->dokumentasi)) {
            $decoded = json_decode($kejadian->dokumentasi, true);
            $existingDocs = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) 
                ? $decoded 
                : [$kejadian->dokumentasi];
        }

        // Handle hapus dokumentasi lama
        if ($request->filled('removed_docs')) {
            $removedIndexes = json_decode($request->removed_docs, true) ?? [];
            foreach ($removedIndexes as $index) {
                if (isset($existingDocs[$index])) {
                    // Hapus file fisik
                    $filePath = $existingDocs[$index];
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                    unset($existingDocs[$index]);
                }
            }
            $existingDocs = array_values($existingDocs); // Reset index
        }

        // Handle upload file baru
        $newDocs = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                if ($file->isValid()) {
                    $newDocs[] = $file->store('dokumentasi', 'public');
                }
            }
        }

        // Gabungkan file lama dan baru
        $allDocs = array_merge($existingDocs, $newDocs);
        $validated['dokumentasi'] = !empty($allDocs) ? json_encode($allDocs) : null;

        // ==========================
        //  HANDLE PETUGAS PIKET
        // ==========================
        if ($request->has('nip_pengawas') && is_array($request->nip_pengawas)) {
            // Filter nilai kosong
            $nipPengawas = array_filter($request->nip_pengawas);
            $validated['nip_pengawas'] = !empty($nipPengawas) ? implode(',', $nipPengawas) : '';
        } else {
            $validated['nip_pengawas'] = $kejadian->nip_pengawas ?? '';
        }

        // ==========================
        //  FINAL NORMALIZATION
        // ==========================
        // Pastikan semua field numerik memiliki nilai default 0 jika null
        $numericFields = [
            'pelayanan_rr', 'pelayanan_rs', 'pelayanan_rb', 'pelayanan_terendam', 'taksiran',
            'rmh_rr', 'rmh_rs', 'rmh_rb', 'terendam'
        ];

        foreach ($numericFields as $field) {
            if (!isset($validated[$field]) || $validated[$field] === '' || $validated[$field] === null) {
                $validated[$field] = 0;
            }
        }

        // ==========================
        //  UPDATE DATA UTAMA
        // ==========================
        $kejadian->update($validated);

        // ==========================
        //  UPDATE RELASI
        // ==========================
        $this->syncRelations($kejadian, $request, $validated);

        // Commit transaction
        DB::commit();

        return redirect()->route('kejadian')->with('success', 'Data kejadian berhasil diperbarui!');

    } catch (\Exception $e) {
        // Rollback jika ada error
        DB::rollBack();
        
        \Log::error('Update kejadian error: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

/** Helper untuk normalisasi sebelum validasi */
private function normalizeForValidation($value)
{
    // Jika null atau string kosong, return 0
    if ($value === null || $value === '' || $value === false) {
        return 0;
    }
    
    // Jika sudah numeric, pastikan tidak negatif
    if (is_numeric($value)) {
        return max(0, (float) $value);
    }
    
    // Coba konversi ke numeric
    $numericValue = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    if (is_numeric($numericValue)) {
        return max(0, (float) $numericValue);
    }
    
    return 0;
}

/** Helper untuk update relasi */
/** Helper untuk update relasi (VERSION WITHOUT TIMESTAMPS) */
private function syncRelations(Kejadian $kejadian, Request $request, array $validatedData = [])
{
    $idKejadian = $kejadian->id_kejadian;
    
    // ==========================
    //  KORBAN
    // ==========================
    Korban::where('id_kejadian', $idKejadian)->delete();
    
    if ($request->has('korban') && is_array($request->korban)) {
        foreach ($request->korban as $korbanData) {
            if (!empty($korbanData['id_kategori_korban']) && !empty($korbanData['id_kategori_umur'])) {
                Korban::create([
                    'id_kejadian' => $idKejadian,
                    'id_kategori_korban' => $korbanData['id_kategori_korban'],
                    'id_kategori_umur' => $korbanData['id_kategori_umur'],
                    'L' => $this->normalizeNumeric($korbanData['L'] ?? 0),
                    'P' => $this->normalizeNumeric($korbanData['P'] ?? 0),
                ]);
            }
        }
    }

    // ==========================
    //  RUMAH
    // ==========================
    $rumahData = [
        'rmh_rr' => $validatedData['rmh_rr'] ?? $this->normalizeNumeric($request->rmh_rr),
        'rmh_rs' => $validatedData['rmh_rs'] ?? $this->normalizeNumeric($request->rmh_rs),
        'rmh_rb' => $validatedData['rmh_rb'] ?? $this->normalizeNumeric($request->rmh_rb),
        'terendam' => $validatedData['terendam'] ?? $this->normalizeNumeric($request->terendam),
        'kerugian' => 0,
    ];

    // Update atau insert tanpa eloquent untuk menghindari timestamps
    $existingRumah = DB::table('tb_kerusakan_rumah')->where('id_kejadian', $idKejadian)->first();
    if ($existingRumah) {
        DB::table('tb_kerusakan_rumah')
            ->where('id_kejadian', $idKejadian)
            ->update($rumahData);
    } else {
        $rumahData['id_kejadian'] = $idKejadian;
        DB::table('tb_kerusakan_rumah')->insert($rumahData);
    }

    // ==========================
    //  SOSIAL EKONOMI (SOSEK)
    // ==========================
    if ($request->filled('id_jenis_kerusakan_sosek')) {
        $sosekData = [
            'id_jenis_kerusakan_sosek' => $request->id_jenis_kerusakan_sosek,
            'luas' => $this->normalizeNumeric($request->input('sosek.luas')),
            'sosek_rr' => $this->normalizeNumeric($request->input('sosek.rr')),
            'sosek_rs' => $this->normalizeNumeric($request->input('sosek.rs')),
            'sosek_rb' => $this->normalizeNumeric($request->input('sosek.rb')),
            'sosek_terendam' => $this->normalizeNumeric($request->input('sosek.terendam')),
            'kerugian' => 0,
            'id_kejadian' => $idKejadian,
        ];

        $existingSosek = DB::table('tb_kerusakan_sosek')->where('id_kejadian', $idKejadian)->first();
        
        if ($existingSosek) {
            DB::table('tb_kerusakan_sosek')
                ->where('id_kejadian', $idKejadian)
                ->update($sosekData);
        } else {
            DB::table('tb_kerusakan_sosek')->insert($sosekData);
        }
    } else {
        DB::table('tb_kerusakan_sosek')->where('id_kejadian', $idKejadian)->delete();
    }

    // ==========================
    //  SARANA PRASARANA (SARPRAS)
    // ==========================
    if ($request->filled('id_jenis_kerusakan_sarpras')) {
        $sarprasData = [
            'id_jenis_kerusakan_sarpras' => $request->id_jenis_kerusakan_sarpras,
            'sarpras_rr' => $this->normalizeNumeric($request->input('sarpras.rr')),
            'sarpras_rs' => $this->normalizeNumeric($request->input('sarpras.rs')),
            'sarpras_rb' => $this->normalizeNumeric($request->input('sarpras.rb')),
            'sarpras_terendam' => $this->normalizeNumeric($request->input('sarpras.terendam')),
            'id_kejadian' => $idKejadian,
        ];

        $existingSarpras = DB::table('tb_kerusakan_sarpras')->where('id_kejadian', $idKejadian)->first();
        
        if ($existingSarpras) {
            DB::table('tb_kerusakan_sarpras')
                ->where('id_kejadian', $idKejadian)
                ->update($sarprasData);
        } else {
            DB::table('tb_kerusakan_sarpras')->insert($sarprasData);
        }
    } else {
        DB::table('tb_kerusakan_sarpras')->where('id_kejadian', $idKejadian)->delete();
    }

    // ==========================
    //  PELAYANAN DASAR
    // ==========================
    if ($request->filled('id_jenis_kerusakan_pelayanandasar')) {
        $pelayananData = [
            'id_jenis_kerusakan_pelayanandasar' => $request->id_jenis_kerusakan_pelayanandasar,
            'pelayanan_rr' => $validatedData['pelayanan_rr'] ?? 0,
            'pelayanan_rs' => $validatedData['pelayanan_rs'] ?? 0,
            'pelayanan_rb' => $validatedData['pelayanan_rb'] ?? 0,
            'pelayanan_terendam' => $validatedData['pelayanan_terendam'] ?? 0,
            'taksiran' => $validatedData['taksiran'] ?? 0,
            'id_kejadian' => $idKejadian,
        ];

        $existingPelayanan = DB::table('tb_kerusakan_pelayanandasar')->where('id_kejadian', $idKejadian)->first();
        
        if ($existingPelayanan) {
            DB::table('tb_kerusakan_pelayanandasar')
                ->where('id_kejadian', $idKejadian)
                ->update($pelayananData);
        } else {
            DB::table('tb_kerusakan_pelayanandasar')->insert($pelayananData);
        }
    } else {
        DB::table('tb_kerusakan_pelayanandasar')->where('id_kejadian', $idKejadian)->delete();
    }
}

// Helper untuk normalisasi nilai numeric
private function normalizeNumeric($value)
{
    if ($value === null || $value === '' || $value === false) {
        return 0;
    }
    
    if (is_numeric($value)) {
        return (float) $value;
    }
    
    $numericValue = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    return is_numeric($numericValue) ? (float) $numericValue : 0;
}

    /** Hapus kejadian beserta relasinya */
    public function destroy($id_kejadian)
    {
        $kejadian = Kejadian::findOrFail($id_kejadian);

        \App\Models\Korban::where('id_kejadian', $id_kejadian)->delete();
        \App\Models\Rumah::where('id_kejadian', $id_kejadian)->delete();
        \App\Models\Sosek::where('id_kejadian', $id_kejadian)->delete();
        \App\Models\Sarpras::where('id_kejadian', $id_kejadian)->delete();
        \App\Models\Pelayanan::where('id_kejadian', $id_kejadian)->delete();

        $kejadian->delete();

        return redirect()->route('kejadian')->with('success', 'Data berhasil dihapus!');
    }

    public function filter(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $kejadian = Kejadian::when($tanggal, fn($q) => $q->whereDate('tanggal', $tanggal))->get();

        return view('formKejadian', compact('kejadian', 'tanggal'));
    }

    public function print($id_kejadian)
    {
        $kejadian = Kejadian::with([
            'jenisBencana',
            'namaKejadian',
            'desa',
            'kecamatan',
            'kabupaten',
            'rumah',
            'korban.kategoriKorban',
            'korban.kategoriUmur'
        ])->findOrFail($id_kejadian);

        // Ambil data TTD dari database
        $ttdPertama = TemplateTTD::where('is_active', 1)
            ->select('nama_pengawas', 'nip_pengawas', 'jabatan')
            ->orderBy('id', 'desc')
            ->first();

        // Ambil logo (jika diperlukan)
        $logo2 = null;
        $path2 = public_path('gambar/logo2.png');
        if (file_exists($path2)) {
            $logo2 = base64_encode(file_get_contents($path2));
        }

        return view('print', [
            'kejadian' => [$kejadian],
            'tanggal' => $kejadian->tanggal,
            'ttdPertama' => $ttdPertama,
            'logo2' => $logo2
        ]);
    }

    public function printByTanggal(Request $request)
    {
        $tanggal = $request->tanggal;
        
        $kejadian = Kejadian::with([
            'jenisBencana', 
            'namaKejadian', 
            'desa', 
            'kecamatan', 
            'kabupaten', 
            'rumah',
            'korban.kategoriKorban',
            'korban.kategoriUmur'
        ])
        ->whereDate('tanggal', $tanggal)
        ->get();

        // Ambil data TTD dari database
        $ttdPertama = TemplateTTD::where('is_active', 1)
            ->select('nama_pengawas', 'nip_pengawas', 'jabatan')
            ->orderBy('id', 'desc')
            ->first();

        // Ambil logo (jika diperlukan)
        $logo2 = null;
        $path2 = public_path('gambar/logo2.png');
        if (file_exists($path2)) {
            $logo2 = base64_encode(file_get_contents($path2));
        }

        return view('print', compact('kejadian', 'tanggal', 'ttdPertama', 'logo2'));
    }
    public function show($id)
{
    $kejadian = Kejadian::with([
        'korban.kategoriKorban',
        'korban.kategoriUmur',
        'rumah',
        'sosek',
        'sarpras', 
        'pelayanan',
        'pengawas', // Tetap ada untuk backward compatibility
        'jenisBencana',
        'statusDarurat',
        'kecamatan',
        'desa'
    ])->findOrFail($id);

    // Ambil semua data pengawas berdasarkan NIP yang ada
    $semuaPengawas = $kejadian->semuaPengawas();

    return view('show', compact('kejadian', 'semuaPengawas'));
}

    public function getIndex(Request $request)
{
    $tanggal = $request->input('tanggal');

    if (!$tanggal) {
        return response()->json(['error' => 'Tanggal tidak ditemukan'], 400);
    }

    // Hitung jumlah kejadian pada tanggal yang sama
    $count = \App\Models\Kejadian::whereDate('tanggal', $tanggal)->count();

    // index berikutnya = jumlah + 1
    $index = $count + 1;

    return response()->json(['index' => $index]);
}
}
