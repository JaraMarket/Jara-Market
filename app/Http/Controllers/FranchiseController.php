<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Franchise; // Assuming you have a Franchise model

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
