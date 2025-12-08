<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Job Categories
        // Job Categories
        $categories = [
            ['name' => 'Customer Service', 'slug' => 'customer-service'],
            ['name' => 'Operations & Logistics', 'slug' => 'operations-logistics'],
            ['name' => 'Hospitality & Tourism', 'slug' => 'hospitality-tourism'],
            ['name' => 'Food & Beverage', 'slug' => 'food-beverage'],
        ];

        DB::table('master_job_categories')->truncate();
        foreach ($categories as $category) {
            DB::table('master_job_categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('master_locations')->truncate();
        // Locations - Indonesia
        $indonesia = DB::table('master_locations')->insertGetId([
            'parent_id' => null,
            'name' => 'Indonesia',
            'type' => 'COUNTRY',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Data Array (38 Provinsi & Kota Utama)
        $provincesID = [
            // --- SUMATERA ---
            'Nanggroe Aceh Darussalam' => ['Banda Aceh', 'Sabang', 'Lhokseumawe', 'Langsa', 'Aceh Besar'],
            'Sumatera Utara' => ['Medan', 'Binjai', 'Pematangsiantar', 'Tebing Tinggi', 'Deli Serdang'],
            'Sumatera Barat' => ['Padang', 'Bukittinggi', 'Payakumbuh', 'Pariaman', 'Solok'],
            'Riau' => ['Pekanbaru', 'Dumai', 'Siak', 'Kampar', 'Bengkalis'],
            'Kepulauan Riau' => ['Tanjung Pinang', 'Batam', 'Bintan', 'Karimun'],
            'Jambi' => ['Jambi', 'Sungai Penuh', 'Muaro Jambi', 'Bungo'],
            'Sumatera Selatan' => ['Palembang', 'Prabumulih', 'Lubuklinggau', 'Pagar Alam', 'Banyuasin'],
            'Bangka Belitung' => ['Pangkal Pinang', 'Tanjung Pandan', 'Bangka', 'Belitung'],
            'Bengkulu' => ['Bengkulu', 'Rejang Lebong', 'Mukomuko'],
            'Lampung' => ['Bandar Lampung', 'Metro', 'Lampung Selatan', 'Lampung Tengah'],

            // --- JAWA ---
            'DKI Jakarta' => ['Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Timur', 'Jakarta Barat', 'Jakarta Utara', 'Kepulauan Seribu'],
            'Banten' => ['Serang', 'Cilegon', 'Tangerang', 'Tangerang Selatan', 'Lebak', 'Pandeglang'],
            'Jawa Barat' => ['Bandung', 'Bekasi', 'Depok', 'Bogor', 'Cimahi', 'Sukabumi', 'Tasikmalaya', 'Cirebon', 'Karawang', 'Indramayu'],
            'Jawa Tengah' => ['Semarang', 'Surakarta (Solo)', 'Magelang', 'Pekalongan', 'Tegal', 'Salatiga', 'Banyumas', 'Cilacap', 'Kudus'],
            'DI Yogyakarta' => ['Yogyakarta', 'Sleman', 'Bantul', 'Kulon Progo', 'Gunung Kidul'],
            'Jawa Timur' => ['Surabaya', 'Malang', 'Madiun', 'Kediri', 'Mojokerto', 'Batu', 'Pasuruan', 'Probolinggo', 'Sidoarjo', 'Banyuwangi', 'Gresik'],

            // --- BALI & NUSA TENGGARA ---
            'Bali' => ['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Buleleng'],
            'Nusa Tenggara Barat' => ['Mataram', 'Bima', 'Lombok Barat', 'Lombok Tengah', 'Sumbawa'],
            'Nusa Tenggara Timur' => ['Kupang', 'Ende', 'Maumere', 'Labuan Bajo', 'Sumba Timur'],

            // --- KALIMANTAN ---
            'Kalimantan Barat' => ['Pontianak', 'Singkawang', 'Kubu Raya', 'Sambas', 'Ketapang'],
            'Kalimantan Tengah' => ['Palangka Raya', 'Kotawaringin Timur', 'Kotawaringin Barat', 'Kapuas'],
            'Kalimantan Selatan' => ['Banjarmasin', 'Banjarbaru', 'Banjar', 'Tabalong'],
            'Kalimantan Timur' => ['Samarinda', 'Balikpapan', 'Bontang', 'Kutai Kartanegara', 'Penajam Paser Utara'],
            'Kalimantan Utara' => ['Tarakan', 'Tanjung Selor', 'Nunukan', 'Malinau'],

            // --- SULAWESI ---
            'Sulawesi Utara' => ['Manado', 'Bitung', 'Tomohon', 'Kotamobagu', 'Minahasa'],
            'Gorontalo' => ['Gorontalo', 'Limboto', 'Bone Bolango'],
            'Sulawesi Tengah' => ['Palu', 'Poso', 'Luwuk', 'Donggala', 'Morowali'],
            'Sulawesi Barat' => ['Mamuju', 'Majene', 'Polewali Mandar'],
            'Sulawesi Selatan' => ['Makassar', 'Parepare', 'Palopo', 'Gowa', 'Maros', 'Bone', 'Tana Toraja'],
            'Sulawesi Tenggara' => ['Kendari', 'Bau-Bau', 'Konawe', 'Wakatobi'],

            // --- MALUKU & PAPUA ---
            'Maluku' => ['Ambon', 'Tual', 'Maluku Tengah'],
            'Maluku Utara' => ['Ternate', 'Tidore Kepulauan', 'Halmahera Utara'],
            'Papua' => ['Jayapura', 'Keerom', 'Sarmi'],
            'Papua Barat' => ['Manokwari', 'Fakfak', 'Kaimana'],
            'Papua Selatan' => ['Merauke', 'Boven Digoel', 'Mappi', 'Asmat'],
            'Papua Tengah' => ['Nabire', 'Mimika (Timika)', 'Paniai'],
            'Papua Pegunungan' => ['Jayawijaya (Wamena)', 'Lanny Jaya'],
            'Papua Barat Daya' => ['Sorong', 'Raja Ampat'],
        ];

        foreach ($provincesID as $provinceName => $cities) {
            $provinceId = DB::table('master_locations')->insertGetId(['parent_id' => $indonesia, 'name' => $provinceName, 'type' => 'PROVINCE', 'created_at' => now(), 'updated_at' => now()]);
            foreach ($cities as $cityName) {
                DB::table('master_locations')->insert(['parent_id' => $provinceId, 'name' => $cityName, 'type' => 'CITY', 'created_at' => now(), 'updated_at' => now()]);
            }
        }



        // Skills
        $skills = [
            'PHP', 'Laravel', 'JavaScript', 'Vue.js', 'React', 'Node.js',
            'Python', 'Java', 'C++', 'SQL', 'MongoDB', 'PostgreSQL',
            'Docker', 'Kubernetes', 'AWS', 'Azure', 'Git', 'Agile',
            'Project Management', 'Communication', 'Leadership', 'Problem Solving',
            'Hotel Management', 'F&B Service', 'Housekeeping', 'Front Office',
            'Crop Cultivation', 'Soil Management', 'Pest Control', 'Harvesting',
            'Palm Oil Processing', 'Rubber Tapping', 'Estate Management',
        ];

        DB::table('master_skills')->truncate();
        foreach ($skills as $skill) {
            DB::table('master_skills')->insert([
                'name' => $skill,
                'slug' => \Illuminate\Support\Str::slug($skill),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
