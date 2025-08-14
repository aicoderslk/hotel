<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::insert([
            ['name'=>'Deluxe King','type'=>'Deluxe','capacity'=>2,'price_cents'=>18000,'description'=>'28m², city view','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Superior Twin','type'=>'Superior','capacity'=>2,'price_cents'=>14000,'description'=>'24m², courtyard','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Family Suite','type'=>'Suite','capacity'=>4,'price_cents'=>30000,'description'=>'2 rooms, 40m²','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Standard Single','type'=>'Standard','capacity'=>1,'price_cents'=>9000,'description'=>'18m², compact','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
