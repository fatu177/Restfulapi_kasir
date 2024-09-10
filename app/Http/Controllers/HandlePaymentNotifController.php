<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HandlePaymentNotifController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $payload = $request->all();

        Log::info('incoming-midtrans',[
            'payload' => $payload,
        ]);
        $orderId = $payload['order_id'];
        $status_code = $payload['status_code'];
        $total_price = $payload['gross_amount'];

        $reqSignature = $payload['signature_key'];

        $signature = hash('sha512', $orderId.$status_code.$total_price.config('midtrans.key'));

        if ($reqSignature != $signature) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }
        $transactionStatus = $payload['transaction_status'];
        $order = transaction::findorfail($orderId);
        if ($transactionStatus == 'settlement') {
            $order->update([
                'status' => 'success',
            ]);
        } else if($transactionStatus == 'expire'){
            $order->update([
                'status' => 'expired',
                ]);
                $product = product::findorfail($order->product_id);

                $product->update([
                    'stock' => $order->total_product + $product->stock,
                    ]);
        }
    }
}
