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
use App\Models\User_otp;
use App\Models\Wallet;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for managing users"
 * )
 */
class UserController extends Controller
{


    public function registerUser(UserSignupRequest $request)
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

        // Retrieve the user instance and Add create user's wallet
        Wallet::create([
            'user_id' => $data->user_id
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

                //update user_id in Wallet 


            } else {
                // Return an error if the referral code is invalid
                return response()->json(['success' => false, 'message' => 'Invalid referral code'], 400);
            }
        }

        // Generate OTP and save
        $otp = rand(1000, 9999); // Generate a 4-digit OTP

        User_otp::create([
            'otp' => $otp,
            'email' => $request->email, // Use the customer ID instead of email
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

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        // Check if the OTP is valid
        $otpRecord = User_otp::where('otp', $otp)->where('user_id', $email)->where('created_at', '>=', now()->subMinutes(15)) // Only consider OTPs created in the last 15 minutes
            ->first();

        if (!$otpRecord) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP or OTP has expired'], 400);
        }

        // Delete the OTP record
        $otpRecord->delete();

        // Generate a new token for the customer
        $token = $user->createToken('user_login')->plainTextToken;

        // Return a success response with the token
        return response()->json(['success' => true, 'message' => 'OTP validated successfully and login complete', 'token' => $token, 'data' => $otpRecord], 201);
    }


    public function fetchProfile($email)
    {
        $cacheKey = 'user-profile-' . $email;
        $cacheTime = 60; // Cache for 1 hour

        $data = Cache::remember($cacheKey, $cacheTime, function () use ($email) {
            return User::where('email', $email)->first();
        });

        if ($data) {
            return response()->json($data, 201);
        } else {
            return response()->json(['success' => false, 'message' => 'customer data not found'], 404);
        }
    }

        public function editProfile($email, Request $request)
    {

        $userData = User::where('email', $email)->first();

        // Check if the customer exists
        if (!$userData) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $userData->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname
        ]);

        if ($userData) {
            return response()->json(['success' => true, 'message' => 'Profile Updated Successfully', 'data' => $userData], 201);
        } else {
            return response()->json(['success' => false, 'message' => 'Problem Updating Profile'], 400);
        }
    }



     /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get all users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="List of users retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="role", type="string"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return User::all();
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID of the user",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="role", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return response()->json(['message' => 'User updated successfully']);
    }

    /**
     * @OA\Patch(
     *     path="/users/{id}/toggle-status",
     *     summary="Toggle user active status",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID of the user",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="User status updated successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->active = !$user->active;
        $user->save();

        return response()->json(['message' => 'User status updated successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="UUID of the user",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="User deleted successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
