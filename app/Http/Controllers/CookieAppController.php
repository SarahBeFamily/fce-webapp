<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CookieAppController extends Controller
{
    public function setCookie($name, $value)
    {
        $minutes = 60;
        $response = response('Cookie settato');
        $response->withCookie($name, $value, $minutes);
        return $response;
        // $idCinema = env('START_CINEMA_ID');
        // return response(view('dashboard'))->withCookie('sessionIdCinema', $idCinema, 60);
    }

    public function updateCookie($name, $value)
    {
        // if (!Cookie::has($name)) {
        //     $value = Cookie::get($name);
        // }
        
        return response($name.' cookie aggiornato')->cookie($name, $value, 30);
    }

    public function getCookie(Request $request, $name)
    {
        return $request->cookie($name);
    }

    public function forgetCookie($name)
    {
        return response($name.' cookie has been removed')->cookie($name, null, -1);
    }
}
