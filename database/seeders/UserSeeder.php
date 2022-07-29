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
            '123456789000' => null,
            '123456789010' => 'member',
            '123456789011' => 'admin',
            '123456789012' => 'superadmin',
        ];

        foreach ($niks as $nik => $role) {
            User::factory()->create(compact('nik', 'role'));
        }
    }
}
