<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ScanMejaController extends Controller
{
    /**
     * Handle QR code scan for a table.
     * When customer scans the QR at a table, they are logged in as a guest
     * customer and redirected to the ordering page with the table pre-selected.
     */
    public function scan(string $token)
    {
        $meja = Meja::where('token', $token)->first();

        if (!$meja) {
            return redirect()->route('login')
                ->with('error', 'QR Code meja tidak valid. Silakan hubungi pelayan.');
        }

        // If user is already logged in as customer, just set table and redirect
        if (Auth::check() && Auth::user()->role === 'customer') {
            session(['nomor_meja' => $meja->nomor_meja, 'meja_id' => $meja->id]);
            return redirect()->route('customer.dashboard')
                ->with('success', 'Anda duduk di Meja ' . $meja->nomor_meja . '. Silakan mulai memesan!');
        }

        // If logged in as non-customer role, logout first
        if (Auth::check()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        // Create a guest customer account or find existing guest for this session
        $guestName = 'Tamu Meja ' . $meja->nomor_meja;
        $guestEmail = 'tamu_meja_' . $meja->nomor_meja . '_' . Str::random(8) . '@guest.nasibakar';

        $guest = User::create([
            'name' => $guestName,
            'email' => $guestEmail,
            'password' => Hash::make(Str::random(16)),
            'role' => 'customer',
        ]);

        Auth::login($guest);
        request()->session()->regenerate();

        // Store table number in session
        session(['nomor_meja' => $meja->nomor_meja, 'meja_id' => $meja->id]);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Selamat datang di Meja ' . $meja->nomor_meja . '! Silakan mulai memesan.');
    }
}
