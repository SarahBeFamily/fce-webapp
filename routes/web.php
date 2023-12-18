<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    // if (!Auth::check()) {
    //     return view('login');
    // } else {
    //     return redirect('/dashboard');
    // }
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

// Route::get('/home', function () {
//     return view('home');
// });

Route::middleware('auth')->group(function () {
    Route::get('/profilo', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profilo', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profilo', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/profilo', function () {
    return view('profilo');
})->middleware(['auth', 'verified'])->name('profilo');

Route::get('/film', function () {
    return view('film');
});

Route::get('/food', function () {
    return view('food');
});

Route::get('/film/{id}', function ($id) {
    return view('partials.single-film',['film' => $id]);
});

Route::get('/food/{id}', function ($id) {
    return view('partials.single-food',['food' => $id]);
});

Route::get('/checkout', function () {
    if (!Auth::check()) {
        return view('accedi');
    } else {
        return view('checkout');
    }
});

// Route::get('/checkout', function () {
//     return view('chekout');
// })->middleware(['auth', 'verified'])->name('checkout');


require __DIR__.'/auth.php';
