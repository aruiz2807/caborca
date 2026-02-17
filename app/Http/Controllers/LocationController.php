<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all(['id', 'name']);

        return Inertia::render('Settings/Locations/Index', [
            'locations' => $locations
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255']
        ])->validate();

        Location::create([
            'name' => $request['name']
        ]);

        return to_route('locations.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {

        return to_route('locations.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {

        return to_route('locations.index')->with('message', 'deleted');
    }
}
