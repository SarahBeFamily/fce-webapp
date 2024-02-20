<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Cashier\User;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(User::class);

        // Inizializzo gli ID dei cinema
        view()->share('cinemas', [
            600 => 'Cinema X',
            700 => 'Cinema Y',
            800 => 'Cinema Z',
        ]);

        // Inizializzo la sessione carrello
        session()->put('cart', []);
    }

    public function getIdCinema($idCinema)
    {
        $idCinema = $idCinema ? session()->put('idCinema', $idCinema) : session()->get('idCinema');
        view()->share('idCinema', $idCinema);
        return redirect()->route('/');
    }

    public function getCart()
    {
        $cart = session()->get('cart');
        // return response()->json($cart);
        view()->share('cart', $cart);
    }
}
