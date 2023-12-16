<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Orders;
use App\Models\User;

class UserOrder extends Component
{
    /**
     * @var User
     */
    public $user;

    /**
     * Create a new component instance.
     */
    public function __construct(Orders $order)
    {
        $this->user = $order->user_id;
        $this->realUser = User::find($this->user);
    }

    /**
     * @return string
     */
    public function get_user_fullname()
    {
        return $this->realUser->user_firstname.' '.$this->realUser->user_lastname;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-order');
    }
}
