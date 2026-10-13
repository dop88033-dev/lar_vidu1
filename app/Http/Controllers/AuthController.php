<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

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

    /**
     * Hiển thị giao diện yêu cầu quên mật khẩu
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi link đặt lại mật khẩu về email người dùng
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email của bạn.',
            'email.email'    => 'Địa chỉ email không đúng định dạng.',
        ]);

        // Kiểm tra xem email có tồn tại không
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withInput($request->only('email'))
                ->with('error', 'Không tìm thấy tài khoản nào gắn với địa chỉ email này.');
        }

        // Tạo và gửi link reset mật khẩu
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Đã gửi liên kết đặt lại mật khẩu vào hòm thư ' . $request->email . '. Vui lòng kiểm tra hộp thư đến (và mục Spam nếu có).');
        }

        return back()->withInput($request->only('email'))
            ->with('error', 'Có lỗi xảy ra khi gửi email: ' . __($status));
    }

    /**
     * Hiển thị form nhập mật khẩu mới từ link email
     */
    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', old('email'))
        ]);
    }

    /**
     * Xử lý lưu mật khẩu mới
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:6',
        ], [
            'token.required'     => 'Mã token xác thực không hợp lệ hoặc đã thiếu.',
            'email.required'     => 'Vui lòng nhập địa chỉ email.',
            'email.email'        => 'Email không đúng định dạng.',
            'password.required'  => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'password.min'       => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', '🎉 Đặt lại mật khẩu thành công! Bạn có thể đăng nhập bằng mật khẩu mới ngay.');
        }

        $errorMsg = match ($status) {
            Password::INVALID_TOKEN => 'Liên kết đặt lại mật khẩu đã hết hạn hoặc không hợp lệ. Vui lòng gửi lại yêu cầu mới.',
            Password::INVALID_USER  => 'Không tìm thấy tài khoản tương ứng với email này.',
            default                 => 'Không thể đặt lại mật khẩu. Vui lòng thử lại sau.'
        };

        return back()->withInput($request->only('email'))
            ->with('error', $errorMsg);
    }
}