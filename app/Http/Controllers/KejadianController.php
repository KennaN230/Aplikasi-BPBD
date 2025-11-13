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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    // === Data wilayah (Kabupaten Malang) ===
    $kodeProvinsi = '35'; // Jawa Timur
    $kodeKabupaten = '07'; // Kabupaten Malang

    // === Jenis bencana ===
    $kodeBencana = str_pad($request->id_jenis_bencana, 3, '0', STR_PAD_LEFT);

    // === Tanggal kejadian ===
    $tanggal = date('Ymd', strtotime($request->tanggal));

    // === Index kejadian ===
    $countToday = Kejadian::whereDate('tanggal', $request->tanggal)->count() + 1;
    $index = str_pad($countToday, 2, '0', STR_PAD_LEFT); // contoh 01, 02

    // === Generate KIB ===
    $kodeKIB = "{$kodeProvinsi}{$kodeKabupaten}{$kodeBencana}{$tanggal}{$index}";

    $validated = $request->validate([
        'id_jenis_bencana' => 'required|integer',
        'id_nama_kejadian' => 'required|integer',
        'tanggal'          => 'required|date',
        'waktu'            => 'required',
        'id_provinsi'      => 'required|integer',
        'id_kabupaten'     => 'required|integer',
        'id_kecamatan'     => 'required|integer',
        'id_desa'          => 'required|integer',
        'alamat'           => 'nullable|string|max:255',
        'longitude'        => 'required|string|max:255',
        'latitude'         => 'required|string|max:255',
        'penyebab'         => 'required|string',
        'kronologi'        => 'required|string',
        'deskripsi'        => 'required|string',
        'sumber'           => 'required|string|max:255',
        'logistik'         => 'nullable|string',
        'kondisi_mutakhir' => 'required|string',
        'id_status_darurat'=> 'required|integer',
        'upaya'            => 'required|string',
        'dokumentasi'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'sebaran_dampak'   => 'required|string',
        'kib'              => 'required|string',
        'nip_pengawas'     => 'required|array',
        'nip_pengawas.*'   => 'required|string',
    ]);

    $validated['kib'] = $kodeKIB;
    
    // Simpan relasi pengawas (1 atau banyak)
    foreach ($validated['nip_pengawas'] as $nip) {
        DB::table('tb_kejadian_pengawas')->insert([
            'id_kejadian' => $kejadian->id_kejadian,
            'nip_pengawas' => $nip,
        ]);
    }
    DB::transaction(function () use ($request, &$validated) {

        if ($request->hasFile('dokumentasi')) {
            $validated['dokumentasi'] = $request->file('dokumentasi')->store('dokumentasi', 'public');
        }

        // Simpan data utama kejadian (nip_pengawas langsung disimpan)
        $kejadian = \App\Models\Kejadian::create($validated);

        // Simpan data korban
        if ($request->has('korban')) {
            foreach ($request->korban as $korban) {
                \App\Models\Korban::create([
                    'id_kejadian'        => $kejadian->id_kejadian,
                    'id_kategori_korban' => $korban['id_kategori_korban'] ?? null,
                    'id_kategori_umur'   => $korban['id_kategori_umur'] ?? null,
                    'L'                  => $korban['L'] ?? 0,
                    'P'                  => $korban['P'] ?? 0,
                ]);
            }
        }

        // Simpan data rumah
        \App\Models\Rumah::create([
            'id_kejadian' => $kejadian->id_kejadian,
            'rmh_rr'      => $request->rmh_rr ?? 0,
            'rmh_rs'      => $request->rmh_rs ?? 0,
            'rmh_rb'      => $request->rmh_rb ?? 0,
            'terendam'    => $request->terendam ?? 0,
            'kerugian'    => 0,
        ]);

        // Ambil ID-nya
$idKejadian = $kejadian->id_kejadian ?? $kejadian->id; // tergantung nama kolom PK di DB

// Baru masukkan ke tb_kerusakan_sosek
if ($request->filled('sosek.id_jenis_kerusakan_sosek')) {
    DB::table('tb_kerusakan_sosek')->insert([
        'id_jenis_kerusakan_sosek' => $request->sosek['id_jenis_kerusakan_sosek'],
        'luas' => $request->sosek['luas'] ?? null,
        'sosek_rr' => $request->sosek['rr'] ?? 0,
        'sosek_rs' => $request->sosek['rs'] ?? 0,
        'sosek_rb' => $request->sosek['rb'] ?? 0,
        'sosek_terendam' => $request->sosek['terendam'] ?? 0,
        'kerugian' => 0,
        'id_kejadian' => $idKejadian, // wajib isi ini
    ]);

}

        // === 3️⃣ Simpan data Sarpras jika diisi ===
        $kejadian = Kejadian::create($request->all());

DB::table('tb_kerusakan_sarpras')->insert([
    'id_jenis_kerusakan_sarpras' => $request->sarpras['id_jenis_kerusakan_sarpras'],
    'sarpras_rr'       => $request->sarpras['rr'] ?? 0,
    'sarpras_rs'       => $request->sarpras['rs'] ?? 0,
    'sarpras_rb'       => $request->sarpras['rb'] ?? 0,
    'sarpras_terendam' => $request->sarpras['terendam'] ?? 0,
    'id_kejadian'      => $kejadian->id_kejadian, // ✅ hanya ID
]);


        // Simpan Pelayanan / Fasilitas Pendidikan
            if ($request->filled('id_jenis_kerusakan_pelayanandasar')) {
    Pelayanan::create([
        'id_kejadian' => $kejadian->id_kejadian,
        'id_jenis_kerusakan_pelayanandasar' => $request->id_jenis_kerusakan_pelayanandasar,
        'pelayanan_rr' => $request->pelayanan_rr ?? 0,
        'pelayanan_rs' => $request->pelayanan_rs ?? 0,
        'pelayanan_rb' => $request->pelayanan_rb ?? 0,
        'pelayanan_terendam' => $request->pelayanan_terendam ?? 0,
        'taksiran' => $request->taksiran ?? null,
    ]);
}
    });

    return redirect()->route('kejadian')->with('success', 'Data kejadian berhasil ditambahkan!');
}


    /** ===============================
     *  EDIT KEJADIAN
     *  =============================== */
    public function edit($id_kejadian)
{
    $kejadian = Kejadian::with(['korban', 'rumah', 'sosek', 'sarpras', 'pelayanan'])->findOrFail($id_kejadian);

    $jenisKerusakan = layan::all();
    $jenisKerusakan2 = sosekk::all();      // <= tambahkan ini
    $jenisKerusakan3 = sarprass::all();

    return view('edit', [
        'kejadian'       => $kejadian,
        'jenisBencana'   => JenisBencana::all(),
        'namaKejadian'   => \App\Models\NamaKejadian::all(),
        'kategoriKorban' => KategoriKorban::all(),
        'kategoriUmur'   => KategoriUmur::all(),
        'statusDarurat'  => \App\Models\StatusDarurat::all(),
        'pengawas'       => \App\Models\Pengawas::all(),
        'kecamatan'      => Kecamatan::all(),
        'sosek'          => $kejadian->sosek()->first(),
        'sarpras'        => $kejadian->sarpras()->first(),
        'pelayanan'      => $kejadian->pelayanan()->first(),
        'jenisKerusakan'  => $jenisKerusakan,
        'jenisKerusakan2' => $jenisKerusakan2, // <= kirim ke view
        'jenisKerusakan3' => $jenisKerusakan3,
    ]);
}

    /** ===============================
     *  UPDATE KEJADIAN
     *  =============================== */
    public function update(Request $request, $id_kejadian)
    {
        $kejadian = Kejadian::findOrFail($id_kejadian);

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
            'nip_pengawas'     => 'nullable|string|exists:tb_pengawas,nip_pengawas',
            'dokumentasi'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('dokumentasi')) {
            $validated['dokumentasi'] = $request->file('dokumentasi')->store('dokumentasi', 'public');
        }

        $kejadian->update($validated);

        // Update relasi dengan helper internal
        $this->syncRelations($kejadian, $request);

        return redirect()->route('kejadian')->with('success', 'Data kejadian berhasil diperbarui!');
    }

    /** Helper untuk update relasi */
    private function syncRelations(Kejadian $kejadian, Request $request)
    {
        // Korban
        $kejadian->korban()->delete();
        if ($request->has('korban')) {
            foreach ($request->korban as $korban) {
                $kejadian->korban()->create([
                    'id_kategori_korban' => $korban['id_kategori_korban'] ?? null,
                    'id_kategori_umur'   => $korban['id_kategori_umur'] ?? null,
                    'L'                  => $korban['L'] ?? 0,
                    'P'                  => $korban['P'] ?? 0,
                ]);
            }
        }

        // Rumah
        $kejadian->rumah()->delete();
        $kejadian->rumah()->create([
            'rmh_rr'   => $request->rmh_rr ?? 0,
            'rmh_rs'   => $request->rmh_rs ?? 0,
            'rmh_rb'   => $request->rmh_rb ?? 0,
            'terendam' => $request->terendam ?? 0,
            'kerugian' => $request->kerugian ?? 0,
        ]);

        // Sosek
        $kejadian->sosek()->updateOrCreate([], [
            'sosek_rr'       => $request->sosek_rr ?? 0,
            'sosek_rs'       => $request->sosek_rs ?? 0,
            'sosek_rb'       => $request->sosek_rb ?? 0,
            'sosek_terendam' => $request->sosek_terendam ?? 0,
            'kerugian'       => $request->kerugian ?? 0,
        ]);

        // Sarpras
        $kejadian->sarpras()->updateOrCreate([], [
            'sarpras_rr'       => $request->sarpras_rr ?? 0,
            'sarpras_rs'       => $request->sarpras_rs ?? 0,
            'sarpras_rb'       => $request->sarpras_rb ?? 0,
            'sarpras_terendam' => $request->sarpras_terendam ?? 0,
            'taksiran'         => $request->taksiran ?? 0,
        ]);

        // Pelayanan
        $kejadian->pelayanan()->updateOrCreate([], [
            'pelayanan_rr'       => $request->pelayanan_rr ?? 0,
            'pelayanan_rs'       => $request->pelayanan_rs ?? 0,
            'pelayanan_rb'       => $request->pelayanan_rb ?? 0,
            'pelayanan_terendam' => $request->pelayanan_terendam ?? 0,
            'taksiran'           => $request->taksiran ?? 0,
        ]);
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
        
        'rumah',
        'korban.kategoriKorban',
        'korban.kategoriUmur',
        'sosek',
        'sarpras',
        'pelayanan',
        'pengawas'
    ])->findOrFail($id_kejadian);

    return view('print', [
        'kejadian' => [$kejadian],
        'tanggal' => $kejadian->tanggal
    ]);
}

    public function printByTanggal(Request $request)
    {
        $tanggal = $request->tanggal;
        $kejadian = Kejadian::with(['jenisBencana', 'namaKejadian', 'desa', 'kecamatan', 'kabupaten', 'rumah'])
            ->whereDate('tanggal', $tanggal)
            ->get();

        return view('print', compact('kejadian', 'tanggal'));
    }

    public function show($id)
    {
        $kejadian = Kejadian::with(['korban', 'rumah', 'sosek', 'sarpras', 'pelayanan', 'pengawas'])
            ->findOrFail($id);

        return view('show', compact('kejadian'));
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
