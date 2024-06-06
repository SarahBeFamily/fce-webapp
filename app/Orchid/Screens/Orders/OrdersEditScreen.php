<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Orders;

use App\Models\Orders;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\CheckBox;

class OrdersEditScreen extends Screen
{
    /**
     * @var Order
     */
    public $order;

    /**
     * Fetch data to be displayed on the screen.
     *
     *
     * @return array
     */
    public function query(Orders $order): iterable
    {
        return [
            'order' => $order,
            'order_id' => $order->id,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        $order_exists = $this->order->id == request()->route('order_id') ? true : false;
        return $order_exists == true ? 'Modifica Ordine' : 'Aggiungi Ordine';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Modifica o aggiungi un ordine.';
    }

    public function permission(): ?iterable
    {
        return [];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->method('remove')
                ->canSee($this->order->exists),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, Order $order)
	{
		$request->validate([
			'order.id' => [
				'required',
				Rule::unique(Order::class, 'id')->ignore($order),
			],
		]);

		$order->fill($request->get('order'));

		$order->permissions = collect($request->get('permissions'))
			->map(fn ($value, $key) => [base64_decode($key) => $value])
			->collapse()
			->toArray();

		$order->save();

		Toast::info(__('Order was saved'));

		return redirect()->route('platform.systems.orders');
	}

    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Order $order)
    {
        $order->delete();

		Toast::info(__('Order was removed'));

		return redirect()->route('platform.systems.orders');
    }
}
