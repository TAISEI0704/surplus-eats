<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function showCart($user_id)
    {
        $carts = Cart::where('user_id', $user_id)->get();

        return view('surplus.cart', compact('carts'));
    }

    public function store($post_id, Request $request)
    {
        $quantity = (int) $request->input('cart_quantity');
        $user_id = Auth::user()->id;

        // すでにカートが存在するかチェック
        $cart = Cart::where('user_id', $user_id)
            ->whereHas('products', function ($query) use ($post_id) {
                $query->where('products.id', $post_id);
            })
            ->first();
    
        if ($cart) {
            $cart->products()->updateExistingPivot($post_id, ['quantity' => $quantity]);
        } else {
            $cart = new Cart();
            $cart->user_id = $user_id;
            $cart->save();
    
            $cart->products()->attach($post_id, ['quantity' => $quantity]);
        }
    
        // cart_productsテーブルにデータが格納された後、カートの表示ページにリダイレクトします
        return redirect()->route('cart.show', ['id' => $user_id]);
    }
    
    public function destroy($post_id)
    {
        $user_id = Auth::user()->id;
    
        $product = Product::find($post_id);
        $cart_ids = $product->carts()->pluck('cart_id');
        
        $cart = Cart::where('user_id', $user_id)->whereIn('id', $cart_ids)->first();
    
        if ($cart) {
            $cart->products()->detach($post_id);
            $cart->delete();
        }

    
        return redirect()->route('cart.show', ['id' => $user_id]);
    }
    
}
