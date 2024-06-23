<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DummyUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userData = [
            [
                'userId'=> '2015354054',
                'password' => Hash::make('123'),
                'role' => 'admin'

            ],
            [
                'userId'=> '2015354056',
                'password' => Hash::make('123'),
                'role' => 'mahasiswa'

            ],
            [
                'userId'=> '2015354002',
                'password' => Hash::make('123'),
                'role' => 'mahasiswa'

            ],
            [
                'userId'=> '2015354003',
                'password' => Hash::make('123'),
                'role' => 'mahasiswa'

            ],
            [
                'userId'=> '2015354004',
                'password' => Hash::make('123'),
                'role' => 'mahasiswa'

            ],
            [
                'userId'=> '2015354005',
                'password' => Hash::make('123'),
                'role' => 'mahasiswa'

            ],
            [
                'userId'=> '1111',
                'password' => Hash::make('123'),
                'role' => 'co_admin'

            ],
        ];

        foreach($userData as $key => $val){
            User::create($val);
        }
    }
}
