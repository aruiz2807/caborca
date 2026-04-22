<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get(['id', 'name', 'email', 'type', 'bpro_user', 'status']);
        $roles = Role::all(['id', 'name']);

        return Inertia::render('Settings/Users/Index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'type' => ['required', Rule::in(['A', 'G'])],
            'bpro_user' => ['required_if:type,A', 'nullable', 'string', 'max:5'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'type' => $request['type'],
            'bpro_user' => $request['type'] === 'A' ? $request['bpro_user'] : null,
        ]);

        $user->assignRole($request['role']);

        return to_route('users.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'type' => ['required', Rule::in(['A', 'G'])],
            'status' => ['required', Rule::enum(\App\Enums\Status::class)],
            'bpro_user' => ['required_if:type,A', 'nullable', 'string', 'max:5'],
            'role' => ['required', 'exists:roles,name'],
        ])->validate();

        if ($request['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $user->forceFill([
                'name' => $request['name'],
                'email' => $request['email'],
                'type' => $request['type'],
                'status' => $request['status'],
                'bpro_user' => $request['type'] === 'A' ? $request['bpro_user'] : null,
                'email_verified_at' => null,
            ])->save();

            $user->sendEmailVerificationNotification();
        } else {
            $user->forceFill([
                'name' => $request['name'],
                'email' => $request['email'],
                'type' => $request['type'],
                'status' => $request['status'],
                'bpro_user' => $request['type'] === 'A' ? $request['bpro_user'] : null,
            ])->save();
        }

        $user->syncRoles([$request['role']]);

        return to_route('users.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();

        return to_route('users.index')->with('message', 'deleted');
    }

    protected function updateVerifiedUser($id, Request $request): void
    {
        $user = User::find($id);

        $user->forceFill([
            'name' => $request['name'],
            'email' => $request['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
