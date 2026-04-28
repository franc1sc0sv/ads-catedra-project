<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function show(Request $request, Customer $customer = null)
    {
        $search = $request->get('search');
        $customers = [];

        // Si no hay un cliente seleccionado en la URL
        if (!$customer || !$customer->exists) {
            $customers = Customer::when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('identification', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();
            
            $customer = null;
        } else {
            // Si hay uno seleccionado, cargamos sus ventas con los medicamentos
            $customer->load(['sales' => fn($q) => $q->latest()]);
        }

        return view('salesperson.dashboard.show', compact('customer', 'customers'));
    }
}