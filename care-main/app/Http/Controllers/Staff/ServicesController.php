<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        $q = Service::query();

        if ($request->filled('q')) {
            $term = $request->get('q');
            $q->where(function($qr) use ($term) {
                $qr->where('name', 'like', "%{$term}%")
                   ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('active') && $request->get('active') === '1') {
            $q->where('active', true);
        }

        $services = $q->orderBy('name')->paginate(20);

        return view('staff.services.index', compact('services'));
    }
}
