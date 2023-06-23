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
            'name' => "Taisei's Kitchen",
            'email' => 'test@test.test',
            'image' => 'imas_0801-3.jpg',
            'password' => Hash::make('password'),
            'phone' => '080-0123-4567',
            'address' => '宮崎県北諸県郡三股町稗田30-8',
            'content' => 'レストラン',
        ]);

        Product::create([
            'name' => 'ストロベリーパンケーキ',
            'price' => '450',
            'content' => '『外はカリッと、中はふんわりっ』なジラフオリジナルパンケーキ。',
            'quantity' => '10',
            'start_time' => '14:00',
            'end_time' => '21:00',
            'seller_id' => '1',
            'image' => 'storberry_pancake.jpg',
            'category' => 'Dessert',
        ]);

        Product::create([
            'name' => '焦がしチーズの窯焼きボルケーノ',
            'price' => '730',
            'content' => 'Giraffe Monochromeの鉄板メニュー。チーズ好きにオススメの一品です。',
            'quantity' => '5',
            'start_time' => '19:30',
            'end_time' => '22:00',
            'seller_id' => '1',
            'image' => 'volcano.jpg',
            'category' => 'Pasta',
        ]);

        Product::create([
            'name' => 'アンティパストミスト-前菜盛り合わせ-',
            'price' => '400',
            'content' => 'みんなでつまめてお得！オススメの前菜5種盛り合わせ。内容は仕入れにより異なります。',
            'quantity' => '6',
            'start_time' => '15:30',
            'end_time' => '19:00',
            'seller_id' => '1',
            'image' => 'antipasto.jpg',
            'category' => 'Snack',
        ]);
    }
}
