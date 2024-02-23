<?php

namespace App\Models;

use Orchid\Screen\AsSource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'user_id',
        'order_type',
        'cart_id',
        'order_status',
        'order_data_list',
        'order_amount',
        'order_transaction',
        'order_ref_cinema',
        'order_notes'
    ];

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
