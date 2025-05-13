<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'order'])
            ->latest('payment_date')
            ->paginate(10);

        return view('payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'order']);
        return view('payments.show', compact('payment'));
    }

    public function filter(Request $request)
    {
        $query = Payment::with(['user', 'order']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest('payment_date')->paginate(10);
        return view('payments.index', compact('payments'));
    }

    public function export(Request $request)
    {
        $payments = Payment::with(['user', 'order'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('payment_method'), function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->get();

        return response()->json($payments);
    }
}
