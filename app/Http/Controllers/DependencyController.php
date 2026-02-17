<?php

namespace App\Http\Controllers;

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
        $dependencies = Dependency::all(['id', 'name', 'customer_number', 'location_id', 'user_id']);
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
            'customer_number' => ['required', 'string', 'max:10'],
            'location_id' => ['required'],
            'user_id' => ['required'],
        ])->validate();

        Dependency::create([
            'name' => $request['name'],
            'customer_number' => $request['customer_number'],
            'location_id' => $request['location_id'],
            'user_id' => $request['user_id']
        ]);

        return to_route('dependencies.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {

        return to_route('dependencies.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {

        return to_route('dependencies.index')->with('message', 'deleted');
    }
}
