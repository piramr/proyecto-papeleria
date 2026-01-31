<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Notifications\TwoFactorCode as TwoFactorNotification;

class TwoFactorController extends Controller
{
    public function index() 
    {
        if (session('two_factor_verified')) {
            return redirect()->route('dashboard');
        }

        // Send code if not sent recently (optional optimization)
        $this->sendCodeIfNeeded();

        return view('auth.two-factor-email');
    }

    public function store(Request $request) 
    {
        $request->validate([
            'code' => 'required|numeric'
        ]);

        $user = Auth::user();
        $cachedCode = Cache::get('2fa_code_' . $user->id);

        if ($request->code == $cachedCode) {
            Session::put('two_factor_verified', true);
            Cache::forget('2fa_code_' . $user->id);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['code' => 'El código de verificación es incorrecto o ha expirado.']);
    }

    public function resend() 
    {
        $this->sendCode(true); // Force send
        return back()->with('status', 'El código de verificación ha sido re-enviado.');
    }

    protected function sendCodeIfNeeded()
    {
        $user = Auth::user();
        if (!Cache::has('2fa_code_' . $user->id)) {
            $this->sendCode();
        }
    }

    protected function sendCode($force = false) 
    {
        $user = Auth::user();
        $code = rand(100000, 999999);
        
        Cache::put('2fa_code_' . $user->id, $code, now()->addMinutes(10));

        try {
            $user->notify(new TwoFactorNotification($code));
        } catch (\Exception $e) {
            // Log error or handle failure
        }
    }
}
