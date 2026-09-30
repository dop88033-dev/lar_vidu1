<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để truy cập.');
        }

        return redirect()->route('welcome')->with('error', 'Bạn không có quyền truy cập trang quản trị Admin.');
    }
}