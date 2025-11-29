<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$application = \App\Models\Application::latest()->first();

if ($application) {
    echo "Application ID: " . $application->id . "\n";
    echo "Status: " . $application->status . "\n";
    echo "Current Step: " . $application->current_step . "\n";
    echo "Interview Date: " . ($application->interview_date ?? 'NULL') . "\n";
    echo "Interview Location: " . ($application->interview_location ?? 'NULL') . "\n";
    echo "Interview Notes: " . ($application->interview_notes ?? 'NULL') . "\n";
} else {
    echo "No application found.\n";
}
