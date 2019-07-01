<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'login' => 'admin',
            'password' => bcrypt('admin'),
            'name' => 'Obi-Wan',
            'surname' => 'Kenobi',
            'race'=>'human',
            'gender'=>'man',
            'birthday'=>date('Y-m-d'),
            'homeplace'=>'Coruscant',
            'avatar'=>'img/obi.jpg',
            'selfdescription'=>'Hello there! I am not admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
