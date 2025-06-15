<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        // Generate random math captcha
        $captcha = $this->generateMathCaptcha();
        
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'captcha' => $captcha,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: true));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Generate new captcha for refresh.
     */
    public function refreshCaptcha(): \Illuminate\Http\JsonResponse
    {
        $captcha = $this->generateMathCaptcha();
        
        return response()->json($captcha);
    }

    /**
     * Generate random math captcha.
     */
    private function generateMathCaptcha(): array
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $answer = $num1 + $num2;

        // Store answer in session
        session(['captcha_answer' => $answer]);

        return [
            'question' => "{$num1} + {$num2} = ?",
            'num1' => $num1,
            'num2' => $num2,
            'operation' => '+',
        ];
    }
}   