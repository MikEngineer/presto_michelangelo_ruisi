<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public $categories = [
        'elettronica',
        'abbigliamento',
        'saluteBenessere',
        'casaGiardino',
        'giocattoli',
        'sport',
        'animaliDomestici',
        'libriRiviste',
        'accessori',
        'motori'
        // 'electronics',
        // 'clothing',
        // 'healthBeauty',
        // 'homeGarden',
        // 'toys',
        // 'sport',
        // 'pets',
        // 'books',
        // 'accessories',
        // 'engines'
    ];

    public function run(): void
    {
        // User::factory(10)->create();

        foreach ($this->categories as $category) {
            Category::create([
                'name' => $category
            ]);
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
