<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;


class PostController extends Controller
{
    //
    public function create()
{
    return view('surplus.create');
}

public function store(Request $request)
{
    
        $request->validate([
            'name' => 'required|string|max:30',
            'price' => 'required|integer',
            'content' => 'required|string|max:255',
            'quantity' => 'required|string',
            'category' => 'required|string|max:30',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            // 'time' => 'required|date',
        ]);

        $file = request()->file('image')->getClientOriginalName();
        request()->file('image')->storeAs('public/images', $file);

        $product = new Product;
        $product-> name = $request -> name;
        $product-> price = $request -> price;
        $product->content = $request -> content;
        $product->quantity  = $request -> quantity;
        $product-> category = $request -> category;
        $product-> image = $request->image;
        $product->image=$file;
        $product -> time = $request -> time;
        

        $product -> save();

        // return redirect()->route('');
   
}

}
