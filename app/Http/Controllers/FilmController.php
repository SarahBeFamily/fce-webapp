<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FilmController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->input('search');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://swapi.dev/api/films/');
        $films = json_decode($response->getBody()->getContents());
        return view('films', ['films' => $films->results]);
    }

}

