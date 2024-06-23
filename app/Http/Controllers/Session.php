<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class Session extends Controller
{
    function index(){
        return view('login');
    }
    function login(Request $request){
        $request->validate([
            'userId' => 'required',
            'password' => 'required'
        ],[
            'userId.required' => 'NIM wajib diisi',
            'password.required' => 'password wajib diisi',
        ]);

        $infologin = [
            'userId' => $request->userId,
            'password' => $request->password,
        ];

        if(Auth::attempt($infologin)){
            
            if (Auth::user()->role == 'admin') {
                return redirect('/admin');
            } elseif (Auth::user()->role == 'mahasiswa') {   
                return redirect('/home');
            }
            elseif (Auth::user()->role == 'co_admin') {   
                return redirect('/co_admin');
            }
        }else{
            return redirect('/')->withErrors('nim atau password salah')->withInput();
        
        }
    }
    function logout(){
        Auth::logout();
        session()->flush();
        return redirect('');
    }
}
