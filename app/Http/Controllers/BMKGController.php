<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BMKGController extends Controller
{
    public function index(Request $request)
    {
        $lat = $request->input('lat', -7.9771); // default Malang
        $lon = $request->input('lon', 112.6341);

        $lokasi = "Lat: {$lat}, Lon: {$lon}";

        try {
            // === 1️⃣ Ambil nama lokasi dari OpenStreetMap ===
            $geoUrl = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lon}&format=json";
            $geoRes = Http::withHeaders(['User-Agent' => 'LaravelCuacaApp'])->get($geoUrl);
            if ($geoRes->ok() && isset($geoRes['display_name'])) {
                $lokasi = $geoRes['display_name'];
            }

            // === 2️⃣ Ambil data cuaca dari Open-Meteo ===
            $weatherUrl = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,precipitation,wind_speed_10m";
            $res = Http::get($weatherUrl);

            if ($res->failed()) {
                throw new \Exception('Gagal mengambil data cuaca.');
            }

            $data = $res->json();

            $hasil = [
                'lokasi' => $lokasi,
                'suhu' => $data['current']['temperature_2m'] ?? '-',
                'hujan' => $data['current']['precipitation'] ?? '-',
                'angin' => $data['current']['wind_speed_10m'] ?? '-',
                'waktu' => $data['current']['time'] ?? now(),
            ];
        } catch (\Exception $e) {
            $hasil = [
                'lokasi' => 'Error',
                'suhu' => '-', 'hujan' => '-', 'angin' => '-',
                'waktu' => now(),
                'error' => $e->getMessage(),
            ];
        }

        return view('testing.cuaca', compact('hasil'));
    }
}
