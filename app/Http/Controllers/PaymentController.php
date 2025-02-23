<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment; // Assuming you have a Payment model
use Yabacon\Paystack;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\PathItem(
 *     path="/orders",
 *     description="Operations related to orders"
 * )
 */
class PaymentController extends Controller
{
    /**
     * Make a payment using Paystack.
     */
    public function makePayment(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:1',
        ]);

        $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));

        try {
            $tranx = $paystack->transaction->initialize([
                'amount' => $request->amount * 100, // Paystack expects the amount in kobo
                'email' => $request->email,
                'callback_url' => route('payment.callback'),
            ]);

            return response()->json(['authorization_url' => $tranx->data->authorization_url], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle Paystack payment callback.
     */
    public function handleCallback(Request $request)
    {
        $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));

        try {
            $tranx = $paystack->transaction->verify([
                'reference' => $request->reference,
            ]);

            if ('success' === $tranx->data->status) {
                // Payment was successful
                return response()->json(['message' => 'Payment successful'], 200);
            }

            return response()->json(['error' => 'Payment failed'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the payment records.
     */
    public function viewPayments()
    {
        $payments = Payment::all(); // Retrieve all payment records

        return response()->json($payments, 200);
    }
}
