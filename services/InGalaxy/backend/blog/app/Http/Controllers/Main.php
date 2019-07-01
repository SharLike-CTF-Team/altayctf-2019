<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Controllers\Controller;

class Main extends Controller
{
    /**
     Главная страница
     */
    public function indexPage()
    {
        if (!Auth::check())
        {
            return view('index');
        }
        return redirect('/news');
    }
}