<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Orders extends Model
{
    use HasFactory, AsSource;

    public function order_user()
    {
        return $this->belongsTo(UserController::class);
    }
}
