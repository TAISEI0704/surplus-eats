<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */

     public $categories_1;

     public $categories_2;

     public function __construct()
     {
         // カテゴリの選択肢を取得する処理
         $this->categories_1 = [
             // '' => __('Select Category'),
             // 'all' => 'All',
             'Chicken' => 'Chicken',
             'Noodles' => 'Noodles',
             'Pizza' => 'Pizza',
             'Burgers' => 'Burgers',
             'Donut' => 'Donut',
             'Fries' => 'Fries',
             'Salad' => 'Salad',
             'Sisig' => 'Sisig',
             'Ramen' => 'Ramen',
             'Milk Tea' => 'Milk Tea',
             'Cakes' => 'Cakes',
             'Coffee' => 'Coffee',
             'Takoyaki' => 'Takoyaki',
             'Ice Cream' => 'Ice Cream',
             'Filipino' => 'Filipino',
             'Fast Food' => 'Fast Food',
             'Pasta' => 'Pasta',
             'Pork' => 'Pork',
             'Beef' => 'Beef',
             'Snack' => 'Snack',
             'Chinese' => 'Chinese',
             'Japanese' => 'Japanese',
             'BBQ' => 'BBQ',
             'Seafood' => 'Seafood',
             'Sushi' => 'Sushi',
             'Soup' => 'Soup',
             'Steak' => 'Steak',
             'French' => 'French',
             'Beer' => 'Beer',
             'Drink' => 'Drink',
             'Dessert' => 'Dessert',
             // 追加のカテゴリオプション
         ];

         $this->categories_2 = [
            // '' => __('Select Category'),
            // 'all' => 'All',
            'Meat' => 'Meat',
            'Seafood' => 'Seafood',
            'Egg' => 'Egg',
            'Milk' => 'Milk',
            'Born' => 'Born',
            'Animal Oil' => 'Animal Oil',
            'Cereal' => 'Cereal',
            'Vegetable' => 'Vegetable',
            'Fruit' => 'Fruit',
            'Herb' => 'Herb',
            'Vegetable Oil' => 'Vegetable Oil',
            'Sea Weed' => 'Sea Weed',
            'Spice' => 'Spice',
            'Condiment' => 'Condiment',
            // 追加のカテゴリオプション
        ];
     }

    public function render()
    {
        return view('components.modal');
    }
}