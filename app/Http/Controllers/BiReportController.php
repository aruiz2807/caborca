<?php

namespace App\Http\Controllers;

use App\Models\BiReport;
use Inertia\Inertia;
use Inertia\Response;

class BiReportController extends Controller
{
    public function show(BiReport $biReport): Response
    {
        $user = request()->user();

        abort_unless($user, 403);
        abort_unless($user->can($biReport->section->permissionName()), 403);
        abort_unless($user->can($biReport->permissionName()), 403);

        return Inertia::render('Reports/Show', [
            'report' => [
                'id' => $biReport->id,
                'name' => $biReport->name,
                'section_name' => $biReport->section->name,
                'embed_url' => $biReport->embed_url,
            ],
        ]);
    }
}

