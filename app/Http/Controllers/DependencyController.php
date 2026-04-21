<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Location;
use App\Models\Dependency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DependencyController extends Controller
{
    public function index()
    {
        $dependencies = Dependency::with(['location', 'user'])->get(['id', 'name', 'customer_number', 'location_id', 'user_id', 'status']);
        $locations = Location::all(['id', 'name']);
        $users = User::select(['id', 'name'])->where('type', 'G')->get();

        return Inertia::render('Settings/Dependencies/Index', [
            'dependencies' => $dependencies,
            'locations' => $locations,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'customer_number' => ['required', 'string', 'max:10', Rule::unique('dependencies')],
            'location_id' => ['required'],
            'user_id' => ['required'],
            'status' => ['nullable', Rule::enum(Status::class)],
        ])->validate();

        Dependency::create([
            'name' => $request['name'],
            'customer_number' => $request['customer_number'],
            'location_id' => $request['location_id'],
            'user_id' => $request['user_id'],
            'status' => $request['status'] ?? Status::ACTIVE,
        ]);

        return to_route('dependencies.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'customer_number' => ['required', 'string', 'max:10', Rule::unique('dependencies')->ignore($id)],
            'location_id' => ['required'],
            'user_id' => ['required'],
            'status' => ['required', Rule::enum(Status::class)],
        ])->validate();

        $dependency = Dependency::findOrFail($id);
        $dependency->update([
            'name' => $request['name'],
            'customer_number' => $request['customer_number'],
            'location_id' => $request['location_id'],
            'user_id' => $request['user_id'],
            'status' => $request['status'],
        ]);

        return to_route('dependencies.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {
        $dependency = Dependency::withCount('orders')->findOrFail($id);

        if ($dependency->orders_count > 0) {
            return back()->with('error', 'app.delete_error');
        }

        $dependency->delete();

        return to_route('dependencies.index')->with('message', 'deleted');
    }
}
