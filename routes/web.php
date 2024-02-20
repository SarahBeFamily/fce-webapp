<?php

use App\Models\Orders;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CookieAppController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/cookie/{name}/{value}', [CookieAppController::class, 'updateCookie']);
Route::get('/getIdCinema', [DashboardController::class, 'getIdCinema'])->name('getIdCinema');


// Route::redirect('/', '/600');
Route::get('/', function () {

    return view('dashboard');
    
    // return view('dashboard', [DashboardController::class, 'idCinema']);
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profilo', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profilo', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profilo', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/profilo', function () {
    return view('profilo');
})->middleware(['auth', 'verified'])->name('profilo');

Route::get('/{idCinema}/film', function ($idCinema) {
    return view('film', ['idCinema' => $idCinema]);
})->name('film');

Route::get('/{idCinema}/food', function ($idCinema) {
    return view('food', ['idCinema' => $idCinema]);
})->name('food');

Route::get('/{idCinema}/film/{id}', function (Orders $orders, $idCinema, $id) {
    return view('partials.single-film',['orders' => $orders, 'idCinema' => $idCinema, 'film' => $id]);
});

// Route::get('/addToCart', [OrdersController::class, 'getCart'])->name('addToCart');
Route::get('addToCart', function () {
    return [
        'cart' => session()->get('cart'),
        'test' => 'test'
    ];
});

Route::get('/{idCinema}/food/{id}', function ($idCinema, $id) {
    return view('partials.single-food',['idCinema' => $idCinema, 'food' => $id]);
});

Route::get('/{idCinema}/biglietti', function ($idCinema) {
    return view('biglietti', ['idCinema' => $idCinema]);
})->name('biglietti');

Route::get('/{idCinema}/checkout', function ($idCinema) {
    if (!Auth::check()) {
        return view('accedi', ['idCinema' => $idCinema]);
    } else {
        return view('checkout', ['idCinema' => $idCinema]);
    }
});

Route::get('/{idCinema}/carrello', function ($idCinema) {
    return view('cart', ['idCinema' => $idCinema]);
})->name('cart');

Route::get('/cart/{cart}/checkout', function (Request $request, Cart $cart) {
    $order = Orders::create([
        'cart_id' => $cart->id,
        'price_ids' => $cart->price_ids,
        'order_data_list' => $cart->cart_items,
        'order_ref_cinema' => $cart->order_ref_cinema,
        'order_status' => 'incomplete',
    ]);

    return $request->user()->checkout($order->price_ids, [
        'success_url' => route('checkout-success').'?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => route('checkout-cancel'),
        'metadata' => ['order_id' => $order->id],
    ]);
})->name('checkout');

Route::post('stripe/webhook', 'StripeWebhookController@handleWebhook')->name('cashier.webhook')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

Route::get('/getSession', [StripeController::class, 'getSession'])->name('getSession');

Route::get('/checkout/success', function (Request $request) {

    $sessionId = $request->get('session_id');
    $orderId = Cashier::stripe()->checkout->sessions->retrieve($sessionId)['metadata']['order_id'] ?? null;

    $order = Orders::findOrFail($orderId);
    // $film = $request->film;
    // $user = Auth::user();
    // $order = new Order();
    // $order->user_id = $user->id;
    // $order->film_id = $film;
    // $order->save();
    $order->update(['order_status' => 'pagato']);

    return view('checkout-success', ['order' => $order]);
});


require __DIR__.'/auth.php';
