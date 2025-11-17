<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Show register form (blade: pages.auth.register)
     */
    public function showRegister()
    {
        return view('pages.auth.register');
    }

    /**
     * Handle register post
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'otp_type' => 'nullable|in:Y,N',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $otpType = $request->input('otp_type', 'Y');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => ($otpType === 'Y') ? 'inactive' : 'active',
            'otp_type' => $otpType,
        ]);

        if ($otpType === 'Y') {
            $this->generateAndSendOtp($user);
            session(['otp_user_id' => $user->id, 'otp_user_email' => $user->email]);
            session(['url.intended' => url('/home')]);
            return redirect()->route('auth.otp.form')
                ->with('status', 'Pendaftaran berhasil. Kode OTP verifikasi telah dikirim ke email Anda.');
        }

        Auth::login($user);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'redirect' => route('home')]);
        }

        return redirect()->intended('/home')->with('status', 'Pendaftaran berhasil. Selamat datang!');
    }
    /**
     * Show login form (blade: auth.login)
     */
    public function showLogin()
    {
        return view('pages.auth.login');
    }

    /**
     * Handle login post
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah'])
                ->withInput();
        }

        if ($user->status === 'inactive') {
            return back()
                ->withErrors(['email' => 'Akun Anda dalam status tidak aktif.'])
                ->withInput();
        }

        if ($user->status === 'blocked') {
            return back()
                ->withErrors(['email' => 'Akun Anda dalam status diblokir.'])
                ->withInput();
        }

        if ($user->otp_type === 'Y') {
            $this->generateAndSendOtp($user);

            session(['otp_user_id' => $user->id, 'otp_user_email' => $user->email]);

            session(['url.intended' => url()->previous()]);

            return redirect()->route('auth.otp.form')->with('status', 'OTP telah dikirim ke email Anda');
        }

        Auth::login($user, $request->filled('remember'));

        return redirect()->intended('/home');
    }

    /**
     * Generate OTP, save to user and send email
     */
    protected function generateAndSendOtp(User $user)
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = Carbon::now()->addMinutes(10);

        $user->otp = $otp;
        $user->otp_expired_at = $expiry;
        $user->save();

        // Send email (Mailable below)
        Mail::to($user->email)->send(new SendOtpMail($user, $otp));
    }

    /**
     * Show OTP verification form (blade: auth.otp-verify)
     */
    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Sesi OTP tidak ditemukan. Silakan login ulang.']);
        }

        return view('pages.auth.verif');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $userId = session('otp_user_id');
        if (!$userId) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi OTP tidak ditemukan. Silakan login ulang.'], 401);
            }
            return redirect()
                ->route('auth.login')
                ->withErrors(['email' => 'Sesi OTP tidak ditemukan. Silakan login ulang.']);
        }

        $user = User::find($userId);
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Pengguna tidak ditemukan.'], 401);
            }
            return redirect()
                ->route('auth.login')
                ->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }

        // Check OTP and expiry
        if (!$user->otp || $user->otp !== $request->otp) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['otp' => ['Kode OTP tidak sesuai']]], 422);
            }
            return back()
                ->withErrors(['otp' => 'Kode OTP tidak sesuai'])
                ->withInput();
        }

        if (!$user->otp_expired_at || \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($user->otp_expired_at))) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['otp' => ['Kode OTP sudah kadaluarsa. Silakan minta kode baru.']]], 422);
            }
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.']);
        }

        // OTP valid -> clear OTP fields and login
        $user->otp = null;
        $user->otp_expired_at = null;
        $user->save();

        Auth::login($user);

        // Clean session
        session()->forget('otp_user_id');
        session()->forget('url.intended');

        // If AJAX, respond JSON with redirect url
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'redirect' => route('home')]);
        }

        // fallback non-AJAX
        return redirect()->route('home');
    }

    /**
     * Resend OTP (if user is in session)
     */
    public function resendOtp(Request $request)
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi OTP tidak ditemukan. Silakan login ulang.'], 401);
            }
            return redirect()
                ->route('auth.login')
                ->withErrors(['email' => 'Sesi OTP tidak ditemukan. Silakan login ulang.']);
        }

        $user = User::find($userId);
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Pengguna tidak ditemukan.'], 401);
            }
            return redirect()
                ->route('auth.login')
                ->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }

        // generate and send OTP (gunakan method yang sudah ada)
        $this->generateAndSendOtp($user);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'OTP baru telah dikirim ke email Anda.']);
        }

        return back()->with('status', 'OTP baru telah dikirim ke email Anda');
    }

    public function showForgotForm()
    {
        return view('pages.auth.reset');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        switch ($status) {
            case Password::RESET_LINK_SENT:
                return back()->with('status', 'Link reset password telah dikirim ke email Anda.');

            case Password::INVALID_USER:
                return back()->withErrors([
                    'email' => 'Email tidak terdaftar.',
                ]);

            case Password::THROTTLED:
                return back()->withErrors([
                    'email' => 'Terlalu banyak percobaan reset. Silakan coba lagi beberapa menit lagi.',
                ]);

            default:
                return back()->withErrors([
                    'email' => 'Gagal mengirim email reset password. Silakan coba beberapa saat lagi.',
                ]);
        }
    }

    public function showResetForm($token)
    {
        return view('pages.auth.reset-form', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        switch ($status) {
            case Password::PASSWORD_RESET:
                return redirect()->route('login')->with('status', 'Password Anda berhasil direset. Silakan login dengan password baru.');

            case Password::INVALID_USER:
                return back()->withErrors([
                    'email' => 'Email tidak ditemukan dalam sistem.',
                ]);

            case Password::INVALID_TOKEN:
                return back()->withErrors([
                    'email' => 'Token reset tidak valid atau sudah kadaluarsa. Silakan ulangi permintaan reset password.',
                ]);

            case Password::THROTTLED:
                return back()->withErrors([
                    'email' => 'Terlalu banyak percobaan reset. Coba beberapa menit lagi.',
                ]);

            default:
                return back()->withErrors([
                    'email' => 'Gagal mereset password. Silakan coba beberapa saat lagi.',
                ]);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
