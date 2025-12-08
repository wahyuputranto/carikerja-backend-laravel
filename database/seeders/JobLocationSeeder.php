<?php

namespace Database\Seeders;

use App\Models\JobLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            // TURKEY
            [
                'country' => 'Turkey',
                'locations' => [
                    // Istanbul Province
                    [
                        'province' => 'Istanbul',
                        'cities' => ['Istanbul', 'Beyoğlu', 'Kadıköy', 'Üsküdar', 'Beşiktaş', 'Şişli', 'Bakırköy', 'Maltepe', 'Pendik', 'Kartal']
                    ],
                    // Ankara Province
                    [
                        'province' => 'Ankara',
                        'cities' => ['Ankara', 'Çankaya', 'Keçiören', 'Yenimahalle', 'Mamak', 'Sincan', 'Etimesgut', 'Gölbaşı', 'Pursaklar', 'Altındağ']
                    ],
                    // Izmir Province
                    [
                        'province' => 'Izmir',
                        'cities' => ['Izmir', 'Konak', 'Karşıyaka', 'Bornova', 'Buca', 'Çiğli', 'Gaziemir', 'Balçova', 'Bayraklı', 'Narlıdere']
                    ],
                    // Antalya Province
                    [
                        'province' => 'Antalya',
                        'cities' => ['Antalya', 'Alanya', 'Manavgat', 'Kepez', 'Muratpaşa', 'Konyaaltı', 'Serik', 'Aksu', 'Döşemealtı', 'Kemer']
                    ],
                    // Bursa Province
                    [
                        'province' => 'Bursa',
                        'cities' => ['Bursa', 'Osmangazi', 'Nilüfer', 'Yıldırım', 'Gemlik', 'İnegöl', 'Mudanya', 'Mustafakemalpaşa', 'Karacabey', 'Orhangazi']
                    ],
                ]
            ],

            // TAIWAN
            [
                'country' => 'Taiwan',
                'locations' => [
                    // Taipei City
                    [
                        'province' => 'Taipei City',
                        'cities' => ['Taipei', 'Zhongzheng', 'Datong', 'Zhongshan', 'Songshan', 'Da\'an', 'Wanhua', 'Xinyi', 'Shilin', 'Beitou', 'Neihu', 'Nangang', 'Wenshan']
                    ],
                    // New Taipei City
                    [
                        'province' => 'New Taipei City',
                        'cities' => ['Banqiao', 'Xizhi', 'Yonghe', 'Zhonghe', 'Tucheng', 'Sanchong', 'Xinzhuang', 'Xindian', 'Luzhou', 'Tamsui', 'Shulin', 'Taishan']
                    ],
                    // Taichung City
                    [
                        'province' => 'Taichung City',
                        'cities' => ['Taichung', 'Central', 'East', 'West', 'South', 'North', 'Beitun', 'Xitun', 'Nantun', 'Taiping', 'Dali', 'Wufeng', 'Wuri', 'Fengyuan', 'Houli', 'Shalu', 'Qingshui', 'Dadu', 'Longjing', 'Shalu']
                    ],
                    // Tainan City
                    [
                        'province' => 'Tainan City',
                        'cities' => ['Tainan', 'Central West', 'East', 'South', 'North', 'Anping', 'Annan', 'Yongkang', 'Guiren', 'Xinhua', 'Zuozhen', 'Nanxi', 'Madou', 'Jiali']
                    ],
                    // Kaohsiung City
                    [
                        'province' => 'Kaohsiung City',
                        'cities' => ['Kaohsiung', 'Lingya', 'Fengshan', 'Zuoying', 'Sanmin', 'Qianzhen', 'Qijin', 'Qieding', 'Xiaogang', 'Yancheng', 'Gushan', 'Nanzih', 'Renwu', 'Dashe', 'Gangshan', 'Linyuan']
                    ],
                    // Taoyuan City
                    [
                        'province' => 'Taoyuan City',
                        'cities' => ['Taoyuan', 'Zhongli', 'Pingzhen', 'Bade', 'Yangmei', 'Luzhu', 'Dayuan', 'Guishan', 'Longtan', 'Xinwu', 'Guanyin', 'Daxi', 'Fuxing']
                    ],
                ]
            ],

            // BULGARIA
            [
                'country' => 'Bulgaria',
                'locations' => [
                    // Sofia City Province
                    [
                        'province' => 'Sofia City',
                        'cities' => ['Sofia', 'Bankya', 'Vitosha', 'Vrazdebna', 'Lozenets', 'Lyulin', 'Mladost', 'Nadezhda', 'Ovcha Kupel', 'Poduyane', 'Serdika', 'Sredets', 'Triaditsa', 'Ilinden', 'Krasno Selo', 'Krasna Polyana', 'Novi Iskar', 'Pancharevo', 'Studentski', 'Vazrazhdane', 'Vrabnitsa', 'Izgrev', 'Oborishte']
                    ],
                    // Plovdiv Province
                    [
                        'province' => 'Plovdiv',
                        'cities' => ['Plovdiv', 'Asenovgrad', 'Karlovo', 'Parvomay', 'Rakovski', 'Stamboliyski', 'Hisarya', 'Krichim', 'Saedinenie', 'Sopot']
                    ],
                    // Varna Province
                    [
                        'province' => 'Varna',
                        'cities' => ['Varna', 'Aksakovo', 'Beloslav', 'Byala', 'Devnya', 'Provadiya', 'Suvorovo', 'Valchi Dol', 'Vetrino', 'Dolni Chiflik']
                    ],
                    // Burgas Province
                    [
                        'province' => 'Burgas',
                        'cities' => ['Burgas', 'Aytos', 'Karnobat', 'Nesebar', 'Pomorie', 'Sozopol', 'Sredets', 'Tsarevo', 'Sungurlare', 'Ruen', 'Primorsko', 'Malko Tarnovo']
                    ],
                    // Ruse Province
                    [
                        'province' => 'Ruse',
                        'cities' => ['Ruse', 'Byala', 'Vetovo', 'Dve Mogili', 'Ivanovo', 'Slivo Pole', 'Tsenovo', 'Borovo', 'Marten', 'Senovo']
                    ],
                    // Stara Zagora Province
                    [
                        'province' => 'Stara Zagora',
                        'cities' => ['Stara Zagora', 'Kazanlak', 'Radnevo', 'Chirpan', 'Galabovo', 'Maglizh', 'Pavel Banya', 'Opan', 'Gurkovo', 'Bratya Daskalovi']
                    ],
                    // Pleven Province
                    [
                        'province' => 'Pleven',
                        'cities' => ['Pleven', 'Cherven Bryag', 'Knezha', 'Levski', 'Nikopol', 'Belene', 'Gulyantsi', 'Dolni Dabnik', 'Dolna Mitropolia', 'Koynare']
                    ],
                ]
            ],
        ];

        foreach ($locations as $countryData) {
            $country = $countryData['country'];
            
            foreach ($countryData['locations'] as $provinceData) {
                $province = $provinceData['province'];
                
                foreach ($provinceData['cities'] as $city) {
                    JobLocation::create([
                        'country' => $country,
                        'province' => $province,
                        'city' => $city,
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('Job locations seeded successfully!');
        $this->command->info('Total locations created: ' . JobLocation::count());
    }
}
