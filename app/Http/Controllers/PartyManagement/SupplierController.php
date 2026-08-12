<?php

namespace App\Http\Controllers\PartyManagement;

use App\Http\Controllers\Controller;

/**
 * @deprecated Unrouted legacy controller. Suppliers are managed via Party (is_vendor) flows.
 * Kept only for reference; do not register new routes against this controller.
 */
class SupplierController extends Controller
{
    public function index()
    {
        abort(410, 'SupplierController is deprecated. Use Party vendor flows.');
    }

    public function create()
    {
        abort(410, 'SupplierController is deprecated. Use Party vendor flows.');
    }

    public function store()
    {
        abort(410, 'SupplierController is deprecated. Use Party vendor flows.');
    }

    public function show($id)
    {
        abort(410, 'SupplierController is deprecated. Use Party vendor flows.');
    }

    public function edit($id)
    {
        abort(410, 'SupplierController is deprecated. Use Party vendor flows.');
    }

    public function destroy($id)
    {
        abort(410, 'SupplierController is deprecated. Use Party vendor flows.');
    }
}
