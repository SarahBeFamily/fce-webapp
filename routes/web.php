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
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AuthenticatedSessionController;

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
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profilo', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profilo', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profilo', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profilo', [ProfileController::class, 'addPaymentMethod'])->name('profile.payment-methods');
    Route::get('/profilo/ordini', function(Orders $orders) {
        return view('profile.partials.orders', ['orders' => auth()->user()->orders]);
    })->name('profile.orders');
    Route::get('/profilo/ordini/{ordine}', function(Orders $ordine) {
        return view('profile.partials.order-details', ['order' => $ordine]);
    })->name('profile.orders.show');
});

Route::get('/profilo', function () {
    return view('profilo', ['intent' => auth()->user()->createSetupIntent()]);
})->middleware(['auth', 'verified'])->name('profilo');

Route::controller(GoogleController::class)->group(function(){
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');

    // Auth with facebook
    Route::get('login/facebook', 'redirectToFacebookProvider')->name('login.facebook');
    Route::get('login/facebook/callback', 'handleFacebookProviderCallback');
});

Route::get('/cerca', function () {
    return view('cerca');
})->name('cerca');

Route::get('/{idCinema}/film', function ($idCinema) {
    return view('film', ['idCinema' => $idCinema]);
})->name('film');

Route::get('/{idCinema}/food', function ($idCinema) {
    return view('food', ['idCinema' => $idCinema]);
})->name('food');

Route::get('/{idCinema}/film/{id}', function ($idCinema, $id) {
    
    $intent = '';
    $intent_id = '';
    if (Auth::check()) {
        $payment = auth()->user()->pay(100);
        $intent = $payment->client_secret;
        $intent_id = $payment->id;
    }
   
    return view('partials.single-film',['idCinema' => $idCinema, 'film' => $id, 'intent' => $intent, 'intent_id' => $intent_id]);
})->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

// Route::get('/addToCart', [OrdersController::class, 'getCart'])->name('addToCart');

Route::get('/{idCinema}/food/{id}', function ($idCinema, $id) {
    return view('partials.single-food',['idCinema' => $idCinema, 'food' => $id]);
});

Route::get('/biglietti', function () {
    return view('biglietti');
})->name('biglietti');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/termini-condizioni', function () {
    return view('termini-condizioni');
})->name('termini-condizioni');

Route::get('/checkout', function () {
    if (!Auth::check()) {
        return view('accedi');
    } else {
        return view('checkout');
    }
});

Route::get('/carrello', function () {
    return view('cart');
})->name('cart');


// Collegameno Stripe Webhook
Route::post('stripe/webhook', 'StripeWebhookController@handleWebhook')->name('cashier.webhook')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
Route::get('/getSession', [StripeController::class, 'getSession'])->name('getSession');
Route::get('/getStripeCustomer', [StripeController::class, 'getStripeCustomer'])->name('getStripeCustomer');
Route::get('/updatePaymentIntent', [StripeController::class, 'updatePaymentIntent'])->name('updatePaymentIntent');
Route::get('/createOrder', [StripeController::class, 'createOrder'])->name('createOrder');

Route::get('/checkout/success', ['middleware' => 'cors', StripeController::class, 'checkoutSuccess'])->name('checkout-success');


require __DIR__.'/auth.php';
