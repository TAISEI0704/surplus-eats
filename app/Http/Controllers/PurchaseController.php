<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Seller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartProduct;
use Illuminate\Http\Request;
use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Information;
use App\Notifications\InformationNotification;

class PurchaseController extends Controller
{
    //
    public function storePurchase(Request $request)
    {
        $userId = Auth::user()->id;
        $productIds = $request->input('product_id');
        $quantities = $request->input('quantity');
        $purchaseHistoryIds = [];
        // お知らせ内容を格納する配列
        $notifications = [];


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

            $sellerId = $product->seller_id;
            
            // セラーごとのお知らせ内容を組み立てる
            $user = User::find($userId);
            $productName = $product->name;
            $notificationContent = "{$user->name}が {$productName} を {$quantity} 個購入しました。";

            // セラーごとにお知らせを追加する
            if (!isset($notifications[$sellerId])) {
                  $notifications[$sellerId] = [];
            }
            $notifications[$sellerId][] = $notificationContent;

       }

       // セラーごとにお知らせを送信
            foreach ($notifications as $sellerId => $notificationContents) {
            $seller = Seller::find($sellerId);

        // お知らせテーブルへ登録
            $information = Information::create([
             'date' => date('Y-m-d H:i'),
             'content' => implode(PHP_EOL, $notificationContents),
            ]);

        // セラーにお知らせを送信
        $seller->notify(new InformationNotification($information));

            

        //      // お知らせテーブルへ登録
        //     $information = Information::create([
        //         'date' => $request->get('date'),
        //         'content' => $request->get('content'),
    
        //     ]);

        //     // セラーIDを取得する
        //     $sellerIds[] = $product->seller_id;

        
        //    // お知らせ内容を対象ユーザー宛てに通知登録
        //    $sellers = Seller::whereIn('id', $sellerIds)->get();
        //    foreach ($sellers as $seller) {
        //        $seller->notify(
        //                        new InformationNotification($information)
        //                       );
        //              }
        

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
