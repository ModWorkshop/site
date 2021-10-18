<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ViewErrorBag;

/**
 * @group Authentication
 */
class LoginController extends Controller
{
    /**
     * Login
     * 
     * Attempts to login a user with the provided username and password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'], //blalba@email.com
            'password' => ['required'],
            'remember' => ['boolean']
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $credentials['remember'])) {
            $request->session()->regenerate();
            return response('');
        } else {
            return response('Email or password are incorrect', Response::HTTP_UNAUTHORIZED);
        }

        return response('Something went wrong :((', Response::HTTP_BAD_REQUEST);
    }

    /**
     * Logout
     * 
     * Logs out the currently authenticated user.
     *
     * @authenticated
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Response::HTTP_OK;
    }

    /**
     * Register
     * 
     * Attempts to register users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);
            
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], true)) {
            $request->session()->regenerate();
            return Response::HTTP_OK;
        }

        return back()->withErrors([
            'email' => 'Something went wrong',
        ]);
    }
}
