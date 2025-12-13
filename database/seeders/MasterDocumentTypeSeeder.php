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

            [
                'name' => 'Curriculum Vitae (CV)',
                'slug' => 'cv',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-07 13:12:39',
                'updated_at' => '2025-12-08 17:22:36',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => 'Ini cv ya..',
                'template' => null
            ],
            [
                'name' => 'KTP',
                'slug' => 'ktp',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['image/jpeg', 'image/png', 'image/jpg']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-07 13:12:39',
                'updated_at' => '2025-12-07 13:12:39',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => null
            ],
            [
                'name' => 'Ijazah',
                'slug' => 'ijazah',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/jpeg', 'image/png']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-07 13:12:39',
                'updated_at' => '2025-12-07 13:12:39',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => null
            ],
            [
                'name' => 'Video Perkenalan',
                'slug' => 'video-perkenalan',
                'is_mandatory' => false,
                'allowed_mimetypes' => json_encode(['video/mp4', 'video/mpeg', 'video/quicktime']),
                'chunkable' => true,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-07 13:12:39',
                'updated_at' => '2025-12-07 13:12:39',
                'deleted_at' => null,
                'max_size' => 12000,
                'notes' => null,
                'template' => null
            ],
            [
                'name' => 'Portfolio',
                'slug' => 'portfolio',
                'is_mandatory' => false,
                'allowed_mimetypes' => json_encode(['application/pdf']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-07 13:12:39',
                'updated_at' => '2025-12-08 16:12:14',
                'deleted_at' => '2025-12-08 16:12:14',
                'max_size' => 1000,
                'notes' => null,
                'template' => null
            ],
//            [
//                'name' => 'Medical Check Up (MCU)',
//                'slug' => 'mcu',
//                'is_mandatory' => true,
//                'allowed_mimetypes' => json_encode(['application/pdf', 'image/jpeg', 'image/png']),
//                'chunkable' => false,
//                'stage' => 'POST_HIRING',
//                'created_at' => '2025-12-07 13:12:39',
//                'updated_at' => '2025-12-07 13:12:39',
//                'deleted_at' => null,
//                'max_size' => 1000,
//                'notes' => null,
//                'template' => null
//            ],
//            [
//                'name' => 'SKCK',
//                'slug' => 'skck',
//                'is_mandatory' => true,
//                'allowed_mimetypes' => json_encode(['application/pdf']),
//                'chunkable' => false,
//                'stage' => 'REGISTRATION',
//                'created_at' => '2025-12-08 14:10:14',
//                'updated_at' => '2025-12-08 14:10:14',
//                'deleted_at' => null,
//                'max_size' => 1000,
//                'notes' => null,
//                'template' => null
//            ],
            [
                'name' => 'Paspor',
                'slug' => 'paspor',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/png']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-08 14:10:33',
                'updated_at' => '2025-12-08 14:10:33',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => null
            ],
            [
                'name' => 'Kartu Keluarga',
                'slug' => 'kartu-keluarga',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/png']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-08 14:11:06',
                'updated_at' => '2025-12-08 14:11:06',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => null
            ],
            [
                'name' => 'Akte Kelahiran',
                'slug' => 'akte-kelahiran',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/png']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-08 14:12:09',
                'updated_at' => '2025-12-08 17:31:12',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => 'Notes (Optional)',
                'template' => null
            ],
            [
                'name' => 'Pass Foto 5 x 5 (Formal background putih)',
                'slug' => 'pass-foto-5-x-5-formal-background-putih',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['image/png', 'application/pdf']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-08 14:12:38',
                'updated_at' => '2025-12-08 14:22:58',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => null
            ],
            [
                'name' => 'Foto Fullbody (Formal)',
                'slug' => 'foto-fullbody-formal',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['image/png', 'image/jpeg']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-08 14:13:24',
                'updated_at' => '2025-12-08 14:22:32',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => null
            ],
            [
                'name' => 'Paklaring (Hospitality)',
                'slug' => 'paklaring-hospitality',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['image/png', 'application/pdf', 'image/jpeg']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-08 14:14:30',
                'updated_at' => '2025-12-08 14:24:31',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => null
            ],
            [
                'name' => 'Surat Izin Keluarga',
                'slug' => 'surat-izin-keluarga',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/jpeg', 'image/png']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-08 14:16:23',
                'updated_at' => '2025-12-08 17:31:56',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => 'document_templates/jp8ZVSCibnDnB2zwSqtgl5LwwebApbXQNlXFuYdw.doc'
            ],
            [
                'name' => 'Tanda tangan',
                'slug' => 'tanda-tangan',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/png', 'image/jpeg']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-08 14:17:24',
                'updated_at' => '2025-12-08 14:17:33',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => null
            ],
            [
                'name' => 'Surat Pernyataan Status Nikah',
                'slug' => 'surat-pernyataan-status-nikah',
                'is_mandatory' => true,
                'allowed_mimetypes' => json_encode(['application/pdf', 'image/png']),
                'chunkable' => false,
                'stage' => 'REGISTRATION',
                'created_at' => '2025-12-08 14:20:00',
                'updated_at' => '2025-12-08 17:56:40',
                'deleted_at' => null,
                'max_size' => 1000,
                'notes' => null,
                'template' => 'http://127.0.0.1:9000/agency-documents/document_templates/Td5c9WyBmBrDc2J9ojBJ4pyjvWnvWWvgYjCmkqPA.doc'
            ],
        ];

        \DB::table('master_document_types')->insert($documentTypes);
    }
}
