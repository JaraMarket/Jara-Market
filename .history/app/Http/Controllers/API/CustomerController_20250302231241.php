<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\customerSignupRequest;
use App\Models\API\Customer;
use Illuminate\Support\Facades\Hash;
use App\Mail\CustomerRegistered;
use Illuminate\Support\Facades\Mail;


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

// Send email notification
Mail::to($data->email)->send(new CustomerRegisteredOtp($data));

//Mail::to($data->email)->send(new CustomerRegistered($data));

// Return success response
return response()->json([
    'message' => 'Customer registered successfully.',
    'data' => $data
], 201);

    }
}
