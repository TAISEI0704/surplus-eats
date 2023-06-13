<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function showCart()
    {
        // var_dump($id);
        // $carts = Cart::where('user_id', $id);
        // $productIds = $carts->pluck('product_id'); // カートに入った商品のIDを取得
        // $products = Product::whereIn('id', $productIds);

        // foreach ($products as $product) {
        //     $product['user_ids'] = json_decode($product->user_ids, true);
        // }

        // return redirect('surplus\cart',compact("products"));
        return view('surplus\cart');
    }
}
