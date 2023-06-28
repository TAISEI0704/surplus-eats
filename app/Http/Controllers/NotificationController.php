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

    return redirect()->route('notification.show');

   }
}
