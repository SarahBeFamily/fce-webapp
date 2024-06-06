<?php

namespace App\Orchid\Screens\Orders;

use Orchid\Screen\Screen;
use App\Models\Orders;
use Orchid\Platform\Models\Order;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Components\Cells\Currency;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Sight;


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
    public function query($order_id): iterable
    {
        // var_dump($order_id);
        
        // $order_id = $request->route('order_id');
        $order_array = Orders::find($order_id)->toArray();
        
        return [
            'order_id' => $order_id,
            'order' => $order_array,
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
        return [
            Link::make(__('Modifica'))
                ->icon('pencil')
                ->route('platform.systems.orders.edit', $this->order_id),
            Link::make(__('Elimina'))
                ->icon('trash')
                ->method('remove'),
        ];
    }

    /**
     * Creo un array delle informazioni dell'ordine
     */
    public function orderInfo($order)
    {
        return $order['order_data_list'];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
     
        return [

            Layout::accordion([
                'Dettagli' => Layout::view('components.orchid.order-details', [
                    'order' => $this->order,
                    'order_items' => $this->orderInfo($this->order),
                    'user' => User::find($this->order['user_id']),
                ]),
            ]),

            Layout::accordion([
                'Prodotti' => Layout::view('components.orchid.order-items', [
                    'order' => $this->order,
                    'order_items' => $this->orderInfo($this->order),
                ]),
            ]),
        ];
    }
}
