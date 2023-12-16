<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Orders;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Orders::create(
            [
                'user_id' => 1,
                'order_type' => 'ticket',
                'order_status' => 'pagato',
                'order_data_list' => json_encode([
                    'Babylon Sala 3 - Posto 4 fila 4', 
                    'Babylon Sala 3 - Posto 5 fila 4', 
                    'popcorn salati', 
                    'popcorn salati', 
                    'fanta', 
                    'sprite',
                ]),
                'order_amount' => '45.00',
                'order_transaction' => 'sgbfsgvjdkcn64bd',
                'order_ref_cinema' => '600',
                'order_notes' => 'note ordine'
            ]
        );

        Orders::create(
            [
                'user_id' => 1,
                'order_type' => 'ticket',
                'order_status' => 'pagato',
                'order_data_list' => json_encode([
                    'Babylon Sala 3 - Posto 5 fila 7', 
                    'Babylon Sala 3 - Posto 6 fila 7', 
                    'popcorn salati', 
                    'popcorn salati', 
                    'fanta', 
                    'sprite',
                ]),
                'order_amount' => '45.00',
                'order_transaction' => 'sgbfsgvjdkcn64bd',
                'order_ref_cinema' => '600',
                'order_notes' => 'note ordine'
            ]
        );
    }
}
