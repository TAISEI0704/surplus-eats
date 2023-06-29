<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as BaseNotification;

use App\Models\Seller;
use App\Models\User;
use App\Models\Information;
use App\Models\Notification;
use App\Notifications\InformationNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    //

   public function read($notification_id){
   
    $notification = Notification::find($notification_id);

    if ($notification) {
        $notification->read_at=Carbon::now();
        $notification->save();
    }


    $data = json_decode($notification->data, true);

    // user_id を取り出して変数に代入
    $userId = $data['user_id'];
    $user = User::find($userId);
    $sellerId=Auth::user()->id;
    $seller=Seller::find($sellerId);
    $content=$data['content'];
    $notificationString="{$seller->name} has confirmed ' $content ' ";

     // お知らせテーブルへ登録
     $information = Information::create([
        'date' => date('Y-m-d H:i'),
        'content' => $notificationString,
        'seller_id'=>$sellerId,
       ]);

   // セラーにお知らせを送信
   $user->notify(new InformationNotification($information));


    return redirect()->route('notification.show');

   }

   public function notificationShow(){
    // ユーザー
    $user =  Auth::user();

    // 通知を取得
    $notifications = $user->notifications;

    return view('surplus.notifications', compact('notifications'));
}

public function onlyRead($notification_id){
   
    $notification = Notification::find($notification_id);

    if ($notification) {
        $notification->read_at=Carbon::now();
        $notification->save();
    }

}

}
