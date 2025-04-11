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
use App\Http\Requests\customerLoginRequest;
use App\Mail\CustomerLoginOtp;
use Illuminate\Support\Str;
class CustomerController extends Controller
{

    public function Customer_Register(customerSignupRequest $request)
    {
        // Check if a referral code is provided
        $referralCode = $request->input('referral_code');

        // Register the Customer
        $data = Customer::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => str_random(10), // Generate a unique referral code for the customer
        ]);

        // If a referral code is provided, validate it and update the referrer's account
        if ($referralCode) {
            $referrer = Customer::where('referral_code', $referralCode)->first();

            if ($referrer) {
                // Update the referrer's account (e.g., increment their referral count)
                $referrer->update([
                    'referral_count' => $referrer->referral_count + 1,
                ]);

                // Update the customer's account to link them to the referrer
                $data->update([
                    'referrer_id' => $referrer->id,
                ]);
            } else {
                // Return an error if the referral code is invalid
                return response()->json(['success' => false, 'message' => 'Invalid referral code'], 400);
            }
        }

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

        $customerData = Customer::where('email', $customer_id)->first();
        $customerotp = Customer_otp::where('customer_id', $customer_id)->first();
        if (!$customerotp) {
            return response()->json(['success' => false, 'message' => 'No customer record found'], 404);
        }

        $otpRecord = Customer_otp::where('otp', $otp)->where('customer_id', $customer_id)->where('created_at', '>=', now()->subMinutes(50)) // Only consider OTPs created in the last 15 minutes
        ->first();

        if ($otpRecord) {
            // Update email_verified_at field
            $customerData->update([
                'email_verified_at' => now(),
            ]);

            // Send notification via email
            Mail::to($customer_id)->send(new CustomerRegistered($customerData));

            $otpRecord->delete();

            $token = $customerData->createToken('Customer_signUp')->plainTextToken;
            return response()->json(['success' => true, 'message' => 'OTP validated successfully and registration Complete', 'token' => $token], 201);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid OTP or OTP has expired'], 400);
        }
    }

public function Customer_login(customerLoginRequest $request)
{
    // Retrieve customer data from the request
    $email = $request->email;
    $password = $request->password;

    // Check if the customer exists
    $customer = Customer::where('email', $email)->first();

    if (!$customer) {
        return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
    }

    // Check if the password is correct
    if (!Hash::check($password, $customer->password)) {
        return response()->json(['success' => false, 'message' => 'Invalid password'], 400);
    }

    // Generate a random OTP
    $otp = rand(1000, 9999);

    // Save the OTP to the database
    $data=Customer_otp::create([
        'otp' => $otp,
        'customer_id' => $customer->email,
    ]);

    // Send the OTP to the customer's email address
    Mail::to($customer->email)->send(new CustomerLoginOtp($otp, $customer->firstname));

    // Return a success response with the OTP
    return response()->json(['success' => true, 'message' => 'An OTP has been sent to your email address. OTP expires after 15 minutes.', 'data' => $data], 201);
}

public function validateCustomerLoginOTP(RegisterOTPRequest $request)
{
    // Retrieve the OTP and email from the request
    $otp = $request->otp;
    $email = $request->email;

    // Check if the customer exists
    $customer = Customer::where('email', $email)->first();

    if (!$customer) {
        return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
    }

    // Check if the OTP is valid
    $otpRecord = Customer_otp::where('otp', $otp)->where('customer_id', $email)->where('created_at', '>=', now()->subMinutes(15)) // Only consider OTPs created in the last 15 minutes
    ->first();

    if (!$otpRecord) {
        return response()->json(['success' => false, 'message' => 'Invalid OTP or OTP has expired'], 400);
    }

    // Delete the OTP record
    $otpRecord->delete();

    // Generate a new token for the customer
    $token = $customer->createToken('Customer_login')->plainTextToken;

    // Return a success response with the token
    return response()->json(['success' => true, 'message' => 'OTP validated successfully and login complete', 'token' => $token, 'data'=> $otpRecord], 201);
}

public function fetchProfile($email)
    {

        $data = Customer::where('email', $email)->first();
        if ($data) {
            return response()->json($data, 201);
        } else {
            return response()->json(['success' => false, 'message' => 'customer data not found'], 404);
        }
    }

    public function editProfile($email, Request $request)
    {

        $customerData = Customer::where('email', $email)->first();

        // Check if the customer exists
        if (!$customerData) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

             $customerData->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname
              ]);

        if ($customerData) {
            return response()->json(['success' => true, 'message' => 'Profile Updated Successfully','data' =>$customerData], 201);
        } else {
            return response()->json(['success' => false, 'message' => 'Problem Updating Profile'], 400);
        }
    }


}
