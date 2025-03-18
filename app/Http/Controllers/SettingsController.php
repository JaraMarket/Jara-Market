<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\Tag(
 *     name="Settings",
 *     description="API Endpoints for managing application settings"
 * )
 */
class SettingsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/settings",
     *     summary="Get all application settings",
     *     description="Retrieves all application settings as key-value pairs",
     *     operationId="getSettings",
     *     tags={"Settings"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             additionalProperties={
     *                 "type": "string",
     *                 "example": "value"
     *             }
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Setting::pluck('value', 'key');
    }

    /**
     * @OA\Post(
     *     path="/api/settings",
     *     summary="Update application settings",
     *     description="Updates multiple application settings with key-value pairs",
     *     operationId="updateSettings",
     *     tags={"Settings"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Key-value pairs of settings to update",
     *         @OA\JsonContent(
     *             type="object",
     *             additionalProperties={
     *                 "type": "string",
     *                 "example": "value"
     *             },
     *             example={
     *                 "site_name": "JaraMarket",
     *                 "contact_email": "support@jaramarket.com",
     *                 "maintenance_mode": "false"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Settings updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Settings saved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['message' => 'Settings saved successfully']);
    }
}
