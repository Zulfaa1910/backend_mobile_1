<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input data
        $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        // Simpan data ke dalam tabel sales
        $sale = new Sale();
        $sale->product_name = $request->product_name;
        $sale->quantity = $request->quantity;
        $sale->price = $request->price;
        $sale->save();

        return response()->json([
            'message' => 'Sale created successfully',
            'data' => $sale
        ], 201);
    }
}
