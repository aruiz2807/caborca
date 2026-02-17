<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class WorkshopController extends Controller
{
    public function index()
    {
        $workshops = Workshop::all(['id', 'name', 'location_id', 'database']);
        $locations = Location::all(['id', 'name']);

        return Inertia::render('Settings/Workshops/Index', [
            'workshops' => $workshops,
            'locations' => $locations
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'location_id' => ['required'],
            'database' => ['required', 'string', 'max:255'],
        ])->validate();

        Workshop::create([
            'name' => $request['name'],
            'location_id' => $request['location_id'],
            'database' => $request['database']
        ]);

        return to_route('workshops.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {

        return to_route('workshops.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {

        return to_route('workshops.index')->with('message', 'deleted');
    }
}
