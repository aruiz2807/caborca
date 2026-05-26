<?php

namespace App\Http\Controllers;

use App\Models\TsplusSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TsplusSettingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Settings/Tsplus/Index', [
            'setting' => TsplusSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['nullable', 'string', 'max:2048', 'url'],
        ]);

        $setting = TsplusSetting::query()->firstOrNew();
        $setting->fill($validated);
        $setting->save();

        TsplusSetting::forgetCurrent();

        return redirect()
            ->route('tsplus-settings.index')
            ->with('message', 'tsplus-stored');
    }
}
