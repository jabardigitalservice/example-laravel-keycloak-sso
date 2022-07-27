<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // dummy values
        $niks = [
            '123456789000',
            '123456789010',
            '123456789011',
            '123456789012',
        ];

        foreach ($niks as $nik) {
            User::factory()->create(compact('nik'));
        }
    }
}
