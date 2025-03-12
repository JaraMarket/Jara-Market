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

    public function Customer_Register(customerSignupRequest $request)
    {
        // Register the Customer
        $data = Customer::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate OTP and save
        $otp = rand(1000, 9999); // Generate a 4-digit OTP

        Customer_otp::create([
            'otp' => $otp,
            'customer_id' => $data->email, // Use the customer ID instead of email
        ]);

         // Send OTP via email
        Mail::to($data->email)->send(new CustomerRegisteredOtp($otp, $data->firstname));

        $token = $data->createToken('Customer_signUp')->plainTextToken;

        // Return success response
        return response()->json(['message' => 'An OTP has been sent to your email address. It expires after 15 minutes.', 'token' => $token, 'data' => $data], 201);
    }
public function validateCustomerRegisterOTP(RegisterOTPRequest $request)
{
    $otp = $request->otp;
    $customer_id = $request->customer_id;

    $customerData = Customer_otp::where('customer_id', $email)->first();
    if (!$customerData) {
        return response()->json(['success' => false, 'message' => 'No customer record found'], 404);
    }

    $otpRecord = Customer_otp::where('otp', $otp)->where('email', $email)->where('created_at', '>=', now()->subMinutes(50)) // Only consider OTPs created in the last 15 minutes
    ->first();

    if ($otpRecord) {
    $otpRecord->delete();

        // Send notification via email
        Mail::to($customerData->email)->send(new CustomerRegistered($customerData));

        $token = $customerData->createToken('Customer_signUp')->plainTextToken;
        return response()->json(['success' => true, 'message' => 'OTP validated successfully and registration Complete', 'token' => $token], 201);
    } else {
        return response()->json(['success' => false, 'message' => 'Invalid OTP or OTP has expired'], 401);
    }
}
}
