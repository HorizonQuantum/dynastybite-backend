<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use Carbon\Carbon;

class QuotaController extends Controller
{
    public function getRegularQuota(Request $request)
    {
        $tanggal = $request->query('tanggal');

        $totalPcs = OrderItem::whereHas('order', function ($q) use ($tanggal) {
            $q->whereDate('delivery_date', $tanggal)
              ->where('type_order_id', 2); // pastikan hanya regular
        })->selectRaw('SUM(quantity * pcs) as total')->value('total') ?? 0;

        return response()->json([
            'max_quota' => 150,
            'used_quota' => $totalPcs,
            'remaining_quota' => max(0, 150 - $totalPcs),
        ]);
    }

    public function getCustomQuota(Request $request)
    {
        $userId = $request->query('user_id');
        $tanggal = Carbon::parse($request->query('tanggal'));

        $startOfWeek = $tanggal->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $tanggal->copy()->endOfWeek(Carbon::SATURDAY);

        $totalPcs = OrderItem::whereHas('order', function ($q) use ($userId, $startOfWeek, $endOfWeek) {
            $q->where('user_id', $userId)
              ->where('type_order_id', 1)
              ->whereBetween('delivery_date', [$startOfWeek, $endOfWeek]);
        })->selectRaw('SUM(quantity * pcs) as total')->value('total') ?? 0;

        return response()->json([
            'max_quota' => 50,
            'used_quota' => $totalPcs,
            'remaining_quota' => max(0, 50 - $totalPcs),
        ]);
    }
}
