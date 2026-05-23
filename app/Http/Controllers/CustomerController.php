<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('identification_id', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        }

        $customers = $query->latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identification_id' => 'required|string|unique:customers',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'references' => 'nullable|array',
            'notes' => 'nullable|string',
            'guarantee' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('customers', 'public');
            $validated['photo_path'] = $path;
        }

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Cliente creado correctamente.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identification_id' => 'required|string|unique:customers,identification_id,' . $customer->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'references' => 'nullable|array',
            'notes' => 'nullable|string',
            'guarantee' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($customer->photo_path) {
                Storage::disk('public')->delete($customer->photo_path);
            }
            $path = $request->file('photo')->store('customers', 'public');
            $validated['photo_path'] = $path;
        }

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Cliente actualizado correctamente.');
    }
}
