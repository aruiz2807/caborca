<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
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
    Get order statistics for the dashboard
    */
    public function home()
    {
        $currentYear = now()->year;

        $totalOrders = Order::whereYear('service_requested_date', $currentYear)->count();
        $attendedOrders = Order::whereYear('service_requested_date', $currentYear)->where('status', OrderStatus::FINISHED)->count();
        $pendingOrders = Order::whereYear('service_requested_date', $currentYear)->whereIn('status', [
            OrderStatus::REQUESTED,
            OrderStatus::SCHEDULED,
            OrderStatus::ENTERED
        ])->count();

        return Inertia::render('Home', [
            'totalOrders' => $totalOrders,
            'attendedOrders' => $attendedOrders,
            'pendingOrders' => $pendingOrders,
        ]);
    }

    /*
    Get all active orders
    */
    public function active()
    {
        $user = Auth::user();

        // Fetch orders where status is not finished
        $query = Order::with(['dependency', 'serviceType', 'serviceLocation', 'appointmentWorkshop'])
            ->where('status', '!=', OrderStatus::FINISHED);
            
        // If the current user is not an admin, filter by their assigned dependency
        if (!$user->hasAnyRole(['Admin', 'Super-Admin'])) {
            $query->whereHas('dependency', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $orders = $query->get();
        
        // Filter orders that need status check (have appointment data)
        $ordersToCheck = $orders->filter(fn($order) => 
            $order->appointment && 
            $order->appointmentWorkshop
        );

        if ($ordersToCheck->isNotEmpty()) 
        {
            foreach($ordersToCheck as $order)
            {
                $this->check_current_status($order);    
            }
            
            /*
            // Concurrent requests to external API to check statuses in parallel
            $responses = Http::pool(fn ($pool) => 
                $ordersToCheck->map(fn ($order) => 
                    $pool->as($order->id)->withToken(config('api.api_key'))->acceptJson()->get(config('api.api_url').'/api/dynamic/cita', [
                            'base' => $order->getRelation('appointmentWorkshop')->database,
                            'cita' => $order->appointment
                        ])
                )
            );

            // Process results and update models in the collection
            foreach ($ordersToCheck as $order) {
                $response = $responses[$order->id] ?? null;
                if ($response && $response->successful()) {
                    $this->updateOrderFromAPI($order, $response->json());
                }
            }
            */
        }

        $services = Service::all(['id', 'name']);
        $locations = Location::all(['id', 'name']);
        $workshops = Workshop::all(['id', 'name']);
        $brands = $this->brands();

        return Inertia::render('Orders/Active/Index', [
            'orders' => $orders,
            'services' => $services,
            'locations' => $locations,
            'workshops' => $workshops,
            'brands' => $brands,
            'dependency' => $user->dependency,
        ]);
    }

    /*
    Get all orders
    */
    public function archive(Request $request)
    {
        $query = Order::with(['dependency', 'serviceType', 'serviceLocation', 'appointmentWorkshop']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vehicle_dependency')) {
            $query->where('vehicle_dependency_id', $request->vehicle_dependency);
        }

        if ($request->filled('order_date_from')) {
            $query->whereDate('service_requested_date', '>=', $request->order_date_from);
        }

        if ($request->filled('order_date_to')) {
            $query->whereDate('service_requested_date', '<=', $request->order_date_to);
        }

        $orders = $query->get();
        $dependencies = Dependency::all(['id', 'name']);

        return Inertia::render('Orders/Archive/Index', [
            'orders' => $orders,
            'dependencies' => $dependencies,
            'filters' => $request->only(['status', 'vehicle_dependency', 'order_date_from', 'order_date_to']),
        ]);
    }

    /*
    Get orders filtered by request parameters
    */
    public function archive_orders($status)
    {
        return redirect()->route('orders.archive', ['status' => $status]);
    }

    /*
    Get brands from external service
    */
    public function brands()
    {
        // GET request to external API
        $response = Http::withToken(config('api.api_key'))->acceptJson()->get(config('api.api_url').'/api/dynamic/marcas', [
            'base' => 'CHRCaborca_TecniHillo', //must correct, because the gob user does not have a workshop assigned
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Get the response body as a PHP array/object
            $data = $response->json();

            return $data['data'];
        }
        else
        {
            // Handle errors
            return null;
        }
    }

    /*
    Get available time slots from external service
    */
    public function available_slots(Request $request)
    {
        $workshop = $request->query('workshop');
        $date = $request->query('date');
        $base = Workshop::find($workshop)->database;
        $user = Auth::user()->bpro_user;

        // GET request to external API
        $response = Http::withToken(config('api.api_key'))->acceptJson()->get(config('api.api_url').'/api/dynamic/horarios', [
            "fecha" => date("d/m/Y", strtotime($date)),
            "asesor" => $user,
            'base' => $base,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Get the response body as a PHP array/object
            $data = $response->json();

            // Pass the data to view
            return inertia('Orders/Active/Index', [
                'slots' => $data['data'],
            ]);
        }
        else
        {
            // Handle errors
            return null;
        }
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

            //Checks if data exists, if not returns an empty array
            if (empty($data['data'])) {
                return inertia('Orders/Active/Index', [
                    'vehicleData' => [],
                ]);
            }

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
            'vehicle_brand' => ['required'],
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
            'vehicle_brand_id' => $request['vehicle_brand'],
            'service_type_id' => $request['service_type'],
            'service_requested_date' => $request['service_date'],
            'service_location_id' => $request['service_location'],
            'service_description' => $request['service_description'],
            'status' => OrderStatus::REQUESTED,
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
            $order->status = OrderStatus::ENTERED; //Set to ENTERED status (received in workshop)
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
            'horaCita' => $request['time'],
            'idPersona' => $dependency->customer_number,
            'modelo' => $order->vehicle_model,
            'placas' => $order->vehicle_plate,
            'serie' => $order->vehicle_vin,
            'comentarios' => $order->service_description,
            'descripcionTrabajo' => $order->service_type,

            'marca' => $order->vehicle_brand_id,
            'descripcion' => $order->vehicle_description,
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
            $order->status = OrderStatus::SCHEDULED; //Set to SCHEDULED status
            $order->save();

            //TODO - Write log

            return to_route('orders.active')->with(['message' => 'stored', 'cit_id_cita' => $appointment]);
        }

        return response()->json([
            'message' => 'Request failed!',
            'error' => $response->body(), // Get the raw response body
        ], $response->status());
    }

    /*
    Check the current status of the order from an external server
    */
    public function check_current_status($order)
    {
        if (!$order || !$order->appointmentWorkshop || !$order->appointment) {
            return;
        }

        // GET request to external API
        $response = Http::withToken(config('api.api_key'))->acceptJson()->get(config('api.api_url').'/api/dynamic/cita', [
            'base' => $order->getRelation('appointmentWorkshop')->database,
            'cita' => $order->appointment
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            $this->updateOrderFromAPI($order, $response->json());
        }
    }

    /**
     * function to update an order from external API response data
     */
    private function updateOrderFromAPI(Order $order, array $responseData): void
    {
        if (empty($responseData['data'])) {
            return;
        }

        $externalData = $responseData['data'][0];

        if (!empty($externalData['ORDEN'])) 
        {
            $order->service_order = $externalData['ORDEN'];
            $order->service_order_date = $externalData['fecha_orden']; // date("Y-m-d", strtotime($externalData['fecha_orden']));
            $order->service_order_status = $externalData['status_orden'];
            $order->service_order_cone = $externalData['cono'];
            $order->service_order_mileage = $externalData['kilometraje'];
            $order->service_order_user = $externalData['id_asesor'];
            $order->service_order_user_name = $externalData['asesor'];
            
            if ($order->status->value < OrderStatus::ENTERED->value) {
                $order->status = OrderStatus::ENTERED;
            }

            $order->save();
        }
    }
}
