<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SitePasswordController extends Controller
{
    public function showForm()
    {
        if (empty(config('app.password'))) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required']);

        if ($request->input('password') === config('app.password')) {
            $request->session()->put('site_authenticated', true);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['password' => 'Incorrect password.']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('site_authenticated');
        return redirect('/');
    }
}
