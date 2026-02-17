<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all(['id', 'name', 'description']);

        return Inertia::render('Settings/Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['string', 'max:255'],
        ])->validate();

        Role::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'guard_name' => 'web',
        ]);

        return to_route('roles.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255', $role->name !== $request->name ? Rule::unique('roles') : ''],
            'description' => ['string', 'max:255'],
        ])->validate();


        $role->forceFill([
            'name' => $request['name'],
            'description' => $request['description'],
        ])->save();

        return to_route('roles.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {
        $role = Role::find($id);

        //TODO delete role permissions
        $role->delete();

        return to_route('roles.index')->with('message', 'deleted');
    }
}
