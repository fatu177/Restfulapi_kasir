<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function buy(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'total_product' => 'required|integer',
            'product_id' => 'required',
            'bank' => 'required|in:bca,bni'

        ]);
        $product = product::findorfail($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'product not found'], 404);
        }
        if ($product->stock < $request->total_product) {
            return response()->json(['message' => 'stock not enough'], 404);
        }
        try {
            DB::beginTransaction();
            $serverKey = config('midtrans.key');
            $orderid = Str::uuid()->toString();
            $response = Http::withBasicAuth($serverKey, '')
                ->post('https://api.sandbox.midtrans.com/v2/charge', [
                    'payment_type'  => 'bank_transfer',
                    'transaction_details' => [
                        'order_id' => $orderid,
                        'gross_amount' => $request->total_product * $product->price,
                    ],
                    'customer_details' =>[
                        'first_name'=> $request->name,
                        'email'=> $request->email,
                    ],
                    'bank_transfer' => [
                        'bank' => $request->bank
                    ]
                ]);
            if ($response->failed()) {
                return response()->json(['message' => 'payment failed'], 500);
            }
            $result = $response->json();
            if ($result['status_code'] != '201') {
                return response()->json(['message' => $result['status_message']], 500);
            }
            transaction::create([
                'id' => $orderid,
                'user_id' => auth()->user()->id,
                'booking_code' => Str::random(5),
                'product_id' => $request->product_id,
                'total_product' => $request->total_product,
                'total_price' => $product->price * $request->total_product,
                'name' => $request->name,
                'email' => $request->email,

            ]);
            product::findorfail($request->product_id)
                ->update([
                    'stock' => $product->stock - $request->total_product
                ]);


            DB::commit();
            return response()->json($result['va_numbers']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
