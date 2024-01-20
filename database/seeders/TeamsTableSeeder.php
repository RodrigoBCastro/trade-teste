<?php

namespace Database\Seeders;

use App\Models\Time;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Time::create(['nome' => 'Athletico Paranaense']);
        Time::create(['nome' => 'Atlético Goianiense']);
        Time::create(['nome' => 'Atlético Mineiro']);
        Time::create(['nome' => 'Bahia']);
        Time::create(['nome' => 'Ceará']);
        Time::create(['nome' => 'Chapecoense']);
        Time::create(['nome' => 'Corinthians']);
        Time::create(['nome' => 'Flamengo']);
        Time::create(['nome' => 'Fluminense']);
        Time::create(['nome' => 'Fortaleza']);
        Time::create(['nome' => 'Grêmio']);
        Time::create(['nome' => 'Internacional']);
        Time::create(['nome' => 'Palmeiras']);
        Time::create(['nome' => 'Red Bull Bragantino']);
        Time::create(['nome' => 'Santos']);
        Time::create(['nome' => 'São Paulo']);
        Time::create(['nome' => 'Sport']);
        Time::create(['nome' => 'Vasco da Gama']);
    }
}
