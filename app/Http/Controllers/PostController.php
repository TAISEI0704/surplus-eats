<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Review;

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

public function store(Request $request)
{
    
        $request->validate([
            'name' => 'required|string|max:30',
            'price' => 'required|integer',
            'content' => 'required|string|max:3000',
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
        $product->available_time = $request->available_time;
        $product->seller_id = Auth::id();
        

        $product -> save();

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

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'price' => 'required|integer',
            'content' => 'required|string|max:255',
            'quantity' => 'required|string',
            // 'category' => 'required|string|max:30',
            // 'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $post = Product::find($id);

        if ($request->hasFile('image')) { //画像がアップロードありの処理
            $file = $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/images', $file);
            // $post->title = $request->title;
            $post->name = $request->name;
            $post->price = $request->price;
            $post->content = $request->content;
            $post->quantity = $request->quantity;
            $post->category = $request->category;
            $post->available_time = $request->available_time;
            $post->image = $file;

            $post->save();
        }else{ //画像のアップロードなしの処理
            // $post->title = $request->title;
            $post->name = $request->name;
            $post->price = $request->price;
            $post->content = $request->content;
            $post->quantity = $request->quantity;
            $post->category = $request->category;
            $post->available_time = $request->available_time;
        
            $post->save();
        }
            
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
        $categories = $request->input('category', []);

        // カテゴリーが選択されていない場合、または'all'が含まれている場合は全商品を表示
        if (empty($categories) || in_array('all', $categories)) {
            $posts = Product::latest('created_at')->get();
            $category = null;
        } else {
            // 複数カテゴリーで検索
            $posts = Product::whereIn('category', $categories)->latest('created_at')->get();
            $category = implode(', ', $categories);
        }
    
        return view('surplus.timeline', compact('posts', 'category'));
    }
}
