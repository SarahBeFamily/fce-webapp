<?php

namespace App\Models;

use Orchid\Screen\AsSource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{
    use HasFactory, AsSource;

    public function order_user()
    {
        return $this->belongsTo(UserController::class);
    }

    public function getCart()
    {
        $cart = session()->get('cart');
        return response()->json($cart);
    }
}
