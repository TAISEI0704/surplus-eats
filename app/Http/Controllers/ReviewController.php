<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Product;
use App\Models\Seller;

class ReviewController extends Controller
{
    public function create($seller_id)
    {
        $seller = Seller::find($seller_id);
        return view('surplus.review', compact('seller'));
    }

    public function store(Request $request)
    {
        // 入力データのバリデーション
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:30',
            'comment' => 'required|string|max:255',
        ]);

        // レビューモデルの作成と保存
        $product = Product::find($request->seller_id);
        $review = new Review;
        $review -> name = $request -> name;
        $review -> content = $request -> comment;
        $review -> user_id = Auth::id();
        $review -> seller_id = $request -> seller_id;
        $review->save();
        // dd($review);

        // 保存後の処理などを追加する場合はここに記述する

        // return redirect()->back()->with('success', 'レビューが投稿されました。');
        return redirect()->route('profile.show')->with('success', 'The review has been posted');
    }

  

    // public function show($product_id)
    // {
    //     $product = Product::find($product_id);
    //     $reviews = Review::where('product_id', $product_id)->get();
    
    //     return view('surplus.detail', compact('product', 'reviews'));
    // }


    public function show($product_id)
    {
        $reviews = Review::where('product_id', $product_id)->get();
    
        return view('surplus.detail', compact('reviews'));
    }



}

