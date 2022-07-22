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
     * @throws Stripe\Exception\ApiErrorException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'name' => 'required|string',
//            'last_name' => 'required|string',
            'email' => 'required|email|unique:invoices,email',
//            'city' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'count' => 'required|string',
            'card_number' => 'required',
            'card_exp_month' => 'required',
            'card_exp_year' => 'required',
            'card_cvc' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponseValidation($validator);
        }

        $ticket = $this->ticketModel->find($request->post('ticket_id'));

        $price = $ticket['price'];

        $totalPrice = $request->post('count') * $price;

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $data = $stripe->tokens->create([
            'card' => [
                'number' => $request->post('card_number'),
                'exp_month' => $request->post('card_exp_month'),
                'exp_year' => $request->post('card_exp_year'),
                'cvc' => $request->post('card_cvc'),
            ],
        ]);
        $stripeToken = $data['id'];
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $stripe = Stripe\Charge::create ([
            "amount" => 100 * 100,
            "currency" => "usd",
            "source" => $stripeToken,
            "description" => "Test payment from itsolutionstuff.com."
        ]);

        $invoice = $this->invoiceModel->create([
            'uuid' => Str::uuid()->toString(),
            'ticket_id' => $request->post('ticket_id'),
            'name' => $request->post('name'),
//            'last_name' => $request->post('last_name'),
            'email' => $request->post('email'),
//            'city' => $request->post('city'),
            'address' => $request->post('address'),
            'phone' => $request->post('phone'),
            'count' => $request->post('count'),
            'price' => $price,
            'strip_id' => $stripe['id'],
            'total_price' => $totalPrice,
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
