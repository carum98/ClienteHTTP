<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Return welcome Page
     * @return \Illuminate\Http\Response
     */
    public function showWelcomePage()
    {
        return view('welcome');
    }
}
