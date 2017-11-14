<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class TrainingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0;$i<21;$i++){
            DB::table('trainings')->insert([
                'title' => str_random(10),
                'description' => str_random(20),
                'city_id' =>rand(1,10),
                'trainer_id'=>rand(1,10)
            ]);
        }
    }
}
