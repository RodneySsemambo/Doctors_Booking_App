<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Find or create admin user
$admin = User::where('email', 'admin@example.com')->first();

if ($admin) {
    $admin->update([
        'password' => Hash::make('admin123'),
        'user_type' => 'admin',
    ]);
    echo "Admin user updated. Password: admin123\n";
} else {
    $admin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('admin123'),
        'user_type' => 'admin',
    ]);
    echo "Admin user created. Password: admin123\n";
}

// Give admin role if using Spatie
if (method_exists($admin, 'assignRole')) {
    $admin->assignRole('super_admin');
    echo "Assigned super_admin role\n";
}

echo "Login at: /admin\n";
echo "Email: admin@example.com\n";
echo "Password: admin123\n";
