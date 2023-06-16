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
        $carts = Cart::where('user_id', $user_id)->whereIn('product_id', $productIds)->get();
        $quantities = $carts->pluck('quantity');

        // return redirect('surplus\cart',compact("products"));
        return view('surplus.cart',compact("posts","quantities"));
            
    }

    public function store($post_id, Request $request)
    {
        $quantity = (int) $request->input('quantity');
        $user_id = Auth::user()->id;
        // $post_id = Product::id();
    // すでにカートが存在するかチェック
    $existingCart = Cart::where('product_id', $post_id)->where('user_id', $user_id)->first();

    if ($existingCart) {
        //カートが存在する場合,数量を更新
        $existingCart->quantity += $quantity;
        $existingCart->save();
    } else {
        // カートが存在しない場合は新規作成
        $cart = new Cart();
        $cart->product_id = $post_id;
        $cart->user_id = $user_id;
        $cart->quantity = $quantity;
        $cart->save();
    }

    // return redirect()->back();
    return $this->showCart($user_id);
    }

    public function destroy($post_id)
    {
        $cart = Cart::where('product_id', $post_id)->where('user_id', Auth::user()->id)->first();
        if ($cart) {
            if ($cart->quantity > 1) {
                // カートの数量が1より大きい場合は、数量を1減らす
                $cart->decrement('quantity');
            } else {
                // カートの数量が1の場合は、削除する
                $cart->delete();
            }
    } else {
        // カートが存在しない場合は新規作成
        $cart = new Cart();
        $cart->product_id = $post_id;
        $cart->user_id = Auth::user()->id;
        $cart->save();
    }
        // return redirect()->back();
        return $this->showCart(Auth::user()->id);
    }
}
