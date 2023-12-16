<?php

namespace App\Orchid\Screens\Orders;

use Orchid\Screen\Screen;
use App\Models\Orders;
use App\Models\User;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Components\Cells\Currency;
use App\View\Components\UserOrder;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;

class OrdersScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'order'  => Orders::find(1),
            'orders' => Orders::paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Ordini';
    }

    /**
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Mostra la lista di tutti gli ordini.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('orders', [
                TD::make('user_id', 'Utente')
                    ->component(UserOrder::class),
                TD::make('created_at', 'Data')->sort()
                    ->usingComponent(DateTimeSplit::class, upperFormat: 'd M Y', lowerFormat: 'H:i', timeZone: 'Europe/Rome'),
                TD::make('order_type', 'Tipologia Ordine'),
                TD::make('order_status', 'Stato')->sort(),
                TD::make('order_amount', 'Totale')
                    ->usingComponent(Currency::class, before: '€', decimals: 2, decimal_separator: ',', thousands_separator: '.'),
                TD::make('order_ref_cinema', 'Cinema')->sort(),
                TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Orders $order) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Dettagli'))
                            ->route('platform.orders.ordine', $order->id)
                            ->icon('bs.eye'),

                    ])),
            ]),
        ];
    }
}
