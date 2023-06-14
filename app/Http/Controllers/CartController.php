<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function showCart(Request $request)
    {
        // var_dump($id);

        $user = User::all()->first();
        // $carts = Cart::where('user_id', $id);
        // $productIds = $carts->pluck('product_id'); // カートに入った商品のIDを取得
        // $products = Product::whereIn('id', $productIds);

        // foreach ($products as $product) {
        //     $product['user_ids'] = json_decode($product->user_ids, true);
        // }

        // return redirect('surplus\cart',compact("products"));
        if ($request->is_seller == $user->is_seller){
            return view('\surplus\cart',['id' => $request->id,]);
        }
        else{
            return ;
        }
            
    }
}
