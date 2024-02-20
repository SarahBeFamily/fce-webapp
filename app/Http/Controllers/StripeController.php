<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function getSession(Request $items) {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $app_url_base = app()->env == 'prod' ? env('APP_URL') : env('APP_DEV');
        $request_to_array = (array) $items->items;
        $items_array_raw = explode(',', $request_to_array[0]);
        $items_array = [];
        $result = [];

        // Accorpo i dati in un array associativo
        foreach ($items_array_raw as $key => $value) {
            $items_array[] = explode(' // ', $value);
        }

        foreach ($items_array as $item) {
            $result[] = [
                'price_data' => [
                    'currency' => $item[0],
                    'unit_amount' => (int) $item[1],
                    'product_data' => [
                        'name' => $item[2],
                    ],
                ],
                'quantity' => (int) $item[3],
            ];
        }

        $checkout = $stripe->checkout->sessions->create([
            'success_url' => 'https://'.$app_url_base.'/checkout/success?film='.$items_array[0][4],
            'line_items' => $result,
            'mode' => 'payment',
        ]);

        return $checkout;
    }
}
