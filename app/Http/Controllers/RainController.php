<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rain; // pastikan ada model Rain

class RainController extends Controller
{
    public function index()
    {
        $data = Rain::all();
        return view('rain', compact('data'));
    }

    public function create()
    {
        return view('rain.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kecamatan' => 'required|string|max:100',
            'hari_hujan' => 'required|integer',
            'hari_tidak_hujan' => 'required|integer',
        ]);

        Rain::create($request->all());

        return redirect()->route('rain')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $rain = Rain::findOrFail($id);
        return view('rain.edit', compact('rain'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kecamatan' => 'required|string|max:100',
            'hari_hujan' => 'required|integer',
            'hari_tidak_hujan' => 'required|integer',
        ]);

        $rain = Rain::findOrFail($id);
        $rain->update($request->all());

        return redirect()->route('rain')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $rain = Rain::findOrFail($id);
        $rain->delete();

        return redirect()->route('rain')->with('success', 'Data berhasil dihapus');
    }
}
