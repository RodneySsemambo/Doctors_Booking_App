<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('LOGIN ATTEMPT', [
            'email' => $request->email,
        ]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            Log::warning('LOGIN FAILED', [
                'email' => $request->email,
            ]);

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        Log::info('LOGIN SUCCESS', [
            'id' => $user->id,
            'email' => $user->email,
            'user_type' => $user->user_type,
        ]);

        if ($user->user_type === 'patient') {
            Log::info('REDIRECT → PATIENT DASHBOARD');
            return redirect()->route('patient.dashboard');
        }

        if ($user->user_type === 'doctor') {
            Log::info('REDIRECT → DOCTOR DASHBOARD');
            return redirect()->route('doctor.dashboard');
        }

        if ($this->canAccessAdminPanel($user)) {
            Log::info('REDIRECT → ADMIN');
            return redirect()->intended('/admin');
        }

        Log::error('NO MATCHING ROLE — REDIRECTING TO LOGIN', [
            'user_type' => $user->user_type,
        ]);

        return redirect('/login');
    }


    protected function canAccessAdminPanel($user)
    {
        if ($user->user_type === 'admin' || $user->user_type === 'super_admin' || $user->user_type === 'user') {
            return true;
        }

        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole([
                'admin',
                'super_admin',
                'manager'

            ]);
        }
        return false;
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
