<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CategorySelect extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $categories;

    public function __construct()
    {
        // カテゴリの選択肢を取得する処理
        $this->categories = [
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
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.category-select');
    }
}
