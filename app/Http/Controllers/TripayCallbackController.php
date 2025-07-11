<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TripayCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Verifikasi signature untuk keamanan
        $signature = hash_hmac('sha256', $request->getContent(), env('TRIPAY_PRIVATE_KEY'));

        if ($signature !== $request->header('X-Callback-Signature')) {
            Log::warning('Invalid callback signature', [$request->all()]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $event = $request->input('event');
        $data  = $request->input('data');

        // Cek event & status pembayaran
        if ($event === 'payment_status') {
            $reference = $data['reference'];
            $status = $data['status'];

            // Update order kamu berdasarkan reference Tripay
            $order = \App\Models\Order::where('reference', $reference)->first();

            if ($order) {
                $order->status_pembayaran = $status;
                $order->save();
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['message' => 'No action taken']);
    }
}
