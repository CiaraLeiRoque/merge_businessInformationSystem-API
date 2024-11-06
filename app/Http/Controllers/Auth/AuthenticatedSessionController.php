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
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Check if the user is verified
        if (!$user->hasVerifiedEmail()) {
            Auth::logout(); // Log out the user immediately
            return back()->withErrors([
                'email' => 'Your email address is not verified. Please check your inbox for the verification link.',
            ]);
        }

        // Redirect based on user type
        if ($user->user_type === 'owner') {
            return redirect()->intended(route('home', absolute: false));
        } else if ($user->user_type === 'customer') {
            return redirect()->intended(route('homepage', absolute: false));
        }

        return back()->withErrors('Login failed. Please try again.');
    }


    /**
     * Show the authenticated user.
     */
    public function show(Request $request)
    {
        return Auth::user();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $button = $request->input('button');
        if ($button == 'register') {
            return redirect('/register');
        }

        return redirect('/');
    }
}
