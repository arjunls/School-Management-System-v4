<?php

namespace App\Modules\Transportasi\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Transportasi\Models\TransportRoute;
use App\Modules\Transportasi\Models\TransportVehicle;
use App\Modules\Transportasi\Models\TransportStudent;
use Illuminate\Http\Request;

class TransportasiWebController extends Controller
{
    public function index()
    {
        $routeCount = TransportRoute::count();
        $vehicleCount = TransportVehicle::count();
        $activeStudentCount = TransportStudent::where('status', 'active')->count();
        $routes = TransportRoute::withCount('students')->orderBy('name')->get();
        $vehicles = TransportVehicle::withCount('students')->orderBy('name')->get();
        return view('transportasi.index', compact(
            'routeCount', 'vehicleCount', 'activeStudentCount', 'routes', 'vehicles'
        ));
    }

    public function storeRoute(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pickup_point' => 'required|string|max:255',
            'dropoff_point' => 'required|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
        ]);
        TransportRoute::create($d);
        return redirect()->route('transportasi.index')->with('success', 'Rute tersimpan');
    }

    public function updateRoute(Request $r, TransportRoute $route)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pickup_point' => 'required|string|max:255',
            'dropoff_point' => 'required|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
        ]);
        $route->update($d);
        return redirect()->route('transportasi.index')->with('success', 'Rute diperbarui');
    }

    public function deleteRoute(TransportRoute $route)
    {
        $route->delete();
        return redirect()->route('transportasi.index')->with('success', 'Rute dihapus');
    }

    public function storeVehicle(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'plate_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'status' => 'required|in:active,maintenance,inactive',
        ]);
        TransportVehicle::create($d);
        return redirect()->route('transportasi.index')->with('success', 'Kendaraan tersimpan');
    }

    public function updateVehicle(Request $r, TransportVehicle $vehicle)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'plate_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'status' => 'required|in:active,maintenance,inactive',
        ]);
        $vehicle->update($d);
        return redirect()->route('transportasi.index')->with('success', 'Kendaraan diperbarui');
    }

    public function deleteVehicle(TransportVehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('transportasi.index')->with('success', 'Kendaraan dihapus');
    }

    public function storeStudent(Request $r)
    {
        $d = $r->validate([
            'route_id' => 'required|exists:transportation_routes,id',
            'vehicle_id' => 'nullable|exists:transportation_vehicles,id',
            'student_id' => 'required|exists:users,id|unique:transportation_students,student_id',
            'pickup_point' => 'required|string|max:255',
            'dropoff_point' => 'required|string|max:255',
            'fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        TransportStudent::create($d);
        return redirect()->route('transportasi.index')->with('success', 'Penempatan siswa tersimpan');
    }

    public function updateStudent(Request $r, TransportStudent $transportStudent)
    {
        $d = $r->validate([
            'route_id' => 'required|exists:transportation_routes,id',
            'vehicle_id' => 'nullable|exists:transportation_vehicles,id',
            'student_id' => 'required|exists:users,id|unique:transportation_students,student_id,' . $transportStudent->id,
            'pickup_point' => 'required|string|max:255',
            'dropoff_point' => 'required|string|max:255',
            'fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        $transportStudent->update($d);
        return redirect()->route('transportasi.index')->with('success', 'Penempatan siswa diperbarui');
    }

    public function deleteStudent(TransportStudent $transportStudent)
    {
        $transportStudent->delete();
        return redirect()->route('transportasi.index')->with('success', 'Penempatan siswa dihapus');
    }
}
