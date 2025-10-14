<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Query the user by email
$email = 'Sitinjakwirandy@gmail.com';
$user = User::where('email', $email)->first();

if ($user) {
    echo "User found:\n";
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Created at: " . $user->created_at . "\n";
    echo "Updated at: " . $user->updated_at . "\n";
} else {
    echo "User with email '{$email}' not found.\n";
}
