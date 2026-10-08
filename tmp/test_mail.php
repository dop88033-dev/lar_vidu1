<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Auth\Events\Registered;

echo "=== TESTING EMAIL VERIFICATION NOTIFICATION ===\n";

$testUser = User::where('email', 'test_verify@example.com')->first();
if (!$testUser) {
    $testUser = User::create([
        'name' => 'Test User',
        'email' => 'test_verify@example.com',
        'password' => bcrypt('password123'),
        'role' => 'customer',
    ]);
}

try {
    event(new Registered($testUser));
    echo "SUCCESS: Event Registered fired and verification email queued/sent!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
