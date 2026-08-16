<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => [
                'site_name' => setting('site_name', config('app.name')),
                'logo' => setting('logo'),
                'favicon' => setting('favicon'),
                'smtp_host' => setting('smtp_host'),
                'seo_title' => setting('seo_title'),
                'seo_description' => setting('seo_description'),
                'social_facebook' => setting('social_facebook'),
                'social_twitter' => setting('social_twitter'),
                'social_instagram' => setting('social_instagram'),
                'currency' => setting('currency', 'BDT'),
                'timezone' => setting('timezone', 'UTC'),
                'maintenance_mode' => setting('maintenance_mode', false),
                'shipping_cost_dhaka' => setting('shipping_cost_dhaka', 0),
                'shipping_cost_outside_dhaka' => setting('shipping_cost_outside_dhaka', 0),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:1024'],
            'favicon' => ['nullable', 'image', 'max:256'],
            'smtp_host' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'social_facebook' => ['nullable', 'url'],
            'social_twitter' => ['nullable', 'url'],
            'social_instagram' => ['nullable', 'url'],
            'currency' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'string', 'max:60'],
            'maintenance_mode' => ['boolean'],
            'shipping_cost_dhaka' => ['required', 'numeric', 'min:0'],
            'shipping_cost_outside_dhaka' => ['required', 'numeric', 'min:0'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            $data['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('status', 'Settings updated.');
    }
}
