<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class PatientServicesController extends Controller
{
    public function index(Request $request)
    {
        $q = Service::query();

        if ($request->filled('q')) {
            $term = $request->q;
            $q->where(function($s) use ($term) {
                $s->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('min_price')) {
            $q->where('price', '>=', floatval($request->min_price));
        }
        if ($request->filled('max_price')) {
            $q->where('price', '<=', floatval($request->max_price));
        }

        // show active services by default
        $q->where('active', 1);

        $services = $q->orderBy('name')->paginate(12)->withQueryString();

        return view('patient.services.index', compact('services'));
    }
}
