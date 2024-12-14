<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class ShippingCompanyController extends Controller
{
    // Display a listing of the shipping companies
    public function index()
    {
        $shippingCompanies = ShippingCompany::all();
        return response()->json($shippingCompanies);
    }

    // Show the form for creating a new shipping company
    public function create()
    {
        // Return view for creating a new shipping company (if using Blade templates)
        return view('shipping_companies.create');
    }

    // Store a newly created shipping company in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:shipping_companies,code|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
        ]);

        $shippingCompany = ShippingCompany::create($request->all());
        return response()->json($shippingCompany, 201);
    }

    // Display the specified shipping company
    public function show($id)
    {
        $shippingCompany = ShippingCompany::findOrFail($id);
        return response()->json($shippingCompany);
    }

    // Show the form for editing the specified shipping company
    public function edit($id)
    {
        $shippingCompany = ShippingCompany::findOrFail($id);
        // Return a view to edit the shipping company (if using Blade templates)
        return view('shipping_companies.edit', compact('shippingCompany'));
    }

    // Update the specified shipping company in the database
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:shipping_companies,code,' . $id . '|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
        ]);

        $shippingCompany = ShippingCompany::findOrFail($id);
        $shippingCompany->update($request->all());

        return response()->json($shippingCompany);
    }

    // Remove the specified shipping company from the database
    public function destroy($id)
    {
        $shippingCompany = ShippingCompany::findOrFail($id);
        $shippingCompany->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
