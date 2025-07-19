<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        return Country::all();
    }

    public function show($id)
    {
        return Country::findOrFail($id);
    }

    public function store(Request $request)
    {
        $country = Country::create($request->all());
        return response()->json($country, 201);
    }

    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);
        $country->update($request->all());
        return response()->json($country);
    }

    public function destroy($id)
    {
        Country::destroy($id);
        return response()->json(null, 204);
    }

    public function create()
    {
        // Optionally return a view or form for creating
    }

    public function edit($id)
    {
        // Optionally return a view or form for editing
    }
} 