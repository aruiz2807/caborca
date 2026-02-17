<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\Location;
use App\Models\Order;
use App\Models\Service;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OrderController extends Controller
{
    /*
    Get all active orders
    */
    public function active()
    {
        $orders = Order::all();
        $services = Service::all(['id', 'name']);
        $locations = Location::all(['id', 'name']);
        $workshops = Workshop::all(['id', 'name']);

        return Inertia::render('Orders/Active/Index', [
            'orders' => $orders,
            'services' => $services,
            'locations' => $locations,
            'workshops' => $workshops,
        ]);
    }

    /*
    Get all orders
    */
    public function archive()
    {
        $orders = Order::all();
        $dependencies = Dependency::all(['id', 'name']);

        return Inertia::render('Orders/Archive/Index', [
            'orders' => $orders,
            'dependencies' => $dependencies,
        ]);
    }

    /*
    Get orders filtered by request parameters
    */
    public function archive_orders($status)
    {
        $orders = Order::where('status', $status)->get();

        return Inertia::render('Orders/Archive/Index', [
            'orders' => $orders,
        ]);
    }

    /*
    Get vehicle data form external service
    */
    public function vehicle_data($economic_number)
    {
        // GET request to external API
        $response = Http::withToken(config('api.api_key'))->acceptJson()->get(config('api.api_url').'/api/dynamic/car', [
            'economic' => $economic_number,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Get the response body as a PHP array/object
            $data = $response->json();

            //Save client data if doesnt exist on dependencies table
            $this->store_dependency($data['data'][0]);

            // Pass the data to view
            return inertia('Orders/Active/Index', [
                'vehicleData' => $data['data'],
            ]);
        }
        else
        {
            // Handle errors
            return back()->with('error', 'Could not retrieve data from the external API.');
        }
    }

    /*
    Store dependecy if doesnt exist on dependencies table
    */
    private function store_dependency($data)
    {
        if(!Dependency::where('customer_number', $data['idClient'])->exists())
        {
            Dependency::create([
                'name' => $data['client'],
                'customer_number' => $data['idClient'],
                'location_id' => 1,
                'user_id' => Auth::id(),
            ]);
        }
    }

    /*
    Save order data
    */
    public function store(Request $request)
    {
        Validator::make($request->input(), [
            'purchase_order' => ['required', 'string', 'max:255'],
            'economic_number' => ['required', 'string', 'max:255'],
            'vehicle_vin' => ['required', 'string', 'max:17'],
            'vehicle_description' => ['required', 'string', 'max:255'],
            'vehicle_plate' => ['required', 'string', 'max:10'],
            'vehicle_model' => ['required', 'string', 'max:4'],
            'service_type' => ['required'],
            'service_date' => ['required'],
            'service_location' => ['required'],
            'service_description' => ['required'],
        ])->validate();

        //check if customer number is registered on the dependencies table
        $dependency = Dependency::select(['id', 'name'])->where('customer_number', $request['vehicle_dependency_id'])->first();

        //if vehicle dependency is not present, search form the dependency associated to the user
        if(!$dependency)
        {
            //Get the dependency associated with the user requesting the order
            $dependency = Dependency::select(['id', 'name'])->where('user_id', $request->user()->id)->first();
        }

        Order::create([
            'purchase_order' => $request['purchase_order'],
            'economic_number' => $request['economic_number'],
            'order_file' => 'file_name.tmp',
            'vehicle_dependency_id' => $dependency->id,
            'vehicle_vin' => $request['vehicle_vin'],
            'vehicle_description' => $request['vehicle_description'],
            'vehicle_plate' => $request['vehicle_plate'],
            'vehicle_model' => $request['vehicle_model'],
            'service_type_id' => $request['service_type'],
            'service_requested_date' => $request['service_date'],
            'service_location_id' => $request['service_location'],
            'service_description' => $request['service_description'],
        ]);

        return to_route('orders.active')->with('message', 'stored');
    }

    public function api_update(Request $request)
    {
        Validator::make($request->input(), [
            'appointment' => ['required'],
            'service_order' => ['required'],
            'service_order_date' => ['required'],
            'service_order_status' => ['required'],
            'service_order_cono' => ['required'],
            'service_order_kilometraje' => ['required'],
            'service_order_user' => ['required'],
            'service_order_workshop' => ['required'],
        ])->validate();


        //look up for the order with the appointment number and the workshop id
        $workshop = Workshop::where('database', $request['service_order_workshop'])->first();

        $order = Order::where([
            ['appointment', $request['appointment']],
            ['appointment_workshop_id', $workshop->id],
        ])->first();

        if($order)
        {
            $order->service_order = $request['service_order'];
            $order->service_order_date = $request['service_order_date'];
            $order->service_order_status = $request['service_order_status'];
            $order->service_order_cone = $request['service_order_cono'];
            $order->service_order_mileage = $request['service_order_kilometraje'];
            $order->service_order_user = $request['service_order_user'];
            $order->service_order_workshop_id = $workshop->id;
            $order->status = 3; //Set to 3 for entered status
            $order->save();

            return response()->json([
                'message' => 'Successful request!',
                'info' => 'The order was updated succesfully',
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Request failed!',
                'error' => 'The order with especified appointment number and workshop does not exist',
            ], 404);
        }
    }

    public function schedule(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        $dependency = Dependency::find($order->vehicle_dependency_id);
        $workshop = Workshop::find($request['workshop']);

        // POST request to external API
        $response = Http::withToken(config('api.api_key'))->acceptJson()->post(config('api.api_url').'/api/dynamic/cita', [
            'base' => $workshop->database,
            'idasesor' => $request->user()->bpro_user,
            'fechaCita' => date("d/m/Y", strtotime($request['date'])), // $request['date'],
            'horaCita' => '12:00',
            'idPersona' => $dependency->customer_number,
            'modelo' => $order->vehicle_model,
            'placas' => $order->vehicle_plate,
            'serie' => $order->vehicle_vin,
            'comentarios' => $order->service_description,
            'descripcionTrabajo' => $order->service_type,
        ]);

        // Check if the request was successful
        if ($response->successful())
        {
            // Get the response body as a PHP array/object
            $response_data = $response->json();
            $appointment = $response_data['data'][0]['CIT_IDCITA'] ?? null;

            // Update order record in database
            $order->appointment = $appointment;
            $order->appointment_date = $request['date'];
            $order->appointment_workshop_id = $request['workshop'];
            $order->status = 2; //Set to 2 for scheduled status
            $order->save();

            //TODO - Write log

            return to_route('orders.active')->with(['message' => 'stored', 'cit_id_cita' => $appointment]);
        }

        return response()->json([
            'message' => 'Request failed!',
            'error' => $response->body(), // Get the raw response body
        ], $response->status());
    }
}
