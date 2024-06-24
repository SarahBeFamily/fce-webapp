<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class StripeController extends Controller
{
    // public function getSession(Request $items, $success_url) {
    //     $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    //     $app_url_base = app()->env == 'prod' ? env('APP_URL') : env('APP_DEV');
    //     $request_to_array = (array) $items->items;
    //     $items_array_raw = explode(',', $request_to_array[0]);
    //     $items_array = [];
    //     $result = [];

    //     // Accorpo i dati in un array associativo
    //     foreach ($items_array_raw as $key => $value) {
    //         $items_array[] = explode(' // ', $value);
    //     }

    //     foreach ($items_array as $item) {
    //         $result[] = [
    //             'price_data' => [
    //                 'currency' => $item[0],
    //                 'unit_amount' => (int) $item[1],
    //                 'product_data' => [
    //                     'name' => $item[2],
    //                 ],
    //             ],
    //             'quantity' => (int) $item[3],
    //         ];
    //     }

    //     $checkout = $stripe->checkout->sessions->create([
    //         'success_url' => $success_url,
    //         'line_items' => $result,
    //         'mode' => 'payment',
    //     ]);

    //     return $checkout;
    // }

    /**
     * Get Stripe Customer from Stripe
     */
    public function getStripeCustomer(Request $request) 
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $stripe_customer = $request->user()->createOrGetStripeCustomer();
        $payment_methods = $stripe->paymentMethods->all([
            'customer' => $stripe_customer->id,
            'type' => 'card',
        ]);
        $data = [
            'customer' => $stripe_customer,
            'payment_methods' => $payment_methods,
        ];

        return response()->json($data, 200);
    }

    /**
     * Pay with Stripe PaymentIntent
     */
    // public function handlePayment(Request $request)
    // {
    //     $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    //     $stripe_customer = $request->user()->createOrGetStripeCustomer();
    //     $payment_method = $request->payment_method;

    //     try {
    //         $payment_intent = $stripe->paymentIntents->create([
    //             'amount' => $request->amount,
    //             'currency' => 'eur',
    //             'customer' => $stripe_customer->id,
    //             'payment_method' => $payment_method,
    //             'off_session' => true,
    //             'confirm' => true,
    //         ]);

    //     } catch (\Exception $e) {
    //         return back()->with('error', $e->getMessage());
    //     }

    //     return back()->with('success', 'Pagamento effettuato con successo!');
    // }

    /**
     * Creo l'ordine provvisorio
     */
    public function createOrder(Request $request)
    {
        $cartArr = (array) $request->cart;
        $app_url_base = app()->env == 'prod' ? env('APP_URL') : env('APP_DEV');
        $message = '';

        // Controllo se esiste già un ordine con lo stesso carrello
        $order = Orders::where('cart_id', $request->sessionID)->first();

        // Se l'ordine esiste già lo aggiorno
        if ($order) {
            $order->update([
                'order_data_list' => count($cartArr) > 0 ? json_encode($cartArr) : '',
                'order_amount' => floatval(number_format($request->total, 2, ',', '.')),
            ]);

            $message = 'Ordine aggiornato con successo!';

        } else {
            $order = Orders::create([
                'user_id' => $request->user()->id,
                'cart_id' => $request->sessionID,
                'order_type' => $request->orderType, // 'ticket' o 'food'
                'order_data_list' => count($cartArr) > 0 ? json_encode($cartArr) : '',
                'order_ref_cinema' => $request->idCinema,
                'performance_id' => $request->performance,
                'order_amount' => floatval(number_format($request->total, 2, ',', '.')),
                'order_status' => 'pending',
                'order_transaction' => '',
                'order_notes' => ''
            ]);
    
            $message = 'Ordine creato con successo!';
        }

        // preparo i dati da inviare a Stripe
        $data = [
            'request' => [
                'return_url' => $app_url_base.'/checkout/success?order='.$order->id.'&film='.$request->film.'&performance='.$request->performance.'&sessionId='.$request->sessionID,
                'metadata' => ['order_id' => $order->id],
            ],
            'message' => $message
        ];

        return response()->json($data);
    }

    /**
     * Update payment intent
     */
    public function updatePaymentIntent(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $payment_intent = $stripe->paymentIntents->update(
            $request->payment_intent,
            [
                'metadata' => [
                    'order_id' => $request->order_id,
                    'items' => $request->items,
                    'cinema' => $request->idCinema,
                    'show' => $request->show,
                ],
                'amount' => $request->amount,
                'currency' => 'eur',
                'customer' => $request->customer,
            ]
        );

        return $payment_intent;
    }

    /**
     * Checkout success page
     */
    public function checkoutSuccess(Request $request) 
    {
        $success = $request->redirect_status == 'succeeded' ? true : false;

        if (!$success) {
            return back()->with('error', 'Errore: Pagamento non riuscito! Si prega di riprovare.');
        } else {
            $order = Orders::find($request->order);
            $order->update([
                'order_status' => 'pagato',
                'order_transaction' => $request->payment_intent,
            ]);

            $data = [
                'order' => $request->order,
                'film' => $request->film,
                'transaction' => $request->payment_intent,
                'sessionId' => $request->sessionID,
                'performance' => $request->performance,
                'items' => $request->cart,
            ];

            return view('checkout-success')->with($data);
        }
    }
}
