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
            '' => __('Select Category'),
            'all' => 'All',
            'category1' => 'Category 1',
            'category2' => 'Category 2',
            'category3' => 'Category 3',
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
