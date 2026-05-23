<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function global(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json([]);
        }

        $customers = Customer::where('name', 'like', "%{$query}%")
            ->orWhere('identification_id', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'name', 'identification_id']);

        $loans = Loan::with('customer')
            ->whereHas('customer', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhere('id', 'like', "%{$query}%")
            ->take(5)
            ->get();

        $results = [];

        foreach ($customers as $customer) {
            $results[] = [
                'type' => 'customer',
                'title' => $customer->name,
                'subtitle' => 'Cliente — ' . $customer->identification_id,
                'url' => route('customers.show', $customer),
                'icon' => 'person'
            ];
        }

        foreach ($loans as $loan) {
            $results[] = [
                'type' => 'loan',
                'title' => 'Préstamo #' . str_pad($loan->id, 5, '0', STR_PAD_LEFT),
                'subtitle' => $loan->customer->name . ' — $' . number_format((float) $loan->amount, 2),
                'url' => route('loans.show', $loan),
                'icon' => 'payments'
            ];
        }

        return response()->json($results);
    }
}
