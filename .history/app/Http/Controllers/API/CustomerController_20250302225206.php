<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\customerSignupRequest;
use App\Models\API\Customer;
use Illuminate\Support\Facades\Hash;


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

// Return success response
return response()->json([
    'message' => 'Customer registered successfully.',
    'data' => $data
], 201);

    }
}
