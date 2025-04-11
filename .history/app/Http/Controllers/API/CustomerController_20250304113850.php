<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\customerSignupRequest;
use App\Models\API\Customer;
use Illuminate\Support\Facades\Hash;
use App\Mail\CustomerRegistered;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerRegisteredOtp;
use App\Http\Requests\RegisterOTPRequest;
use App\Models\API\Customer_otp;

class CustomerController extends Controller
{

public function Customer_signUp(customerSignupRequest $request)
{

// register the Customer
$data = Customer::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);

//generate otp
$otp = rand(10200, 90002);

// Send email notification
Mail::to($data->email)->send(new CustomerRegisteredOtp($otp,$data));

$token = $data->createToken('Customer_signUp')->plainTextToken;

// Return success response
return response()->json(['message' => 'Customer registered successfully.', 'token' => $token,'data'=> $data], 201);
}

public function validateSignupOTP(RegisterOTPRequest $request)
{
    $otp = $request->otp;
    $email = $request->email;

    $customerData = Customer::where('email', $email)->first();
    if (!$customerData) {
        return response()->json(['success' => false, 'message' => 'No customer record found'], 404);
    }

    $otpRecord = Customer_otp::where('otp', $otp)->where('email', $email)->where('created_at', '>=', now()->subMinutes(50)) // Only consider OTPs created in the last 15 minutes
    ->first();

    if ($otpRecord) {
    $otpRecord->delete();

        // Send OTP via email
        Mail::to($customerData->email)->send(new CustomerRegistered($customerData));

        $token = $customerData->createToken('Customer_signUp')->plainTextToken;
        return response()->json(['success' => true, 'message' => 'OTP validated successfully and registration Complete', 'token' => $token], 201);
    } else {
        return response()->json(['success' => false, 'message' => 'Invalid OTP or OTP has expired'], 401);
    }
}
}
