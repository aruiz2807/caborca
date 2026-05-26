<?php

namespace App\Http\Controllers;

use App\Models\TsplusSetting;
use Inertia\Inertia;
use Inertia\Response;

class TsplusController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tsplus/Index', [
            'url' => TsplusSetting::configuredUrl(),
        ]);
    }
}
