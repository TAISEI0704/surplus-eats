<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    //
    public function showCart($user_id)
    {
        // var_dump($id);

        // $user = User::all()->first();
        $carts = Cart::where('user_id', $user_id);
        $productIds = $carts->pluck('product_id'); // カートに入った商品のIDを取得
        $posts = Product::whereIn('id', $productIds)->get();

        // var_dump($posts);
        // foreach ($products as $product) {
        //     $product['user_ids'] = json_decode($product->user_ids, true);
        // }

        // return redirect('surplus\cart',compact("products"));
        return view('surplus.cart',compact("posts"));
            
    }

    public function store($post_id)
    {
        $user_id = Auth::user()->id;

    // すでにカートが存在するかチェック
    $existingCart = Cart::where('product_id', $post_id)->where('user_id', $user_id)->first();

    if ($existingCart) {
        // カートが存在する場合は削除
        $existingCart->delete();
    } else {
        // カートが存在しない場合は新規作成
        $cart = new Cart();
        $cart->product_id = $post_id;
        $cart->user_id = $user_id;
        $cart->save();
    }

    return redirect()->back();
    }

    public function destroy($post_id)
    {
        $cart = Cart::where('product_id', $post_id)->where('user_id', Auth::user()->id)->first();
        if ($cart) {
        $cart->delete();
    } else {
        // カートが存在しない場合は新規作成
        $cart = new Cart();
        $cart->product_id = $post_id;
        $cart->user_id = Auth::user()->id;
        $cart->save();
    }
        return redirect()->back();
    }
}
