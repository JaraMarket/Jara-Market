<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name'         => ['required', 'string', 'max:255'],
            'site_description'  => ['nullable', 'string'],
            'contact_email'     => ['required', 'email'],
            'contact_phone'     => ['nullable', 'string'],
            'support_email'     => ['nullable', 'string'],
            'address'           => ['nullable', 'string'],
            'currency'          => ['required', 'string'],
            'tax_rate'          => ['required', 'numeric', 'min:0', 'max:100'],
            'shipping_fee'      => ['required', 'numeric', 'min:0'],
            'social_facebook'   => ['nullable', 'url'],
            'social_twitter'    => ['nullable', 'url'],
            'social_instagram'  => ['nullable', 'url'],
            'social_youtube'    => ['nullable', 'url'],
            'social_tiktok'     => ['nullable', 'url'],
            'payment_methods'   => ['nullable','array'],
            'order_statuses'    => ['nullable'],
            'minimum_order_amount' => ['nullable', 'numeric'],
            'first_order_bonus'    => ['nullable', 'numeric'],
            'repeat_order_bonus'   => ['nullable', 'numeric'],
            'timezone'             => ['nullable', 'timezone'],
            'company_logo'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:1024'],
            'favicon_logo'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:1024'],
        ]);
      
        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = upload_image("logo", $request->company_logo);
        }

        if ($request->hasFile('favicon_logo')) {
            $validated['favicon_logo'] = upload_image("logo", $request->favicon_logo);
        }
      
        if (isset($validated['payment_methods'])) {
            $validated['payment_methods'] = implode(',', $validated['payment_methods']);
        }

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()
            ->with('success', 'Settings saved successfully');
    }
}
