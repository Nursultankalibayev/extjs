<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class Training_dateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0;$i<21;$i++){
            DB::table('training_date')->insert([
                'started_date_time' => date("Y-m-d H:i:s",mt_rand(1262055681,1262055681)),
                'ended_date_time' => date("Y-m-d H:i:s",mt_rand(1262055681,1262055681)),
                'training_id' =>rand(1,21),
            ]);
        }
    }
}
