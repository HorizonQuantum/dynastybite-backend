<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function monthlyReport(Request $request)
    {
        $bulan = $request->query('bulan');  
        $tahun = $request->query('tahun');  

        $start = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $orders = Order::with('items') 
            ->whereBetween('delivery_date', [$start, $end])
            ->get();

        // Inisialisasi data
        $periods = [
            'periode_1' => [
                'name' => 'Periode 1',
                'total_orders' => 0,
                'total_pcs' => 0,
                'total_revenue' => 0,
            ],
            'periode_2' => [
                'name' => 'Periode 2',
                'total_orders' => 0,
                'total_pcs' => 0,
                'total_revenue' => 0,
            ],
            'periode_3' => [
                'name' => 'Periode 3',
                'total_orders' => 0,
                'total_pcs' => 0,
                'total_revenue' => 0,
            ],
            'custom' => [
                'name' => 'Custom Order',
                'total_orders' => 0,
                'total_pcs' => 0,
                'total_revenue' => 0,
            ],
        ];

        foreach ($orders as $Order) {
            $type = $Order->type_order_id;

            if ($type == 1) {
                $key = 'custom';
            } else {
                $hari = Carbon::parse($Order->delivery_date)->day;
                if ($hari <= 10) {
                    $key = 'periode_1';
                } elseif ($hari <= 20) {
                    $key = 'periode_2';
                } else {
                    $key = 'periode_3';
                }
            }

            $periods[$key]['total_orders'] += 1;
            $periods[$key]['total_revenue'] += $Order->total_price;

            foreach ($Order->items as $item) {
                $periods[$key]['total_pcs'] += $item->pcs * $item->quantity;
            }
        }

        return response()->json([
            'month' => $start->translatedFormat('F Y'),
            'periods' => array_values($periods)
        ]);
    }
}
