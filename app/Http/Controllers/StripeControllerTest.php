<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\StripeController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class StripeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_amount_is_float(): void
    {
        $request = new Request();
        $request->merge(['total' => '10.99']);

        $stripeController = new StripeController();
        $result = $stripeController->processOrder($request);

        $this->assertIsFloat($result['order_amount']);
    }
}