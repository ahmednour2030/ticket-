<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tickets = [
            [
                'title' => 'normal ticket',
                'description' => 'normal ticket description',
                'price' => 200,
                'is_vip' => 0
            ],
            [
                'title' => 'vip ticket',
                'description' => 'normal ticket description',
                'price' => 500,
                'is_vip' => 1
            ]
        ];

        Ticket::insert($tickets);
    }
}
