<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\Customer;

class ReferalController extends Controller
{
    public function generateReferralLink(Customer $customer)
    {
        // Generate a unique referral link for the customer
        $referralLink = route('register', ['referrer_id' => $customer->id]);

        return response()->json(['referral_link' => $referralLink]);
    }
    public function processReferral(Request $request)
    {
        // Get the referrer ID from the request
        $referrerId = $request->input('referrer_id');

        // Find the referrer customer
        $referrer = Customer::find($referrerId);

        if (!$referrer) {
            return response()->json(['error' => 'Invalid referrer ID'], 422);
        }

        $customer = new Customer();
        $customer->referrer_id = $referrerId;
        $customer->save();

        // Update the referrer's wallet balance
        $referrer->wallet += config('referral.bonus_amount');
        $referrer->save();

        return response()->json(['message' => 'Referral processed successfully']);
    }
}
