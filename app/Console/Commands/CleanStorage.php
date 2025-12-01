<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanStorage extends Command
{
    protected $signature = 'clean:storage';
    protected $description = 'Clean all files from MinIO storage';

    public function handle()
    {
        $disk = Storage::disk('minio');
        $this->info('Cleaning MinIO storage...');

        $directories = $disk->allDirectories();
        foreach ($directories as $directory) {
            $disk->deleteDirectory($directory);
        }

        $files = $disk->allFiles();
        $disk->delete($files);

        $this->info('MinIO storage cleaned successfully.');
    }
}
