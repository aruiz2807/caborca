<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Dependency;
use App\Models\Location;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
            OrderStatus::ENTERED,
        ])->count();

        return Inertia::render('Home', [
            'totalOrders' => $totalOrders,
            'attendedOrders' => $attendedOrders,
            'pendingOrders' => $pendingOrders,
        ]);
    }

    /*
    Get orders for dashboard indicators dynamically
    */
    public function dashboard_records(Request $request)
    {
        $request->validate([
            'filter' => 'required|in:all,attended,pending',
        ]);

        $currentYear = now()->year;
        $query = Order::with(['dependency', 'serviceType', 'serviceLocation'])
            ->whereYear('service_requested_date', $currentYear);

        if ($request->filter === 'attended') {
            $query->where('status', OrderStatus::FINISHED);
        } elseif ($request->filter === 'pending') {
            $query->whereIn('status', [
                OrderStatus::REQUESTED,
                OrderStatus::SCHEDULED,
                OrderStatus::ENTERED,
            ]);
        }

        return response()->json([
            'orders' => $query->get(),
        ]);
    }

    /*
    Get all active orders
    */
    public function active()
    {
        $user = Auth::user();

        // Fetch orders where status is not finished
        $query = Order::with(['dependency', 'serviceType', 'serviceLocation', 'appointmentWorkshop', 'events.user'])
            ->where('status', '!=', OrderStatus::FINISHED);

        // If the current user is not an admin, filter by their assigned dependency and/or workshop location (if advisor)
        if (! $user->hasAnyRole(['Admin', 'Super-Admin'])) {
            $query->where(function ($subQuery) use ($user) {
                // Keep the current filter by dependency
                $subQuery->whereHas('dependency', function ($q) use ($user) {
                    $q->where('advisor_id', $user->id)->orWhere('user_id', $user->id);
                });

                // Plus, if they are an advisor, also include orders in locations of their assigned workshops
                if ($user->type === 'A') {
                    $workshopLocationIds = Workshop::whereHas('advisors', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })->pluck('location_id');

                    if ($workshopLocationIds->isNotEmpty()) {
                        $subQuery->orWhereIn('service_location_id', $workshopLocationIds);
                    }
                }
            });
        }

        $orders = $query->get();

        // Refresh current status for orders that already have an appointment.
        $ordersToCheck = $orders->filter(fn ($order) => $order->appointment && $order->appointmentWorkshop);

        if ($ordersToCheck->isNotEmpty()) {
            foreach ($ordersToCheck as $order) {
                $this->check_current_status($order);
            }

            $orders = $query->get();
        }

        $services = Service::all(['id', 'name']);
        $locations = Location::all(['id', 'name']);
        $workshops = Workshop::all(['id', 'name']);
        $brands = Cache::get('orders.brands', []);

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
    Get brands for the active orders form
    */
    public function brandsData()
    {
        return response()->json([
            'brands' => $this->brands(),
        ]);
    }

    /*
    Get all orders
    */
    public function archive(Request $request)
    {
        $query = Order::with(['dependency', 'serviceType', 'serviceLocation', 'appointmentWorkshop', 'events.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vehicle_dependency')) {
            $query->where('vehicle_dependency_id', $request->vehicle_dependency);
        }

        if ($request->filled('service_location')) {
            $query->where('service_location_id', $request->service_location);
        }

        if ($request->filled('order_date_from')) {
            $query->whereDate('service_requested_date', '>=', $request->order_date_from);
        }

        if ($request->filled('order_date_to')) {
            $query->whereDate('service_requested_date', '<=', $request->order_date_to);
        }

        $orders = $query->get();
        $dependencies = Dependency::all(['id', 'name']);
        $locations = Location::all(['id', 'name']);

        return Inertia::render('Orders/Archive/Index', [
            'orders' => $orders,
            'dependencies' => $dependencies,
            'locations' => $locations,
            'filters' => $request->only(['status', 'vehicle_dependency', 'service_location', 'order_date_from', 'order_date_to']),
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
        $cacheKey = 'orders.brands';
        $cachedBrands = Cache::get($cacheKey, []);

        // GET request to external API
        try {
            $response = Http::connectTimeout(3)
                ->timeout(5)
                ->withToken(config('api.api_key'))
                ->acceptJson()
                ->get(config('api.api_url').'/api/dynamic/marcas', [
                    'base' => 'CHRCaborca_TecniHillo', // must correct, because the gob user does not have a workshop assigned
                ]);

            // Check if the request was successful
            if ($response->successful()) {
                // Get the response body as a PHP array/object
                $data = $response->json();
                $brands = data_get($data, 'data', []);

                if (is_array($brands)) {
                    Cache::put($cacheKey, $brands, now()->addDay());

                    return $brands;
                }
            }
        } catch (\Throwable $throwable) {
            Log::warning('Unable to load brands for orders page', [
                'message' => $throwable->getMessage(),
            ]);
        }

        return $cachedBrands;
    }

    /*
    Get available time slots from external service
    */
    public function available_slots(Request $request)
    {
        $workshop = $request->query('workshop');
        $date = $request->query('date');
        $workshopModel = Workshop::with('advisors:id,bpro_user')->find($workshop);

        if (! $workshopModel) {
            return response()->json([
                'message' => 'Workshop not found.',
                'slots' => [],
            ], 404);
        }

        $advisorCode = $this->resolveAdvisorCode($request->user(), $workshopModel);

        if (! $advisorCode) {
            return response()->json([
                'message' => 'No advisor code configured for this user or workshop.',
                'slots' => [],
            ], 422);
        }

        // GET request to external API
        $response = Http::withToken(config('api.api_key'))
            ->acceptJson()
            ->withBody(json_encode([
                'fecha' => date('d/m/Y', strtotime($date)),
                'asesor' => $advisorCode,
                'base' => $workshopModel->database,
            ], JSON_THROW_ON_ERROR), 'application/json')
            ->send('GET', config('api.api_url').'/api/dynamic/horarios');

        // Check if the request was successful
        if ($response->successful()) {
            // Get the response body as a PHP array/object
            $data = $response->json();

            return response()->json([
                'slots' => $data['data'] ?? [],
            ]);
        } else {
            // Handle errors
            return response()->json([
                'message' => 'Request failed!',
                'error' => $response->body(),
                'slots' => [],
            ], $response->status());
        }
    }

    private function resolveAdvisorCode(?User $user = null, ?Workshop $workshop = null): ?string
    {
        $advisorCode = trim((string) ($user?->bpro_user ?? ''));

        if ($advisorCode !== '') {
            return $advisorCode;
        }

        if (! $workshop) {
            return null;
        }

        $advisorCode = trim((string) $workshop->advisors()
            ->whereNotNull('bpro_user')
            ->orderBy('users.id')
            ->value('bpro_user'));

        return $advisorCode !== '' ? $advisorCode : null;
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

            // Checks if data exists, if not returns an empty array
            if (empty($data['data'])) {
                return inertia('Orders/Active/Index', [
                    'vehicleData' => [],
                ]);
            }

            // Save client data if doesnt exist on dependencies table
            $this->store_dependency($data['data'][0]);

            // Pass the data to view
            return inertia('Orders/Active/Index', [
                'vehicleData' => $data['data'],
            ]);
        } else {
            // Handle errors
            return back()->with('error', 'Could not retrieve data from the external API.');
        }
    }

    /*
    Store dependecy if doesnt exist on dependencies table
    */
    private function store_dependency($data)
    {
        if (! Dependency::where('customer_number', $data['idClient'])->exists()) {
            Dependency::create([
                'name' => $data['client'],
                'customer_number' => $data['idClient'],
                'location_id' => 1, // TODO remove harcoded id
                'user_id' => Auth::id(),
            ]);
        }
    }

    /*
    Save order data
    */
    public function store(Request $request)
    {
        abort_if(! $request->user()->can('create-order'), 403);

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

        // check if customer number is registered on the dependencies table
        $dependency = Dependency::select(['id', 'name', 'advisor_id'])->where('customer_number', $request['vehicle_dependency_id'])->first();

        // if vehicle dependency is not present, search form the dependency associated to the user
        if (! $dependency) {
            // Get the dependency associated with the user requesting the order
            $dependency = Dependency::select(['id', 'name', 'advisor_id'])->where('user_id', $request->user()->id)->first();
        }

        if (! $dependency) {
            return back()->with('error', 'No se encontró una dependencia asociada. Busque el vehículo para autocompletar la dependencia o asigne una al usuario.');
        }

        $order = Order::create([
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

        \App\Facades\OrderEvent::log($order, 'Solicitud creada');

        // Immediately transition to PARTS for checking inventory
        $order->status = OrderStatus::PARTS;
        $order->save();
        \App\Facades\OrderEvent::log($order, 'Validación de refacciones', 'Enviado para revisión de refacciones.');

        if ($dependency && $dependency->advisor_id) {
            \App\Facades\Message::send(
                $dependency->advisor_id,
                'Nueva Orden: '.$order->purchase_order,
                'Se ha creado una nueva orden para la dependencia '.$dependency->name.'. No. Económico: '.$order->economic_number.'. Por favor, revise el inventario de refacciones.'
            );
        }

        return to_route('orders.active')->with('message', 'stored');
    }

    public function update_parts(Request $request, $order_id)
    {
        abort_if(! $request->user()->can('update-order-parts'), 403);

        $order = Order::findOrFail($order_id);

        $request->merge([
            'parts_available' => filter_var($request->parts_available, FILTER_VALIDATE_BOOLEAN),
        ]);

        Validator::make($request->all(), [
            'parts_available' => ['required', 'boolean'],
            'parts_arrival_date' => ['nullable', 'required_if:parts_available,false', 'date'],
        ])->validate();

        $order->parts_available = $request->parts_available;
        $order->parts_arrival_date = $request->parts_arrival_date;

        if ($request->parts_available) {
            $order->status = OrderStatus::PARTS_AVAILABLE;
            \App\Facades\OrderEvent::log($order, 'Refacciones', 'Las refacciones están disponibles.');

            if ($order->dependency && $order->dependency->advisor_id) {
                \App\Facades\Message::send(
                    $order->dependency->advisor_id,
                    'Refacciones disponibles para Orden: '.$order->purchase_order,
                    'Las refacciones para la orden '.$order->purchase_order.' (No. Económico: '.$order->economic_number.') ya se encuentran disponibles. La orden está lista para ser agendada.'
                );
            }
        } else {
            $formattedDate = \Carbon\Carbon::parse($request->parts_arrival_date)->format('d/m/Y');
            \App\Facades\OrderEvent::log($order, 'Refacciones', "Refacciones llegarán el {$formattedDate}");

            if ($order->dependency && $order->dependency->user_id) {
                \App\Facades\Message::send(
                    $order->dependency->user_id,
                    'Refacciones para Orden: '.$order->purchase_order,
                    'Las refacciones para su orden '.$order->purchase_order.' (No. Económico: '.$order->economic_number.') llegarán el '.$formattedDate.'.'
                );
            }
        }

        $order->save();

        return to_route('orders.active')->with('message', 'updated');
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

        // look up for the order with the appointment number and the workshop id
        $workshop = Workshop::where('database', $request['service_order_workshop'])->first();

        $order = Order::where([
            ['appointment', $request['appointment']],
            ['appointment_workshop_id', $workshop->id],
        ])->first();

        if ($order) {
            $order->service_order = $request['service_order'];
            $order->service_order_date = $request['service_order_date'];
            $order->service_order_status = $request['service_order_status'];
            $order->service_order_cone = $request['service_order_cono'];
            $order->service_order_mileage = $request['service_order_kilometraje'];
            $order->service_order_user = $request['service_order_user'];
            $order->service_order_workshop_id = $workshop->id;
            $order->status = OrderStatus::ENTERED; // Set to ENTERED status (received in workshop)
            $order->save();

            return response()->json([
                'message' => 'Successful request!',
                'info' => 'The order was updated succesfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Request failed!',
                'error' => 'The order with especified appointment number and workshop does not exist',
            ], 404);
        }
    }

    public function schedule(Request $request, $order_id)
    {
        abort_if(! $request->user()->can('create-appointment'), 403);

        $order = Order::find($order_id);
        $dependency = Dependency::find($order->vehicle_dependency_id);
        $workshop = Workshop::find($request['workshop']);

        // POST request to external API
        $response = Http::withToken(config('api.api_key'))->acceptJson()->post(config('api.api_url').'/api/dynamic/cita', [
            'base' => $workshop->database,
            'idasesor' => $this->resolveAdvisorCode($request->user(), $workshop),
            'fechaCita' => date('d/m/Y', strtotime($request['date'])), // $request['date'],
            'horaCita' => $request['time'],
            'idPersona' => $dependency->customer_number,
            'modelo' => $order->vehicle_model,
            'placas' => $order->vehicle_plate,
            'serie' => $order->vehicle_vin,
            'comentarios' => $order->service_description,
            'descripcionTrabajo' => $order->serviceType->name,
            'marca' => $order->vehicle_brand_id,
            'descripcion' => $order->vehicle_description,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Get the response body as a PHP array/object
            $response_data = $response->json();
            $appointment = $response_data['data'][0]['CIT_IDCITA'] ?? null;

            // Update order record in database
            $order->appointment = $appointment;
            $order->appointment_date = $request['date'];
            $order->appointment_time = $request['time'];
            $order->appointment_workshop_id = $request['workshop'];
            $order->status = OrderStatus::SCHEDULED; // Set to SCHEDULED status
            $order->save();

            \App\Facades\OrderEvent::log($order, 'Cita agendada', "Cita para el día {$request['date']} en {$workshop->name}");

            return to_route('orders.active')->with(['message' => 'stored', 'cit_id_cita' => $appointment]);
        }

        return response()->json([
            'message' => 'Request failed!',
            'error' => $response->body(), // Get the raw response body
        ], $response->status());
    }

    public function cancel_appointment(Request $request, $order_id)
    {
        abort_if(! $request->user()->can('cancel-appointment'), 403);

        Validator::make($request->input(), [
            'motive' => ['required', 'string', 'max:255'],
        ])->validate();

        $order = Order::find($order_id);
        $workshop = Workshop::find($order->appointment_workshop_id);

        // DELETE request to external API
        $response = Http::withToken(config('api.api_key'))->acceptJson()->delete(config('api.api_url').'/api/dynamic/cita', [
            'base' => $workshop->database,
            'idcita' => $order->appointment,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Update order record in database
            $order->appointment = null;
            $order->appointment_date = null;
            $order->appointment_workshop_id = null;
            $order->status = OrderStatus::REQUESTED; // Reset to REQUESTED status
            $order->save();

            \App\Facades\OrderEvent::log($order, 'Cita cancelada', $request['motive']);

            return to_route('orders.active')->with('message', 'cancelled');
        }

        return response()->json([
            'message' => 'Request failed!',
            'error' => $response->body(),
        ], $response->status());
    }

    /*
    Check the current status of the order from an external server
    */
    public function check_current_status($order)
    {
        if (! $order || ! $order->appointmentWorkshop || ! $order->appointment) {
            return;
        }

        try {
            // GET request to external API
            $response = Http::connectTimeout(3)
                ->timeout(5)
                ->withToken(config('api.api_key'))
                ->acceptJson()
                ->get(config('api.api_url').'/api/dynamic/cita', [
                    'base' => $order->getRelation('appointmentWorkshop')->database,
                    'cita' => $order->appointment,
                ]);

            // Check if the request was successful
            if ($response->successful()) {
                $payload = $response->json();

                if (is_array($payload)) {
                    $this->updateOrderFromAPI($order, $payload);
                }
            }
        } catch (\Throwable $throwable) {
            Log::warning('Unable to refresh order status', [
                'order_id' => $order->id,
                'message' => $throwable->getMessage(),
            ]);
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

        if (! empty($externalData['ORDEN'])) {
            $order->service_order = $externalData['ORDEN'];
            $order->service_order_date = $externalData['fecha_orden']; // date("Y-m-d", strtotime($externalData['fecha_orden']));
            $externalStatus = strtoupper(trim((string) data_get($externalData, 'status_orden', '')));
            $order->service_order_status = $externalStatus;
            $order->service_order_cone = $externalData['cono'];
            $order->service_order_mileage = $externalData['kilometraje'];
            $order->service_order_user = $externalData['id_asesor'];
            $order->service_order_user_name = $externalData['asesor'];

            if ($order->status->value < OrderStatus::ENTERED->value) {
                $order->status = OrderStatus::ENTERED;
            }

            if (in_array($externalStatus, ['CERRADA', 'CERRADO'], true)) {
                $order->status = OrderStatus::FINISHED;
            }

            $order->save();
        }
    }
}
