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
//genrate otp
$otp = rand(10200, 90002);

// Send email notification
Mail::to($data->email)->send(new CustomerRegisteredOtp($otp,$data));

//Mail::to($data->email)->send(new CustomerRegistered($data));

// Return success response
return response()->json(['message' => 'Customer registered successfully.','data'=> $data], 201);
}


public function validateLoginOTP(registerOTPRequest $request)
    {

        $otp = $request->otp;
        $voterID = $request->voterID;

        $voterRecord = tblvoter::where('voterID', $voterID)->first();
        if (!$voterRecord) {
            return response()->json(['success' => false, 'message' => 'No voter record found'], 404);
        }
             $otpRecord = tblotp::where('code', $otp)
            ->where('voterID', $voterID)
            ->where('created_at', '>=', now()->subMinutes(50)) // Only consider OTPs created in the last 15 minutes
            ->first();

            if ($otpRecord) {
            $otpRecord->delete();

            Mail::to($voterRecord->email)->send(new LoginSuccessMail($voterRecord->fullname));

            $token = $otpRecord->createToken('otplogin')->plainTextToken;
            return response()->json(['success' => true, 'message' => 'Login Is succcessful', 'token' => $token], 201);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid OTP or OTP has expired'], 401);
        }
    }
}
