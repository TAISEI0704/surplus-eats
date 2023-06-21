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
        $purchaseHistoryIds = [];

          // purchasehistories テーブルにデータを保存
          foreach ($productIds as $index => $productId) {
            $quantity = $quantities[$index];
    
            $purchaseHistory = new PurchaseHistory();
            $purchaseHistory->user_id = $userId;
            $purchaseHistory->product_id = $productId;
            $purchaseHistory->purchase_quantity = $quantity;
            $purchaseHistory->save();

            $purchaseHistoryIds[] = $purchaseHistory->id;

            $product = Product::find($productId);
            $cart_ids = $product->carts()->pluck('cart_id');
            
            $cart = Cart::where('user_id', $userId)->whereIn('id', $cart_ids)->first();
        
            if ($cart) {
                $cart->products()->detach($productId);
                $cart->delete();
            }

            // productsテーブルの数量を減らす
            $product->quantity -= $quantity;
            // $product->updated_at = now();
            $product->save();

        }

        session(['purchaseHistoryIds' => $purchaseHistoryIds]);

        return redirect()->route('purchase.show', ['id' => $userId]);
    }

    public function showPurchase($userId)
    {
        $purchaseHistoryIds = session('purchaseHistoryIds');
    // ユーザーの購入履歴を取得
        $purchaseHistories = PurchaseHistory::where('user_id', $userId)
            ->whereIn('id', $purchaseHistoryIds)
            ->with('product', 'product.seller')
            ->latest('created_at')
            ->get();
            
        // // ユーザーの最新の購入履歴を取得
        // $latestPurchaseHistory = PurchaseHistory::where('user_id', $userId)
        // ->with('product', 'product.seller')
        // ->latest('created_at')
        // ->first();

        // 購入した商品と関連するデータをビューに渡す
        return view('surplus.complete', compact('purchaseHistories'));
    }

    // public function showPurchaseHistory()
    // {
    //     $userId = Auth::user()->id;

    //     $latestPurchaseHistories = PurchaseHistory::where('user_id', $userId)
    //     ->with('product', 'product.seller')
    //     ->latest('created_at')
    //     ->get();

    //     return
    // }

}
