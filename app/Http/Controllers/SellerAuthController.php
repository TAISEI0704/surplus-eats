<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Validator;
use App\Models\Seller;

use App\Models\Notifications;

use App\Models\Review;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// sellerユーザー用の認証処理
class SellerAuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('seller.register');
    }

    public function register(Request $request)
    {
        // ユーザーの登録処理を実装する
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'content'=> 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('public/images', $file);
    
    
        $seller = new Seller([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $file,
            'content'=>$request->content,
            'password' => Hash::make($request->password),
        ]);
        
        $seller->save();
    
        // ユーザーの登録成功時の処理
        Auth::login($seller);

        return redirect('/seller-dashboard');
    }

    public function showLoginForm()
    {
        return view('seller.login');
    }

    public function login(Request $request)
    {
        // var_dump($request);
        // カスタムログインのロジックを実装する
        $credentials = $request->only('email', 'password');
        $guard = 'sellers';
    
        if (Auth::guard($guard)->attempt($credentials)) {
            // ログイン成功時の処理
            return redirect()->intended('/seller-dashboard');
        } else {
            // ログイン失敗時の処理
            return redirect()->back()->withErrors([
                'email' => 'ログイン情報が正しくありません。',
            ]);
        }
    }

    public function logout(Request $request)
    {
        // カスタムログアウトのロジックを実装する
        Auth::logout();

        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }

    // sellerユーザー用プロフィールページに遷移
    public function showProfile(Request $request)
    {
        $user = Auth::guard('sellers')->user();

        $reviews = Review::where('seller_id',$user->id)->get();

        return view('seller.show', [
            'request' => $request,
            'user' => $user,
            'reviews' => $reviews,
        ]);
    }

    public function updateProfile(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'phone' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'content'=> 'required|string',
    ]);

    $seller = Seller::find($id);

    if ($request->hasFile('image')) { //画像がアップロードありの処理
        $file = $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('public/images', $file);
        // $post->title = $request->title;
        $seller->name = $request->name;
        $seller->email = $request->email;
        $seller->phone = $request->phone;
        $seller->address = $request->address;
        $seller->content = $request->content;
        $seller->image = $file;

        $seller->save();
    }else{ //画像のアップロードなしの処理
        // $post->title = $request->title;
        $seller->name = $request->name;
        $seller->email = $request->email;
        $seller->phone = $request->phone;
        $seller->address = $request->address;
        $seller->content = $request->content;
       

        $seller->save();
    }
        
    return redirect()->route('seller.dashboard');
}

//     public function notificationShow(){
//     //   // ユーザー
//     //   $seller = Seller::find($seller_id);

//     //   //  全通知を取得
//     //   $notifications=$seller->notifications;
//       return view('seller.notifications');

// } 


        public function notificationShow(){
            // ユーザー
            $seller =  Auth::user();
        
            // 通知を取得
            $notifications = $seller->notifications;
        
            return view('seller.notifications', compact('notifications'));
        }


}

