<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $colors = [
            ['name' => 'Red', 'hex_code' => '#FF0000'],
            ['name' => 'Green', 'hex_code' => '#00FF00'],
            ['name' => 'Blue', 'hex_code' => '#0000FF'],
            ['name' => 'Yellow', 'hex_code' => '#FFFF00'],
            ['name' => 'Black', 'hex_code' => '#000000'],
            ['name' => 'White', 'hex_code' => '#FFFFFF'],
            ['name' => 'Orange', 'hex_code' => '#FFA500'],
            ['name' => 'Purple', 'hex_code' => '#800080'],
            ['name' => 'Pink', 'hex_code' => '#FFC0CB'],
            ['name' => 'Gray', 'hex_code' => '#808080'],
        ];

        // Insert the color data into the colors table
        DB::table('colors')->insert($colors);
    }
}
