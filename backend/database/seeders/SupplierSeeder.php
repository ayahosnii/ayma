<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = [
            // Laptop Companies
            [
                'name' => 'Dell Inc.',
                'contact_person' => 'John Smith',
                'contact_info' => '123-456-7890',
                'phone' => '123-456-7890',
                'email' => 'contact@dell.com',
                'address' => '1 Dell Way, Round Rock, TX 78682',
                'country' => 'USA',
                'city' => 'Round Rock',
                'postal_code' => '78682',
                'website' => 'https://www.dell.com',
                'tax_id' => 'TX123456789',
                'status' => 'active',
                'notes' => 'Top supplier for business laptops.',
                'supplier_type' => 'international',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HP Inc.',
                'contact_person' => 'Jane Doe',
                'contact_info' => '987-654-3210',
                'phone' => '987-654-3210',
                'email' => 'contact@hp.com',
                'address' => '1501 Page Mill Road, Palo Alto, CA 94304',
                'country' => 'USA',
                'city' => 'Palo Alto',
                'postal_code' => '94304',
                'website' => 'https://www.hp.com',
                'tax_id' => 'CA123456789',
                'status' => 'active',
                'notes' => 'Supplier for consumer and business laptops.',
                'supplier_type' => 'wholesale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Phone Companies
            [
                'name' => 'Samsung Electronics',
                'contact_person' => 'Kim Lee',
                'contact_info' => '+82-2-2053-3000',
                'phone' => '+82-2-2053-3000',
                'email' => 'contact@samsung.com',
                'address' => '129 Samsung-ro, Yeongtong-gu, Suwon-si, Gyeonggi-do',
                'country' => 'South Korea',
                'city' => 'Suwon',
                'postal_code' => '16677',
                'website' => 'https://www.samsung.com',
                'tax_id' => 'KR123456789',
                'status' => 'active',
                'notes' => 'Leading supplier of smartphones and components.',
                'supplier_type' => 'factory',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Apple Inc.',
                'contact_person' => 'Tim Cook',
                'contact_info' => '1-800-676-2775',
                'phone' => '1-800-676-2775',
                'email' => 'contact@apple.com',
                'address' => 'One Apple Park Way, Cupertino, CA 95014',
                'country' => 'USA',
                'city' => 'Cupertino',
                'postal_code' => '95014',
                'website' => 'https://www.apple.com',
                'tax_id' => 'CA987654321',
                'status' => 'active',
                'notes' => 'Premium smartphone supplier.',
                'supplier_type' => 'retail',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('suppliers')->insert($suppliers);
    }
}
