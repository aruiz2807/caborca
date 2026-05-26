<?php

namespace App\Http\Controllers;

use App\Models\RdpSetting;
use Inertia\Inertia;
use Inertia\Response;

class RdpController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Rdp/Index', [
            'url' => RdpSetting::configuredUrl(),
        ]);
    }
}
