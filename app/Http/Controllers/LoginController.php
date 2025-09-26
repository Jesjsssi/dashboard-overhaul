<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role == 'superadmin') {
                return redirect()->route('superadmin.dashboard')->with('success', 'Login berhasil!');
            }
        }
        return redirect()->route('formlogin')->with('error', 'Username atau password salah');
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            return redirect()->route('formlogin');
        }
        return redirect()->route('formlogin');  
        
    }   

}