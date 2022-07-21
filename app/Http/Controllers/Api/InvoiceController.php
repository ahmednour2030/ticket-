<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isNull;
use Stripe;

class InvoiceController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var Invoice
     */
    private $invoiceModel;

    /**
     * @var Ticket
     */
    private $ticketModel;

    /**
     * @param Invoice $invoice
     * @param Ticket $ticket
     */
    public function __construct(Invoice $invoice, Ticket $ticket)
    {
        $this->invoiceModel = $invoice;
        $this->ticketModel = $ticket;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:invoices,email',
            'city' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'count' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponseValidation($validator);
        }

        $ticket = $this->ticketModel->find($request->post('ticket_id'));

        $price = $ticket['price'];

        $totalPrice = $request->post('count') * $price;

        $invoice = $this->invoiceModel->create([
            'uuid' => Str::uuid()->toString(),
            'ticket_id' => $request->post('ticket_id'),
            'first_name' => $request->post('first_name'),
            'last_name' => $request->post('last_name'),
            'email' => $request->post('email'),
            'city' => $request->post('city'),
            'address' => $request->post('address'),
            'phone' => $request->post('phone'),
            'count' => $request->post('count'),
            'price' => $price,
            'total_price' => $totalPrice,
        ]);

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $data = $stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 7,
                'exp_year' => 2023,
                'cvc' => '314',
            ],
        ]);
        $stripeToken = $data['id'];
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
            "amount" => 100 * 100,
            "currency" => "usd",
            "source" => $stripeToken,
            "description" => "Test payment from itsolutionstuff.com."
        ]);

       // Mail::to($request->post('email'))->send(new InvoiceMail());

        return $this->apiResponse('successfully', $invoice);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function entry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_uuid' => 'required|exists:invoices,uuid',
        ]);

        if ($validator->fails()) {
            return $this->apiResponseValidation($validator);
        }

        $invoice = $this->invoiceModel->whereUuid($request->post('invoice_uuid'))->first();

        if(!isNull($invoice->entry_at)){
            return $this->apiResponse('the invoice is expired', null, 'not allow', 422);
        }

        $invoice->update(['entry_at' => now()]);

        return $this->apiResponse('successfully', $invoice);
    }
}
