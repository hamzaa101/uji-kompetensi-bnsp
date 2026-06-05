<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request, AuditLogService $audit)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials['is_active'] = true;

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $audit->record('login', Auth::user(), 'User berhasil login.');

        return redirect()->intended('/dashboard');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request, AuditLogService $audit)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => $data['password'],
            'role' => 'pasien',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $audit->record('register', $user, 'Pasien mendaftar akun.');

        return redirect('/catalog');
    }

    public function logout(Request $request, AuditLogService $audit)
    {
        if ($request->user()) {
            $audit->record('logout', $request->user(), 'User logout.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function dashboard(Request $request)
    {
        return match ($request->user()->role) {
            'admin' => redirect('/admin/dashboard'),
            'apoteker' => redirect('/apoteker/dashboard'),
            'kasir' => redirect('/kasir/dashboard'),
            default => redirect('/catalog'),
        };
    }
}
