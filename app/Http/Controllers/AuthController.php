<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Kejadian;
use App\Models\AktivitasGunung;
use App\Models\Rain;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /** =======================
     * HALAMAN LOGIN
     * ======================= */
    public function showLoginForm()
    {
        return view('loginAdmin');
    }

    /** Proses login (khusus admin dashboard) */
    public function login(Request $request)
    {
        $request->validate([
            'nama'     => ['required','string'],
            'password' => ['required','string','min:6'],
        ]);

        $field = str_contains($request->nama, '@') ? 'email' : 'nama';
        $user  = User::where($field, $request->nama)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['nama' => 'Nama/Email atau password salah'])->withInput();
        }

        $role = strtolower(trim($user->role ?? ''));
        if (!in_array($role, ['admin', 'administrator'], true)) {
            return back()->withErrors(['nama' => 'Akun ini bukan admin.'])->withInput();
        }

        if (!in_array(strtolower($user->status ?? ''), ['approved', 'aktif'], true)) {
            return back()->withErrors(['nama' => 'Akun Anda belum disetujui admin.'])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /** =======================
     * HALAMAN REGISTER
     * ======================= */
    public function showRegisterForm()
    {
        return view('register');
    }

    /** Proses register */
    public function registerProcess(Request $request)
    {
        $request->validate([
            'nama'     => ['required','string','max:255'],
            'email'    => ['required','email', Rule::unique(User::class, 'email')],
            'no_hp'    => ['required','string','max:30'],
            'password' => ['required','confirmed','min:6'],
            'role'     => ['required','in:User,Admin'],
        ]);

        User::create([
            'role'        => $request->role,
            'nama'        => $request->nama,
            'email'       => $request->email,
            'no_hp'       => $request->no_hp,
            'password'    => Hash::make($request->password),
            'photo'       => '',
            'status'      => 'pending',
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil dikirim. Tunggu persetujuan admin.');
    }

    /** =======================
     * LUPA / RESET PASSWORD
     * ======================= */
    public function showForgotPasswordForm()
    {
        return view('lupa-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(
            [
                'email' => [
                    'required',
                    'email',
                    Rule::exists(User::class, 'email'),
                ],
            ],
            ['email.exists' => 'Email tidak terdaftar.']
        );

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required','email'],
            'password' => [
                'required',
                PasswordRule::min(8)->mixedCase()->numbers()->symbols(),
                'confirmed'
            ],
        ]);

        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /** =======================
     * PROFIL
     * ======================= */

    public function showFilterForm()
    {
        return view('users.laporan-filter', [
            'from' => now()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ]);
    }
    
    public function editProfile()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'nama'     => ['required','string','max:100'],
            'email'    => [ 'required','email', Rule::unique(User::class,'email')->ignore($user->getKey()) ],
            'password' => ['nullable','min:6','confirmed'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('ok','Profil berhasil diperbarui');
    }

    /** =======================
     * LOGOUT
     * ======================= */
    public function logout(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->forceFill(['last_seen_at' => now()])->saveQuietly();
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /** =========================================================
     *  CETAK LAPORAN HARIAN EOC (Preview/Unduh HTML dulu)
     *  Endpoint: GET /beranda/cetak-laporan-eoc?date=YYYY-MM-DD&preview=1
     * ========================================================= */
    public function laporanHarian(Request $r)
{
    $from = $r->query('from', now()->format('Y-m-d'));
    $to = $r->query('to', now()->format('Y-m-d'));

    // Parse tanggal dengan format yang benar
    $start = Carbon::parse($from . ' 07:00:00')->setTimezone('Asia/Jakarta');
    $end = Carbon::parse($to . ' 07:00:00')->addDay()->subSecond()->setTimezone('Asia/Jakarta');

    // ==== ambil lat & lon dari query atau gunakan default (Kabupaten Malang)
    $lat = $r->query('lat', -8.1463);   // default Malang
    $lon = $r->query('lon', 112.6084);

    // ==== ambil data cuaca dari Open-Meteo API ====
    $cuacaText = 'Hujan Ringan – Hujan Deras';
    $temperature = '28';
    $precip = null;
    $windSpeed = '15';
    $windDir = 'Barat Daya';

    try {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,precipitation,weather_code,wind_speed_10m,wind_direction_10m&timezone=Asia%2FJakarta";
        $response = @file_get_contents($url);
        
        if ($response !== false) {
            $weatherData = json_decode($response, true);
            
            if (isset($weatherData['current'])) {
                $current = $weatherData['current'];
                $temperature = $current['temperature_2m'] ?? $temperature;
                $precip = $current['precipitation'] ?? $precip;
                $windSpeed = $current['wind_speed_10m'] ?? $windSpeed;
                $windDir = $current['wind_direction_10m'] ?? $windDir;

                // terjemahkan weather_code jadi teks singkat
                $code = $current['weather_code'] ?? 0;
                $cuacaText = match(true) {
                    $code >= 0 && $code <= 3 => 'Cerah – Berawan',
                    $code >= 45 && $code <= 48 => 'Berkabut',
                    $code >= 51 && $code <= 67 => 'Hujan Ringan',
                    $code >= 71 && $code <= 77 => 'Hujan Salju Ringan',
                    $code >= 80 && $code <= 82 => 'Hujan Lebat',
                    $code >= 95 => 'Badai / Petir',
                    default => 'Cerah Berawan',
                };
            }
        }
    } catch (\Exception $e) {
        // fallback jika API gagal
        \Log::error('Open-Meteo API error: ' . $e->getMessage());
    }

    // === KEJADIAN - Fix query dengan proper timestamp handling
    $kejadian = Kejadian::query()
        ->whereDate('tanggal', '>=', $start->toDateString())
        ->whereDate('tanggal', '<=', $end->toDateString())
        ->with([
            'jenisBencana',
            'kecamatan',
            'desa',
            'korban.kategoriKorban',
            'korban.kategoriUmur',
            'rumah',
            'sosek',
            'sarpras',
            'pelayanan',
            'statusDarurat',
            'pengawas'
        ])
        ->orderBy('tanggal', 'desc')
        ->orderBy('waktu', 'desc')
        ->get();

    // Format kejadian untuk view
    $kejadianFormatted = $kejadian->map(function ($item) {
        // Format waktu
        $waktuFormatted = '-';
        try {
            if ($item->tanggal && $item->waktu) {
                $waktuFormatted = Carbon::parse($item->tanggal . ' ' . $item->waktu)
                    ->format('d/m/Y H:i');
            } elseif ($item->tanggal) {
                $waktuFormatted = Carbon::parse($item->tanggal)->format('d/m/Y');
            }
        } catch (\Exception $e) {
            // Keep default
        }

        // Format lokasi
        $lokasiParts = [];
        if ($item->alamat) {
            $lokasiParts[] = $item->alamat;
        }
        if ($item->desa) {
            $lokasiParts[] = 'Desa ' . $item->desa->desa;
        }
        if ($item->kecamatan) {
            $lokasiParts[] = 'Kec. ' . $item->kecamatan->kecamatan;
        }
        $lokasi = !empty($lokasiParts) ? implode(', ', $lokasiParts) : '-';

        // Format sumber
        $sumber = $item->sumber ?? '-';
        
        // Dokumentasi
        $dokumentasi = [];
        if ($item->dokumentasi) {
            $docs = json_decode($item->dokumentasi, true);
            if (is_array($docs)) {
                foreach ($docs as $doc) {
                    if ($doc) {
                        $dokumentasi[] = asset('storage/' . $doc);
                    }
                }
            }
        }

        return [
            'tanggal' => $item->tanggal,
            'waktu' => $item->waktu,
            'waktu_formatted' => $waktuFormatted,
            'jenis_bencana' => $item->jenisBencana->jenis_bencana ?? '-',
            'nama_kejadian' => $item->nama_kejadian,
            'lokasi' => $lokasi,
            'kecamatan' => $item->kecamatan->kecamatan ?? '-',
            'desa' => $item->desa->desa ?? '-',
            'alamat' => $item->alamat,
            'sumber' => $sumber,
            'kronologi' => $item->kronologi,
            'penyebab' => $item->penyebab,
            'deskripsi' => $item->deskripsi,
            'kondisi_mutakhir' => $item->kondisi_mutakhir,
            'upaya' => $item->upaya,
            'unsur' => $item->unsur,
            'logistik' => $item->logistik,
            'sebaran_dampak' => $item->sebaran_dampak,
            'status_darurat' => $item->statusDarurat->status ?? '-',
            'kib' => $item->kib,
            'latitude' => $item->latitude,
            'longitude' => $item->longitude,
            
            // Relasi data
            'korban' => $item->korban,
            'rumah' => $item->rumah,
            'sosek' => $item->sosek,
            'sarpras' => $item->sarpras,
            'pelayanan' => $item->pelayanan,
            'pengawas' => $item->pengawas,
            
            // Dokumentasi
            'dokumentasi' => $dokumentasi,
        ];
    });

    // === AKTIVITAS GUNUNG
    $gunungRows = AktivitasGunung::query()
        ->whereDate('tanggal', '>=', $start->toDateString())
        ->whereDate('tanggal', '<=', $end->toDateString())
        ->orderBy('tanggal', 'desc')
        ->get();

    $gunung = $gunungRows->map(function ($it) {
        $dokumentasi = [];
        if ($it->dokumentasi_path) {
            $docs = json_decode($it->dokumentasi_path, true);
            if (is_array($docs)) {
                foreach ($docs as $doc) {
                    $dokumentasi[] = asset('storage/' . $doc);
                }
            } else {
                // Jika string biasa
                $dokumentasi[] = asset('storage/' . $it->dokumentasi_path);
            }
        }

        return [
            'nama' => $it->gunung ?? 'Gunung Api',
            'tanggal' => $it->tanggal ? Carbon::parse($it->tanggal)->locale('id')->translatedFormat('d F Y') : '-',
            'meteorologi' => $it->meteorologi ?? '-',
            'visual' => $it->visual ?? '-',
            'aktivitas' => $it->aktivitas_vulkanik ?? '-',
            'rekomendasi' => $it->rekomendasi ?? '-',
            'dokumentasi' => $dokumentasi,
        ];
    });

    // === CURAH HUJAN
    // Di controller AuthController, method laporanHarian
// === CURAH HUJAN - Fix: pastikan hasil query adalah object, bukan array
$curah_hujan = Rain::query()
    ->whereDate('hari_tanggal', '>=', $start->toDateString())
    ->whereDate('hari_tanggal', '<=', $end->toDateString())
    ->with('kecamatan')
    ->orderBy('hari_tanggal', 'asc')
    ->get()
    ->map(function ($item) {
        return (object)[ // Convert ke object, bukan array
            'hari_tanggal' => $item->hari_tanggal ? Carbon::parse($item->hari_tanggal) : null,
            'kecamatan' => optional($item->kecamatan)->kecamatan ?? '-',
            'hari_hujan' => $item->hari_hujan ?? 0,
            'hari_tidak_hujan' => $item->hari_tidak_hujan ?? 0,
            'intensitas' => $item->intensitas ?? '-',
        ];
    });

    // === Data statis (tetap)
    $kop = [
        'instansi1' => 'PEMERINTAH KABUPATEN MALANG',
        'instansi2' => 'BADAN PENANGGULANGAN BENCANA DAERAH',
        'alamat' => 'Jalan Trunojoyo Kepanjen, Kabupaten Malang, Jawa Timur',
        'kontak' => 'Telepon/Faksimile (0341) 392121  Laman: bpbd.malangkab.go.id',
        'email' => 'Pos-el: bpbd@malangkab.go.id, Kode Pos: 65163',
    ];

    $surat = [
        'kota_tanggal' => 'Malang, ' . $start->locale('id')->translatedFormat('d F Y'),
        'tujuan' => 'Yth. Bapak Bupati Malang',
        'tujuan_kota' => 'M A L A N G',
        'nomor' => '360/' . $start->format('m') . '/426.205/' . $start->format('Y'),
        'sifat' => 'Penting',
        'lampiran' => '1 (satu) berkas',
        'perihal' => 'Laporan Harian Pusdalops',
        'paragraf' => 'Bersama ini disampaikan dengan hormat Laporan Harian Pusat Pengendali Operasi Penanggulangan Bencana (Pusdalops PB) BPBD Kabupaten Malang tanggal ' . 
                     $start->locale('id')->translatedFormat('d') . ' - ' . $end->locale('id')->translatedFormat('d F Y') . 
                     ' Pukul 07.00 WIB, berdasarkan pantauan petugas piket dan laporan dari masyarakat Kabupaten Malang sebagaimana terlampir.',
        'penutup' => 'Demikian untuk menjadikan periksa.',
        'pejabat' => [
            'jabatan' => 'Plt. KEPALA PELAKSANA BPBD KABUPATEN MALANG',
            'nama' => 'R. ICHWANUL MUSLIMIN S.H., M.Si.',
            'pangkat' => 'Pembina Tingkat I',
            'nip' => 'NIP. 196807061998031006',
        ],
        'tembusan' => [
            'Bp. Kepala BNPB di Jakarta',
            'Bp. Kepala Pelaksana BPBD Prov. Jatim di Sidoarjo',
            'Bp. Sekretaris Daerah Kab. Malang selaku Kepala Ex Officio BPBD Kab. Malang',
        ],
    ];

    $prakiraan_cuaca = [
        'rentang' => $start->locale('id')->translatedFormat('l, d F Y') . ' – ' . 
                    $end->locale('id')->translatedFormat('l, d F Y') . ' (07.00 – 07.00 WIB)',
        'cuaca' => $cuacaText,
        'suhu' => $temperature ? "{$temperature}°C" : '23 – 32°C',
        'kelembaban' => $precip ? "{$precip} mm" : '75 – 95%',
        'kecepatan' => $windSpeed ? "{$windSpeed} km/jam" : '30 km/jam',
        'arah' => $this->convertWindDirection($windDir) ?? 'Barat Daya – Barat Laut',
    ];

    // Data hotspot (contoh - bisa diganti dengan data real)
    $hotspot = [
        'pukul1' => '16.00 WIB',
        'data1' => [
            ['kecamatan' => 'PAGAK', 'jumlah' => '3'],
            ['kecamatan' => 'BANTUR', 'jumlah' => '2'],
            ['kecamatan' => 'DONOMULYO', 'jumlah' => '1'],
            ['kecamatan' => 'AMPELGADING', 'jumlah' => '1'],
            ['kecamatan' => 'SUMBERMANJING WETAN', 'jumlah' => '1'],
        ],
        'pukul2' => '05.00 WIB',
        'data2' => [
            ['kecamatan' => 'PAGAK', 'jumlah' => '2'],
            ['kecamatan' => 'BANTUR', 'jumlah' => '1'],
            ['kecamatan' => 'SUMBERMANJING WETAN', 'jumlah' => '1'],
        ],
    ];

    $peringatan_dini = [
        'radio' => 'VHF 169.525 MHz via Repeater BPBD Kab. Malang dan frekuensi komunitas di wilayah kabupaten.',
        'jejaring' => 'Jejaring sosial & ponsel monitoring 082244094886.',
    ];

    $gelombang = [[
        'arah' => 'Barat Daya – Barat Laut',
        'kts' => '16',
        'cuaca' => 'Hujan Ringan – Hujan Deras',
        'sig' => '0,1',
        'max' => '0,3'
    ]];

    $radio = [
        ['kecamatan' => 'Donomulyo', 'pantauan' => 'Hujan Ringan – Hujan Deras'],
        ['kecamatan' => 'Pagak', 'pantauan' => 'Hujan Ringan – Hujan Deras'],
        ['kecamatan' => 'Bantur', 'pantauan' => 'Hujan Ringan – Hujan Deras'],
        ['kecamatan' => 'Ampelgading', 'pantauan' => 'Hujan Ringan'],
        ['kecamatan' => 'Sumbermanjing Wetan', 'pantauan' => 'Hujan Deras'],
    ];

    $ttd = [
        'manager' => [
            'jabatan' => "Manajer Pusdalops PB\nBPBD Kabupaten Malang",
            'nama' => 'ZAINUDDIN, S.H.',
            'pangkat' => 'Penata Tingkat I',
            'nip' => 'NIP. 198006231999011001',
        ],
        'piket' => [
            ['label' => 'Petugas Piket,', 'nama' => 'HANUGRAH'],
            ['label' => 'Petugas Piket,', 'nama' => 'EKO APRILIANTO'],
        ],
        'tanggal_bawah' => 'Malang, ' . $end->locale('id')->translatedFormat('d F Y'),
    ];

    return view('users.laporan-harian', compact(
        'kop', 'surat', 'hotspot', 'peringatan_dini', 'prakiraan_cuaca', 'gelombang', 'radio', 'ttd',
        'start', 'end', 'kejadian', 'kejadianFormatted', 'gunung', 'curah_hujan'
    ));
}

/**
 * Convert wind direction from degrees to cardinal direction
 */
private function convertWindDirection($degrees)
    {
        if (!is_numeric($degrees)) {
            return $degrees;
        }
        
        $directions = [
            'Utara' => [0, 22.5],
            'Timur Laut' => [22.5, 67.5],
            'Timur' => [67.5, 112.5],
            'Tenggara' => [112.5, 157.5],
            'Selatan' => [157.5, 202.5],
            'Barat Daya' => [202.5, 247.5],
            'Barat' => [247.5, 292.5],
            'Barat Laut' => [292.5, 337.5],
            'Utara' => [337.5, 360],
        ];
        
        foreach ($directions as $direction => $range) {
            if ($degrees >= $range[0] && $degrees < $range[1]) {
                return $direction;
            }
        }
        
        return 'Tidak diketahui';
    }
}