<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\Product;


class PostController extends Controller
{
    //

    public function index()
    {
        $posts=Product::all();
        return view('surplus.timeline', compact('posts'));

    }


    public function detail($id)
    {
        $post = Product::find($id);
        return view('surplus.detail', compact('post'));
    }


    public function sellerIndex()
    {
        $user = Auth::user();
        $seller = Seller::find($user->id);
    
        $posts = Product::where('seller_id', $seller->getId())->get();
    
        return view('seller.surplus.timeline', compact('posts'));

    }

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
        $product->start_time = $request->start_time;
        $product->end_time = $request->end_time;
        $product->seller_id = Auth::id();
        

        $product -> save();

        return redirect()->route('seller.dashboard');
   
}

}
