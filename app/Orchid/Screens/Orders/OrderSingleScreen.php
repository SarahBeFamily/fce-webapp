<?php

namespace App\Orchid\Screens\Orders;

use Orchid\Screen\Screen;
use App\Models\Orders;
use App\Models\User;
use Orchid\Support\Facades\Layout;
use App\View\Components\UserOrder;

class OrderSingleScreen extends Screen
{
    /**
     * @var Order
     */
    public $order;
    public $order_id;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(int $order_id): iterable
    {
        return [
            'order_id' => $order_id,
            'order' => Orders::find($order_id)
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Ordine #'.$this->order_id;
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
            
        ];
    }
}
