<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SellerAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->get('is_seller')) {
            return redirect()->route('seller.login');
        }

        return $next($request);
    }
}
