<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->paginate(20);
        return view('admin.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'active' => 'nullable|boolean',
        ]);
        dd($data);

        $s = Service::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'active' => isset($data['active']) ? boolval($data['active']) : true,
        ]);

        return redirect()->route('admin.services.index')->with('success','Service added');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        
            'price' => 'required|numeric|min:0',
            'active' => 'nullable|boolean',
        ]);

        $service->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'active' => isset($data['active']) ? boolval($data['active']) : $service->active,
        ]);

        return redirect()->route('admin.services.index')->with('success','Service updated');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success','Service deleted');
    }

    public function toggleActive(Service $service)
    {
        $service->active = !$service->active;
        $service->save();
        return response()->json(['active' => $service->active]);
    }
}
