<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __contruct()
    {
        $this->middleware('auth');
    }

    public function getSetupIntent()
    {
        return auth()->user()->createSetupIntent();
    }

    public function updateDefaultPaymentMethod(Request $request)
    {
        $user = auth()->user();
        $methods = [];

        foreach ($user->paymentMethods() as $method) {
            $methods[] = $method->asStripePaymentMethod();
        }
    }
}
