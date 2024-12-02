<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sizes = [
            ['name' => 'Extra Small', 'abbreviation' => 'XS'],
            ['name' => 'Small', 'abbreviation' => 'S'],
            ['name' => 'Medium', 'abbreviation' => 'M'],
            ['name' => 'Large', 'abbreviation' => 'L'],
            ['name' => 'Extra Large', 'abbreviation' => 'XL'],
            ['name' => 'Double Extra Large', 'abbreviation' => 'XXL'],
        ];

        foreach ($sizes as $size) {
            DB::table('sizes')->insert([
                'name' => $size['name'],
                'abbreviation' => $size['abbreviation'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
