<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Franchise; // Assuming you have a Franchise model

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\PathItem(
 *     path="/orders",
 *     description="Operations related to orders"
 * )
 */
class FranchiseController extends Controller
{
    /**
     * Display a listing of the franchises.
     */
    public function index()
    {
        $franchises = Franchise::all(); // Retrieve all franchise records

        return response()->json($franchises, 200);
    }
}
