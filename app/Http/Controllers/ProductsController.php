<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductsResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index()
    {
        return response()->json(ProductsResource::collection(Product::all()));
    }
}
