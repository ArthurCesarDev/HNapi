<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

use App\Models\User;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        DB::table('units')->insert([
            'name' => 'LOJA TATUAPÉ',
            'id_owner' => '1'
        ]);
        DB::table('units')->insert([
            'name' => 'VILA MADALENA TATUAPÉ',
            'id_owner' => '1'
        ]);
        DB::table('units')->insert([
            'name' => 'PARAÍSO',
            'id_owner' => '0'
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Academia',
            'cover' => 'gym.jpg',
            'days' => '1,2,3,4,5',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Piscina',
            'cover' => 'pool.jpg',
            'days' => '1,3,5',
            'start_time' => '05:00:00',
            'end_time' => '19:00:00',
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Churrasqueira',
            'cover' => 'barbecue.jpg',
            'days' => '1,2,3,4,5,6,7',
            'start_time' => '09:00:00',
            'end_time' => '22:00:00',
        ]);
        DB::table('walls')->insert([
        
            'title' => 'Perda Muito alta',
            'body' => 'Quebra está muito alta devido a rebaixa alta demando de locação!',
            'datecreat' => '2022-02-21 20:09:00',
        ]);

        DB::table('walls')->insert([
        
            'title' => 'Acredite Sempre',
            'body' => 'Deus tem o melhor para cada alcasião!',
            'datecreat' => '2022-02-21 20:10:00',
        ]);
       
        
        

        
    }
}
