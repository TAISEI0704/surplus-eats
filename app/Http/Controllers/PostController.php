<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Review;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    //

    public function index()
    {
        // $posts=Product::all()->latest('created_at')->get();
        $posts = Product::latest('created_at')->get();
        $category = null;
        return view('surplus.timeline', compact('posts','category'));

    }


    public function detail($id)
    {
        $post = Product::find($id);

        $reviews = Review::where('product_id',$id)->get();
        
        return view('surplus.detail', compact('post','reviews'));
    }


    public function sellerIndex()
    {
        $user = Auth::user();
        $seller = Seller::find($user->id);
    
        $posts = Product::where('seller_id', $seller->getId())->latest('created_at')->get();
    
        return view('seller.surplus.timeline', compact('posts'));

    }

    public function create()
    {
        return view('surplus.create');
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $file = $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('public/images', $file);

        $product = new Product();
        $product->fill([
            'name' => $data['name'],
            'price' => $data['price'],
            'content' => $data['content'],
            'quantity' => $data['quantity'],
            'category' => $data['category'],
            'image' => $file,
            'available_time' => $request->input('available_time'),
            'seller_id' => Auth::id(),
        ]);

        $product->save();

        return redirect()->route('seller.dashboard');
    }

    // public function feedback()
    // {
    //     return view('surplus.feedback');
    // }

    public function edit($id)
    {
        $post = Product::find($id);

        return view('seller.surplus.edit',compact('post'));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $post = Product::findOrFail($id);

        $data = $request->validated();

        $post->fill([
            'name' => $data['name'],
            'price' => $data['price'],
            'content' => $data['content'],
            'quantity' => $data['quantity'],
        ]);

        if ($request->has('category')) {
            $post->category = $request->input('category');
        }

        if ($request->has('available_time')) {
            $post->available_time = $request->input('available_time');
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/images', $file);
            $post->image = $file;
        }

        $post->save();

        return redirect()->route('seller.dashboard');
    }

    public function destroy($product_id)
    {
        $post = Product::find($product_id);
        $post->delete();

        return redirect()->route('seller.dashboard');
    }

    public function filterByCategory(Request $request)
    {
        $categories = $request->input('category');

        if (empty($categories)) {
            return redirect()->route('dashboard');
        } elseif (in_array('all', $categories)) {
            $posts = Product::latest('created_at')->get();
        } else {
            $posts = Product::whereIn('category', $categories)->latest('created_at')->get();
        }
    
        return view('surplus.timeline', compact('posts', 'categories'));
    }
}
