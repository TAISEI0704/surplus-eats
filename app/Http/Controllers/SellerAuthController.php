<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Validator;
use App\Models\Seller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerAuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth\seller-register');
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
    
        $user = Seller::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        // ユーザーの登録成功時の処理
    
        return redirect('/dashboard');
    }

    public function showLoginForm()
    {
        return view('auth\seller-login');
    }

    public function login(Request $request)
    {
        // カスタムログインのロジックを実装する
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            // ログイン成功時の処理
            return redirect()->intended('/dashboard');
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
}