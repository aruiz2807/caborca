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

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(['id', 'name', 'email', 'type', 'bpro_user']);

        return Inertia::render('Settings/Users/Index', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'type' => $request['type'],
        ]);

        return to_route('users.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bpro_user' => ['required_if:type,A', 'string', 'max:5'],
        ])->validate();

        if ($request['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user->id, $request);
        } else {
            $user->forceFill([
                'name' => $request['name'],
                'email' => $request['email'],
                'bpro_user' => $request['bpro_user'],
            ])->save();
        }

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
