<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;

class ReviewController extends Controller
{
    public function create()
    {
        return view('surplus.review');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'content' => 'required|string|max:255',
        ]);

        $review = new Review;
        $review-> name = $request -> name;
        $review-> content = $request -> content;
        $review->seller_id = Auth::id();

        $review -> save();

        return redirect()->route('profile.show');
    }
}

