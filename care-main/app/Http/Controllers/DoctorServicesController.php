<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class DoctorServicesController extends Controller
{
    public function index(Request $request)
    {
        $q = Service::query();

        if ($request->filled('q')) {
            $q->where('name', 'like', '%'.$request->q.'%');
        }

        if ($request->filled('active_only')) {
            $q->where('active', 1);
        }

        $services = $q->orderBy('name')->paginate(20)->withQueryString();

        return view('doctor.services.index', compact('services'));
    }
}
