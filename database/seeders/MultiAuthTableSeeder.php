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
            'image' => 'imas_0801-3.jpg',
            'password' => Hash::make('password'),
            'phone' => '080-0123-4567',
            'address' => '宮崎県北諸県郡三股町稗田30-8',
            'content' => 'レストランだよ～',
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
            'category' => 'category1',
        ]);

        Product::create([
            'name' => 'ぎょうざ',
            'price' => '600',
            'content' => '丸岡のぎょうざ',
            'quantity' => '6',
            'start_time' => '10:30',
            'end_time' => '16:00',
            'seller_id' => '1',
            'image' => 'food_gyoza_maruoka.jpeg',
            'category' => 'category3',
        ]);
    }
}
