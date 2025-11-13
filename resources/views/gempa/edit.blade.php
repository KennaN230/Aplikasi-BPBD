<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Gempa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-2xl">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Edit Data Kejadian Gempa Bumi
        </h2>

        <!-- Form Edit Data -->
        <form action="{{ route('gempa.update', $gempa->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')

            <!-- Tanggal -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Tanggal</label>
                <input type="date" name="tanggal"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                       value="{{ \Carbon\Carbon::parse($gempa->tanggal)->format('Y-m-d') }}" required>
            </div>

            <!-- SR -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">SR</label>
                <input type="number" step="0.1" name="sr"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                       value="{{ $gempa->sr }}" required>
            </div>

            <!-- Waktu -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Waktu</label>
                <input type="time" name="waktu"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                       value="{{ $gempa->waktu }}" required>
            </div>

            <!-- Lokasi Gempa -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Lokasi Gempa</label>
                <input type="text" name="gempa"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
                       value="{{ $gempa->gempa }}" required>
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"
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

</body>
</html>
