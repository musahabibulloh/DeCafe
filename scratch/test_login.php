<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

$email = 'admin@decafe.test';
$password = 'password';

$user = User::where('email', $email)->first();
if (!$user) {
    echo "User $email not found.\n";
    exit;
}

echo "User: " . $user->email . "\n";
echo "Role: " . $user->role . "\n";
echo "Hash: " . $user->password . "\n";
echo "Hash::check('password'): " . (Hash::check($password, $user->password) ? 'TRUE' : 'FALSE') . "\n";

$credentials = ['email' => $email, 'password' => $password];
echo "Auth::attempt: " . (Auth::attempt($credentials) ? 'TRUE' : 'FALSE') . "\n";
