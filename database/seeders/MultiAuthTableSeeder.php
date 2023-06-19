<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MultiAuthTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name' => '一般ユーザー',
            'email' => 'user@user.user',
            'password' => Hash::make('password'),
        ]);


        Seller::create([
            'name' => '管理者',
            'email' => 'admin@admin.admin',
            'password' => Hash::make('password'),
            'phone' => '080-0123-4567',
            'adress' => '福岡県',
        ]);

        Product::create([
            'name' => 'チキン南蛮',
            'price' => '500',
            'content' => 'おぐらのチキン南蛮',
            'quantity' => '5',
            'start_time' => '12:00',
            'end_time' => '14:00',
            'seller_id' => '1',
            'image' => 'food_chikinnanban_ogura.jpeg',
            'category' => 'カテゴリ1',
        ]);
    }
}
