<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Validator;
use App\Models\Seller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// sellerユーザー用の認証処理
class SellerAuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('seller\register');
    }

    public function register(Request $request)
    {
        // ユーザーの登録処理を実装する
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $seller = new Seller([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        $seller->save();
    
        // ユーザーの登録成功時の処理
        Auth::login($seller);

        return redirect('/seller-dashboard');
    }

    public function showLoginForm()
    {
        return view('seller\login');
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

        return view('seller\show', [
            'request' => $request,
            'user' => $user,
        ]);
    }
}