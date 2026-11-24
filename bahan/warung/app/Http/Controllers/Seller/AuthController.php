<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->get('is_seller')) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $real = (string) config('shop.password');

        if ($real === '') {
            return back()->withErrors(['password' => 'SELLER_PASSWORD belum diisi di file .env.']);
        }

        if (! hash_equals($real, (string) $request->password)) {
            return back()->withErrors(['password' => 'Password salah.']);
        }

        $request->session()->regenerate();
        $request->session()->put('is_seller', true);

        return redirect()->route('seller.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_seller');
        $request->session()->regenerate();

        return redirect()->route('shop');
    }
}
