<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function idCinema(Request $request, $idCinema = 600) {
        return $idCinema;
    }
}
