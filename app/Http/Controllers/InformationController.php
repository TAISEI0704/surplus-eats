<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\User;
use App\Models\Information;
use App\Notifications\InformationNotification;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    //

    // public function store(Request $request)
    // {
    //     // お知らせテーブルへ登録
    //     $information = Information::create([
    //         'date' => $request->get('date'),
    //         'title' => $request->get('title'),
    //         'content' => $request->get('content'),
    //     ]);

    //     // お知らせ内容を対象ユーザー宛てに通知登録
    //     $seller = Seller::find($request->get('seller_id'));
    //     $seller->notify(
    //         new InformationNotification($information)
    //     );
    // }
}
