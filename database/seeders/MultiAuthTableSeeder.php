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
        // User::create([
        //     'name' => 'User',
        //     'email' => 'user@user.user',
        //     'password' => Hash::make('password'),
        // ]);


        // Seller::create([
        //     'name' => "Taisei's Kitchen",
        //     'email' => 'test@test.test',
        //     'image' => 'imas_0801-3.jpg',
        //     'password' => Hash::make('password'),
        //     'phone' => '080-0123-4567',
        //     'address' => '宮崎県北諸県郡三股町稗田30-8',
        //     'content' => 'レストラン',
        // ]);

        Product::create([
            'name' => 'Fried Chicken',
            'price' => '12',
            'content' => '"Philippines best-tasting crispylicious, juicylicious Chickenjoy that is crispy on the outside, tender and juicy on the inside. 1pc 10 pesos."',
            'quantity' => '10',
            'available_time' => '12:00~17:00 (Afternoon)',
            'seller_id' => '1',
            'image' => 'jollibee_chicken.jpeg',
            'category' => 'Pasta',
        ]);

        Product::create([
            'name' => 'Strawberry Pancake',
            'price' => '320',
            'content' => 'Original pancakes that are "Crispy on the outside and soft on the inside"',
            'quantity' => '10',
            'available_time' => '17:00~21:00 (Evening)',
            'seller_id' => '2',
            'image' => 'storberry_pancake.jpg',
            'category' => 'Dessert',
        ]);

        Product::create([
            'name' => 'Mango',
            'price' => '30',
            'content' => '30 pesos per piece',
            'quantity' => '7',
            'available_time' => '12:00~17:00 (Afternoon)',
            'seller_id' => '3',
            'image' => 'Mango.jpeg',
            'category' => 'Fruit',
        ]);

        Product::create([
            'name' => 'Volcano Cheese Pasta',
            'price' => '400',
            'content' => 'Pasta topped with plenty of cheese. This is a recommended item for cheese lovers.',
            'quantity' => '5',
            'available_time' => '21:00~25:00 (Night)',
            'seller_id' => '2',
            'image' => 'volcano.jpg',
            'category' => 'Pasta',
        ]);

        Product::create([
            'name' => 'Spaghetti',
            'price' => '50',
            'content' => 'Unsold. If you are hungry, please buy it.',
            'quantity' => '6',
            'available_time' => '17:00~21:00 (Evening)',
            'seller_id' => '1',
            'image' => 'jollibee_spaghetti.jpeg',
            'category' => 'Pasta',
        ]);

        // Product::create([
        //     'name' => 'アンティパストミスト-前菜盛り合わせ-',
        //     'price' => '150',
        //     'content' => 'みんなでつまめてお得！オススメの前菜5種盛り合わせ。内容は仕入れにより異なります。',
        //     'quantity' => '6',
        //     'available_time' => '12:00~17:00 (Afternoon)',
        //     'seller_id' => '2',
        //     'image' => 'antipasto.jpg',
        //     'category' => 'Snack',
        // ]);
    }
}
