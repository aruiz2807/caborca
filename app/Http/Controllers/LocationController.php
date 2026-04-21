<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all(['id', 'name', 'status']);

        return Inertia::render('Settings/Locations/Index', [
            'locations' => $locations
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'status' => [Rule::enum(Status::class)],
        ])->validate();

        Location::create([
            'name' => $request['name'],
            'status' => $request['status'] ?? Status::ACTIVE,
        ]);

        return to_route('locations.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(Status::class)],
        ])->validate();

        $location = Location::findOrFail($id);
        $location->update([
            'name' => $request['name'],
            'status' => $request['status'],
        ]);

        return to_route('locations.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {
        $location = Location::withCount(['dependencies', 'workshops', 'orders'])->findOrFail($id);

        if ($location->dependencies_count > 0 || $location->workshops_count > 0 || $location->orders_count > 0) {
            return back()->with('error', 'app.delete_error');
        }

        $location->delete();

        return to_route('locations.index')->with('message', 'deleted');
    }
}
