<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('images')->insert([
            'business_id' => 1,
            'image1' => '/storage/images/chat_img_homepage.jpeg',
            'image2' => '/storage/images/DXCD80Tq93loW5p7v4aSnKzoqJTsdBg2eMwzwuzr.jpg',
            'image3' => '/storage/images/JqfJovNHtmOXQ4Q5wqsEsLuLatmWW0cxDlmMy9qg.jpg'
        ]);
    }
}
