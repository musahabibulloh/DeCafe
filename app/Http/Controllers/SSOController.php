<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SSOController extends Controller
{
    public function index()
    {
        if (config('services.google.client_id')) {
            return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
        }
        return view('auth.sso_login');
    }

    public function callback(Request $request)
    {
        if (config('services.google.client_id')) {
            try {
                $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();
                $email = $googleUser->getEmail();
                $name = $googleUser->getName();
            } catch (\Exception $e) {
                return redirect()->route('login')->with('error', 'Gagal masuk menggunakan Google: ' . $e->getMessage());
            }

            $user = User::where('email', $email)->first();

            if ($user) {
                Auth::login($user);
                $request->session()->regenerate();

                return $this->redirectByRole($user->role)
                    ->with('success', 'Login Google berhasil.');
            }

            // Store email and name in session to be used in registration step
            session(['sso_email' => $email, 'sso_name' => $name]);

            return redirect()->route('sso.google.register');
        }

        // Mock SSO logic (runs when GOOGLE_CLIENT_ID is not configured in .env)
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return $this->redirectByRole($user->role)
                ->with('success', 'Login SSO berhasil (Mode Simulasi).');
        }

        // Store email in session to be used in registration step
        session(['sso_email' => $email, 'sso_name' => null]);

        return redirect()->route('sso.google.register');
    }

    public function showRegister()
    {
        $email = session('sso_email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Sesi SSO tidak valid.');
        }

        return view('auth.sso_register', compact('email'));
    }

    public function register(Request $request)
    {
        $email = session('sso_email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Sesi SSO tidak valid.');
        }

        // Double check if user already exists to prevent duplicate SQL exception
        $user = User::where('email', $email)->first();

        if ($user) {
            session()->forget(['sso_email', 'sso_name']);
            Auth::login($user);
            $request->session()->regenerate();
            return $this->redirectByRole($user->role)->with('success', 'Akun Anda sudah terdaftar.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create new user (Role defaults to customer for registrations)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'password' => Hash::make(Str::random(24)), // Random secure password
            'role' => 'customer',
        ]);

        // Clear SSO data from session
        session()->forget(['sso_email', 'sso_name']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')
            ->with('success', 'Akun berhasil dibuat menggunakan Google.');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'kasir' => redirect()->route('kasir.dashboard'),
            'pelayan' => redirect()->route('pelayan.dashboard'),
            'dapur' => redirect()->route('dapur.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
