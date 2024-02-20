<?php

use Illuminate\Http\Request;
 
Route::post('/checkout', function (Request $request) {
    $stripeCharge = $request->user()->charge(
        100, $request->paymentMethodId
    );

 
    Route::post('/pay', function (Request $request) {
        $payment = $request->user()->pay(
            $request->get('amount')
        );
    
        return $payment->client_secret;
    });
    
});