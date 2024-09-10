<?php

namespace App\Http\Controllers;


use App\Http\Resources\productResource;
use App\Models\product;
use Illuminate\Http\Request;

class productController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function products()
    {
        $products = product::all();
        return productResource::collection($products);
    }

    public function update(Request $request, $id)
    {
        $product = product::findorfail($id);
        $product->update($request->all());
        return new productResource($product);
    }
    public function delete($id)
    {
        $product = product::findorfail($id);
        $product->delete();
        return response()->json(['message' => 'product deleted successfully'], 200);
    }
}
