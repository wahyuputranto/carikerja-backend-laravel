<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            [
                'name' => 'KTP',
                'slug' => 'ktp',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['image/jpeg', 'image/png', 'image/jpg']),
                'chunkable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ijazah',
                'slug' => 'ijazah',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/jpeg', 'image/png']),
                'chunkable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Video Perkenalan',
                'slug' => 'video-perkenalan',
                'is_mandatory' => false,
                'allowed_mimetypes' => json_encode(['video/mp4', 'video/mpeg', 'video/quicktime']),
                'chunkable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('master_document_types')->insert($documentTypes);
    }
}
