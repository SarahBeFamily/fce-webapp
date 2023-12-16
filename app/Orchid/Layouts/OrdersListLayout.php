<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Components\Cells\Currency;

class OrdersListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'orders';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('created_at', 'Data')->sort()
                ->usingComponent(DateTimeSplit::class, upperFormat: 'd M Y', lowerFormat: 'H:i', timeZone: 'Europe/Rome'),
            TD::make('order_type', 'Tipologia Ordine'),
            TD::make('order_status', 'Stato')->sort(),
            TD::make('order_amount', 'Totale')
                ->usingComponent(Currency::class, before: '€', decimals: 2, decimal_separator: ',', thousands_separator: '.'),
            TD::make('order_ref_cinema', 'Cinema')->sort(),
        ];
    }
}
