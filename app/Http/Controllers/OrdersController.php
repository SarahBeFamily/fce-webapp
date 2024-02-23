<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function getCart()
    {
        $cart = session()->get('sessionCart');
        return response()->json($cart);
    }
    
    public function addToCart(Request $id, $items)
    {
        $cart = session('sessionCart');
        $items = json_decode($items);
        $newCart = [];

        // var_dump()

        foreach ($items as $item) {
            $item_id = $item->id;
            $newCart[$id][$item_id] = [
                'price_data' => [
                    'currency' => $item->currency,
                    'unit_amount' => str_replace(',', '',$item->prezzo),
                    'product_data' => [
                        'name' => $item->prodotto,
                    ],
                ],
                'quantity' => $item->qty,
                'id' => $item->id,
                'type' => $item->type,
                'show' => $item->show, 
                'date' => $item->date, 
                'cinema' => $item->cinema,
                'hall' => $item->hall
            ];
        }

        if (isset($cart[$id])) {
            session('cart', $newCart);
        } else {
            session('cart', []);
            session('cart', [$newCart]);
        }

        return json($newCart);
    }
    
    public function updateCart(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Book added to cart.');
        }
    }
  
    public function deleteProduct(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Book successfully deleted.');
        }
    }
}
