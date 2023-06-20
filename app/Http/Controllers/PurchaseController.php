<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartProduct;
use Illuminate\Http\Request;
use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    //
    public function storePurchase(Request $request)
    {
        $userId = Auth::user()->id;
        $productIds = $request->input('product_id');
        $quantities = $request->input('quantity');

          // purchasehistories テーブルにデータを保存
          foreach ($productIds as $index => $productId) {
            $quantity = $quantities[$index];
    
            $purchaseHistory = new PurchaseHistory();
            $purchaseHistory->user_id = $userId;
            $purchaseHistory->product_id = $productId;
            $purchaseHistory->purchase_quantity = $quantity;
            $purchaseHistory->save();
        }

        return redirect()->route('purchase.show', ['id' => $userId]);
    }

    // public function showPurchase($userId)
    // {
    //     $carts = Cart::where('user_id', $userId)->get();

    //     return view('surplus.cart', compact('carts'));
    // }
}
