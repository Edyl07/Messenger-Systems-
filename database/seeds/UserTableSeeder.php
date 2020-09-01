<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 10; $i++){
            \Illuminate\Support\Facades\DB::table('users')->insert(
              [
                  'name' => "Demba$i",
                  'email' => "demba$i@maarsa.com",
                  'password' => bcrypt('000000')
              ]
            );
        }
    }
}
