<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meja;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    /**
     * Display a listing of all tables with QR codes.
     */
    public function index()
    {
        $mejas = Meja::orderBy('nomor_meja')->get();

        return view('admin.meja.index', compact('mejas'));
    }

    /**
     * Store a newly created table.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_meja' => 'required|string|max:50|unique:meja,nomor_meja',
        ]);

        Meja::create([
            'nomor_meja' => $validated['nomor_meja'],
        ]);

        return redirect()->route('admin.meja.index')
            ->with('success', 'Meja ' . $validated['nomor_meja'] . ' berhasil ditambahkan.');
    }

    /**
     * Remove the specified table.
     */
    public function destroy(Meja $meja)
    {
        $nomorMeja = $meja->nomor_meja;
        $meja->delete();

        return redirect()->route('admin.meja.index')
            ->with('success', 'Meja ' . $nomorMeja . ' berhasil dihapus.');
    }

    /**
     * Regenerate the QR token for a specific table.
     */
    public function regenerateToken(Meja $meja)
    {
        $meja->update(['token' => \Illuminate\Support\Str::random(32)]);

        return redirect()->route('admin.meja.index')
            ->with('success', 'Token QR untuk Meja ' . $meja->nomor_meja . ' berhasil diperbarui.');
    }

    /**
     * Show printable page for a single QR code.
     */
    public function printQr(Meja $meja)
    {
        return view('admin.meja.print-qr', compact('meja'));
    }

    /**
     * Show printable page for all QR codes.
     */
    public function printAllQr()
    {
        $mejas = Meja::orderBy('nomor_meja')->get();

        return view('admin.meja.print-all-qr', compact('mejas'));
    }
}
