<?php

namespace App\Http\Controllers;

use App\Models\RdpSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RdpSettingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Settings/Rdp/Index', [
            'setting' => RdpSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['nullable', 'string', 'max:2048', 'url'],
        ]);

        $setting = RdpSetting::query()->firstOrNew();
        $setting->fill($validated);
        $setting->save();

        RdpSetting::forgetCurrent();

        return redirect()
            ->route('rdp-settings.index')
            ->with('message', 'rdp-stored');
    }
}
