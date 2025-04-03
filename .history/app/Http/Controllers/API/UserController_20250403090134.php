<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserSignupRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\UserRegistered;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegisteredOtp;
use App\Http\Requests\RegisterOTPRequest;
use App\Http\Requests\UserLoginRequest;
use App\Mail\UserLoginOtp;
use Illuminate\Support\Facades\Cache;
use App\Models\API\User_otp;

class UserController extends Controller
{


    public function Customer_Register(UserSignupRequest $request)
    {
        $referralCode = $request->input('referral_code');

        // Register the Customer
        $data = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => substr(str_shuffle('0123456789'), 0, 4)
        ]);

        // If a referral code is provided, validate it and update the referrer's account
        if ($referralCode) {
            $referrer = User::where('referral_code', $referralCode)->first();

            if ($referrer) {
                // Update the referrer's account (e.g., increment their referral count)
                $referrer->update([
                    'referral_count' => $referrer->referral_count + 1,
                ]);

                // Update the customer's account to link them to the referrer
                $data->update([
                    'referrer_id' => $referrer->id,
                ]);

                // Update the referrer's wallet with a bonus
                $referral_bonus = config('app.referral_bonus');

                $referrer->update([
                    'wallet' => $referrer->wallet + $referral_bonus

                ]);
            } else {
                // Return an error if the referral code is invalid
                return response()->json(['success' => false, 'message' => 'Invalid referral code'], 400);
            }
        }

        // Generate OTP and save
        $otp = rand(1000, 9999); // Generate a 4-digit OTP

        User_otp::create([
            'otp' => $otp,
            'user_id' => $data->email, // Use the customer ID instead of email
        ]);

        // Send OTP via email
        Mail::to($data->email)->send(new UserRegisteredOtp($otp, $data->firstname));

        $token = $data->createToken('User_signUp')->plainTextToken;

        // Return success response
        return response()->json(['message' => 'An OTP has been sent to your email address. It expires after 15 minutes.', 'token' => $token, 'data' => $data], 201);
    }

    public function validateUserRegisterOTP(RegisterOTPRequest $request)
    {
        $otp = $request->otp;
        $user_id = $request->user_id;

        $UserData = User::where('email', $user_id)->first();
        $Userotp = User_otp::where('user_id', $user_id)->first();
        if (!$Userotp) {
            return response()->json(['success' => false, 'message' => 'No customer record found'], 404);
        }

        $otpRecord = User_otp::where('otp', $otp)->where('user_id', $user_id)->where('created_at', '>=', now()->subMinutes(50)) // Only consider OTPs created in the last 15 minutes
            ->first();

        if ($otpRecord) {
            // Update email_verified_at field
            $UserData->update([
                'email_verified_at' => now(),
            ]);

            // Send notification via email
            Mail::to($user_id)->send(new UserRegistered($UserData));

            $otpRecord->delete();

            $token = $UserData->createToken('User_signUp')->plainTextToken;
            return response()->json(['success' => true, 'message' => 'OTP validated successfully and registration Complete', 'token' => $token], 201);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid OTP or OTP has expired'], 400);
        }
    }
    public function User_login(UserLoginRequest $request)
    {
        // Retrieve customer data from the request
        $email = $request->email;
        $password = $request->password;

        // Check if the customer exists
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        // Check if the password is correct
        if (!Hash::check($password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid password'], 400);
        }

        // Generate a random OTP
        $otp = rand(1000, 9999);

        // Save the OTP to the database
        $data = User_otp::create([
            'otp' => $otp,
            'user_id' => $user->email,
        ]);

        // Send the OTP to the customer's email address
        Mail::to($user->email)->send(new UserLoginOtp($otp, $user->firstname));

        // Return a success response with the OTP
        return response()->json(['success' => true, 'message' => 'An OTP has been sent to your email address. OTP expires after 15 minutes.', 'data' => $data], 201);
    }


    public function validateUserLoginOTP(RegisterOTPRequest $request)
    {
        // Retrieve the OTP and email from the request
        $otp = $request->otp;
        $email = $request->email;

        // Check if the customer exists
        $user = User::where('email', $email)->first();

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
        return response()->json(['success' => true, 'message' => 'OTP validated successfully and login complete', 'token' => $token, 'data' => $otpRecord], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/customer/profile/{email}",
     *     tags={"Customers"},
     *     summary="Fetch customer profile",
     *     description="Get customer profile information with caching",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="email")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found"
     *     )
     * )
     */
    public function fetchProfile($email)
    {
        $cacheKey = 'customer-profile-' . $email;
        $cacheTime = 60; // Cache for 1 hour

        $data = Cache::remember($cacheKey, $cacheTime, function () use ($email) {
            return Customer::where('email', $email)->first();
        });

        if ($data) {
            return response()->json($data, 201);
        } else {
            return response()->json(['success' => false, 'message' => 'customer data not found'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/customer/profile/{email}",
     *     tags={"Customers"},
     *     summary="Update customer profile",
     *     description="Update customer's firstname and lastname",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="email")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"firstname", "lastname"},
     *             @OA\Property(property="firstname", type="string", example="John"),
     *             @OA\Property(property="lastname", type="string", example="Doe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile Updated Successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Problem updating profile"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found"
     *     )
     * )
     */
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
            return response()->json(['success' => true, 'message' => 'Profile Updated Successfully', 'data' => $customerData], 201);
        } else {
            return response()->json(['success' => false, 'message' => 'Problem Updating Profile'], 400);
        }
    }
}
