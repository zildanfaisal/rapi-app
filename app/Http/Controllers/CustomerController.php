<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        // Gunakan get() atau orderBy untuk performa lebih baik
        $customers = Customer::orderBy('nama_customer', 'asc')->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'alamat' => 'required|string|max:255',
            'point' => 'nullable|integer',
        ]);

        Customer::create([
            'nama_customer' => $request->nama_customer,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'point' => $request->point,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }
public function show($id)
{
    $customer = Customer::with([
        'invoices.invoiceItems.product',
        'invoices.invoiceItems.batch' // ⬅️ JIKA BATCH RELASI
    ])->findOrFail($id);

    foreach ($customer->invoices as $inv) {
        $inv->items_json = $inv->invoiceItems->map(function ($item) {
            return [
                'batch_number' => $item->batch->batch_number ?? '-', // ✅ FIX
                'produk'       => $item->product->nama_produk,
                'qty'          => $item->quantity,
                'harga'        => $item->harga,
                'subtotal'     => $item->quantity * $item->harga,
            ];
        });
    }

    return view('customers.show', compact('customer'));
}





    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'alamat' => 'required|string|max:255',
            'point' => 'nullable|integer',
        ]);

        $customer->update([
            'nama_customer' => $request->nama_customer,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'point' => $request->point,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
