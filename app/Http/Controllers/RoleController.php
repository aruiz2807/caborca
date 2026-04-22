<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions:id,name')->get(['id', 'name', 'description']);
        $permissions = Permission::all(['id', 'name']);

        return Inertia::render('Settings/Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions
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

    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        Validator::make($request->input(), [
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name']
        ])->validate();

        $role->syncPermissions($request->input('permissions', []));

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
