<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Gempa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        #map { height: 300px; width: 100%; border-radius: 0.5rem; }
    </style>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-2xl">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Edit Data Kejadian Gempa Bumi
        </h2>

        <form action="{{ route('gempa.update', $gempa->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')

            <!-- Tanggal -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Tanggal</label>
                <input type="date" name="tanggal"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                       value="{{ \Carbon\Carbon::parse($gempa->tanggal)->format('Y-m-d') }}" required>
            </div>

            <!-- SR -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">SR</label>
                <input type="number" step="0.1" name="sr"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                       value="{{ $gempa->sr }}" required>
            </div>

            <!-- Waktu -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Waktu</label>
                <input type="time" name="waktu"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                       value="{{ $gempa->waktu }}" required>
            </div>

            <!-- Lokasi Gempa -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Lokasi Gempa</label>
                <input type="text" name="gempa"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                       value="{{ $gempa->gempa }}" required>
            </div>

            <!-- Latitude -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Latitude</label>
                <input type="number" step="0.000001" name="latitude" id="latitude"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                       value="{{ $gempa->latitude }}" required>
            </div>

            <!-- Longitude -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Longitude</label>
                <input type="number" step="0.000001" name="longitude" id="longitude"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                       value="{{ $gempa->longitude }}" required>
            </div>

            <!-- Peta -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-1">Peta Lokasi</label>
                <div id="map"></div>
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                          required>{{ $gempa->keterangan }}</textarea>
            </div>

            <!-- Tombol -->
            <div class="md:col-span-2 flex justify-center gap-4 mt-4">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold">
                    Simpan Perubahan
                </button>
                <a href="{{ route('gempa.index') }}"
                   class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg font-semibold">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');

    var lat = parseFloat(latInput.value) || -7.98;
    var lng = parseFloat(lngInput.value) || 112.63;

    var map = L.map('map').setView([lat, lng], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker draggable
    var marker = L.marker([lat, lng], {draggable:true}).addTo(map);

    // Update input saat marker digeser
    marker.on('dragend', function(e) {
        var pos = marker.getLatLng();
        latInput.value = pos.lat.toFixed(6);
        lngInput.value = pos.lng.toFixed(6);
    });

    // Update marker jika input diubah manual
    function updateMarker() {
        var newLat = parseFloat(latInput.value);
        var newLng = parseFloat(lngInput.value);
        if (!isNaN(newLat) && !isNaN(newLng)) {
            marker.setLatLng([newLat, newLng]);
            map.panTo([newLat, newLng]);
        }
    }

    latInput.addEventListener('change', updateMarker);
    lngInput.addEventListener('change', updateMarker);

    // Update marker dan input saat peta diklik
    map.on('click', function(e) {
        var clickedLat = e.latlng.lat;
        var clickedLng = e.latlng.lng;
        marker.setLatLng([clickedLat, clickedLng]);
        latInput.value = clickedLat.toFixed(6);
        lngInput.value = clickedLng.toFixed(6);
    });
</script>


</body>
</html>
