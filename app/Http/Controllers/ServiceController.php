<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all(['id', 'name', 'status']);

        return Inertia::render('Settings/Services/Index', [
            'services' => $services
        ]);
    }

    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255']
        ])->validate();

        Service::create([
            'name' => $request['name']
        ]);

        return to_route('services.index')->with('message', 'stored');
    }

    public function update(Request $request, $id)
    {
        Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(\App\Enums\Status::class)],
        ])->validate();

        $service = Service::findOrFail($id);
        $service->update([
            'name' => $request['name'],
            'status' => $request['status'],
        ]);

        return to_route('services.index')->with('message', 'stored');
    }

    public function destroy(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if ($service->orders()->exists()) {
            return to_route('services.index')->with('error', 'has-orders');
        }

        $service->delete();

        return to_route('services.index')->with('message', 'deleted');
    }
}
