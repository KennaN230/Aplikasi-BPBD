<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Gempa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">

<div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-2xl">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
        Tambah Data Kejadian Gempa Bumi
    </h2>

    <form action="{{ route('gempa.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Tanggal</label>
            <input type="date" name="tanggal"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">SR</label>
            <input type="number" step="0.1" name="sr"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Waktu</label>
            <input type="time" name="waktu"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Lokasi Gempa</label>
            <input type="text" name="gempa"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
        </div>
        
        <div class="md:col-span-2 mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Lokasi di Peta</label>
            <div id="map" style="height: 300px; border-radius: 8px;"></div>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Latitude</label>
            <input id="latitude" type="text" name="latitude"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required readonly>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Longitude</label>
            <input id="longitude" type="text" name="longitude"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required readonly>
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-700 font-semibold mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" required></textarea>
        </div>

        <div class="md:col-span-2 flex justify-center gap-4 mt-4">
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold">
                Simpan
            </button>
            <a href="{{ route('gempa.index') }}"
                class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg font-semibold">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var defaultLat = -7.9797;
    var defaultLon = 112.6304;

    var map = L.map('map').setView([defaultLat, defaultLon], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker;

    map.on('click', function(e) {
        var lat = e.latlng.lat.toFixed(6);
        var lon = e.latlng.lng.toFixed(6);

        // Masukkan ke input form
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;

        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lon]).addTo(map)
            .bindPopup("Lokasi dipilih:<br>Lat: " + lat + "<br>Lon: " + lon)
            .openPopup();
    });
});
</script>

</body>
</html>
