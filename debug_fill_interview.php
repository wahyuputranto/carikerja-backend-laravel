<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$application = \App\Models\Application::latest()->first();

if ($application) {
    echo "Updating Application ID: " . $application->id . "\n";
    
    $application->update([
        'interview_date' => now()->addDays(3),
        'interview_location' => 'https://meet.google.com/abc-defg-hij',
        'interview_notes' => 'Harap membawa CV fisik dan berpakaian rapi.',
        'current_step' => 4 // Pastikan step juga benar
    ]);
    
    echo "Interview details updated with dummy data.\n";
} else {
    echo "No application found.\n";
}
