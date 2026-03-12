<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:6',
            'nomor_telp' => 'required|max:20',
            'no_ktp' => 'required|unique:customers,no_ktp',
        ]);

        Customer::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nomor_telp' => $request->nomor_telp,
            'no_ktp' => $request->no_ktp,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil ditambahkan');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'nomor_telp' => 'required|max:20',
            'no_ktp' => 'required|unique:customers,no_ktp,' . $customer->id,
        ]);

        $customer->update($request->only('nama', 'email', 'nomor_telp', 'no_ktp'));

        return redirect()->route('admin.customers.index')
            ->with('success', 'Data customer berhasil diperbarui');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return back()->with('success', 'Customer berhasil dihapus');
    }
}
