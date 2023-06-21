<?php

namespace App\Http\Controllers;

use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Http\Controllers\Livewire\UserProfileController as JetstreamUserProfileController;
use Illuminate\Http\Request;

class UserProfileController extends JetstreamUserProfileController
{
    public function show(Request $request)
    {
        $userId = Auth::user()->id;

        $purchaseHistories = PurchaseHistory::where('user_id', $userId)
            ->with('product', 'product.seller')
            ->latest('created_at')
            ->get();

        return view('profile.show', compact('purchaseHistories'));
    }
}
