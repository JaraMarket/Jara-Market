<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting; // Assuming you have a Setting model

class SettingsController extends Controller
{
    public function index()
    {
        return Setting::pluck('value', 'key');
    }

    public function store(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['message' => 'Settings saved successfully']);
    }
}
