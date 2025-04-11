<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cus
class ReferalController extends Controller
{
    public function generateReferralLink(Customer $customer)
    {
        // Generate a unique referral link for the customer
        $referralLink = route('register', ['referrer_id' => $customer->id]);

        return response()->json(['referral_link' => $referralLink]);
    }

}
