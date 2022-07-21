<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var Ticket
     */
    private $ticketModel;

    /**
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticketModel = $ticket;
    }

    public function index()
    {
        $tickets = $this->ticketModel->get();

        return $this->apiResponse('successfully', $tickets);
    }

}
