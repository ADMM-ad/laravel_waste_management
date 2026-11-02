<?php

namespace Database\Seeders;

use App\Models\WasteCategory;
use Illuminate\Database\Seeder;

class WasteCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Organic Waste',
                'description' => 'Sampah organik hayati seperti sisa makanan, sayuran, buah-buahan, dedaunan, dan ranting pohon.',
            ],
            [
                'name' => 'Plastic Waste',
                'description' => 'Sampah berbahan plastik seperti botol PET, kantong kresek, kemasan sachet, sedotan, dan gelas plastik.',
            ],
            [
                'name' => 'Paper Waste',
                'description' => 'Sampah kertas seperti karton, kardus pengemasan, kertas koran, majalah, dan kertas HVS kantor.',
            ],
        ];

        foreach ($categories as $cat) {
            WasteCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
