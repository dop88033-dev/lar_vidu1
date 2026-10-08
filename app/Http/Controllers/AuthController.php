<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký người dùng 
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ], [
            'name.required'     => 'Vui lòng nhập họ tên.',
            'email.required'    => 'Vui lòng nhập địa chỉ email.',
            'email.unique'      => 'Email này đã được đăng ký trước đó.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed'=> 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'role'     => 'customer', // hoặc 'user' tùy theo cơ sở dữ liệu của bạn
        ]);

        // Gửi email xác thực tài khoản qua event Registered
        try {
            event(new \Illuminate\Auth\Events\Registered($user));
        } catch (\Throwable $e) {
            Log::error('Lỗi khi gửi mail xác thực đăng ký: ' . $e->getMessage());
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $ex) {
                Log::error('Direct email verification send failed: ' . $ex->getMessage());
            }
        }

        // Đăng nhập tự động để phiên xác thực hoạt động
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Đăng ký thành công! Hệ thống đã gửi email xác thực đến địa chỉ ' . $user->email . '. Vui lòng kiểm tra hòm thư.');
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập người dùng
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->has('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Đăng nhập Admin thành công!');
            }

            return redirect()->intended(route('welcome'))->with('success', 'Đăng nhập thành công!');
        }

        return redirect()->back()->withInput($request->only('email'))->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    // Xử lý đăng xuất người dùng
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đã đăng xuất thành công.');
    }
}