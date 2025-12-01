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
        \DB::table('master_document_types')->truncate();

        $documentTypes = [
            // REGISTRATION Documents
            [
                'name' => 'KTP',
                'slug' => 'ktp',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['image/jpeg', 'image/png', 'image/jpg']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ijazah',
                'slug' => 'ijazah',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/jpeg', 'image/png']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Video Perkenalan',
                'slug' => 'video-perkenalan',
                'is_mandatory' => false,
                'allowed_mimetypes' => json_encode(['video/mp4', 'video/mpeg', 'video/quicktime']),
                'chunkable' => true,
                'stage' => 'REGISTRATION',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Curriculum Vitae (CV)',
                'slug' => 'cv',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Portfolio',
                'slug' => 'portfolio',
                'is_mandatory' => false,
                'allowed_mimetypes' => json_encode(['application/pdf']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // POST_HIRING Documents
            [
                'name' => 'Medical Check Up (MCU)',
                'slug' => 'mcu',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/jpeg', 'image/png']),
                'chunkable' => false,
                'stage' => 'POST_HIRING',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'name' => 'Visa / Work Permit',
            //     'slug' => 'visa-work-permit',
            //     'is_mandatory' => true,
            //     'allowed_mimetypes' => json_encode(['application/pdf', 'image/jpeg', 'image/png']),
            //     'chunkable' => false,
            //     'stage' => 'POST_HIRING',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'name' => 'Employment Contract',
            //     'slug' => 'employment-contract',
            //     'is_mandatory' => true,
            //     'allowed_mimetypes' => json_encode(['application/pdf']),
            //     'chunkable' => false,
            //     'stage' => 'POST_HIRING',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        \DB::table('master_document_types')->insert($documentTypes);
    }
}
