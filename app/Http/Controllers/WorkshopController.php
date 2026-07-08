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
        $workshops = Workshop::with(['location', 'advisors'])->get(['id', 'name', 'location_id', 'database', 'status']);
        $locations = Location::all(['id', 'name']);
        $advisors = \App\Models\User::select(['id', 'name'])->where('type', 'A')->get();

        return Inertia::render('Settings/Workshops/Index', [
            'workshops' => $workshops,
            'locations' => $locations,
            'advisors' => $advisors,
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
            'database' => $request['database'],
        ]);

        return to_route('workshops.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'location_id' => ['required', 'exists:locations,id'],
            'database' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(\App\Enums\Status::class)],
        ])->validate();

        $workshop = Workshop::findOrFail($id);
        $workshop->update([
            'name' => $request['name'],
            'location_id' => $request['location_id'],
            'database' => $request['database'],
            'status' => $request['status'],
        ]);

        return to_route('workshops.index')->with('message', 'stored');
    }

    public function updateAdvisors(Request $request, $id)
    {
        Validator::make($request->input(), [
            'advisors' => ['nullable', 'array'],
            'advisors.*' => ['exists:users,id'],
        ])->validate();

        $workshop = Workshop::findOrFail($id);
        $workshop->advisors()->sync($request->input('advisors', []));

        return to_route('workshops.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {
        $workshop = Workshop::findOrFail($id);

        if ($workshop->orders()->exists()) {
            return to_route('workshops.index')->with('error', 'has-orders');
        }

        $workshop->delete();

        return to_route('workshops.index')->with('message', 'deleted');
    }
}
