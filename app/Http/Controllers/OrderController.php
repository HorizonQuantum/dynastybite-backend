<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        return Order::latest()->get();
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'total_price' => 'required|integer|min:0',
            'note_order' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'type_order_id' => 'required|exists:type_orders,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'product_type_id' => 'required|exists:type_products,id',
            'address' => 'required|string',
            'delivery_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'items.*.total' => 'required|integer|min:0',
            'items.*.pcs' => 'required|integer|min:0',
        ]);

        // Tentukan periode
        if ($validate['type_order_id'] == 2) {
            $deliveryDate = Carbon::parse($request->delivery_date);
            $dayName = $deliveryDate->locale('id')->dayName;

            $periode = match (strtolower($dayName)) {
                'rabu' => 1,
                'jumat' => 2,
                'minggu' => 3,
                default => null,
            };
        } else {
            $periode = 'Custom Order';
        }

        // Cek kuota
        $pcsBaru = collect($validate['items'])->sum('pcs');

        if ($validate['type_order_id'] == 1) {
            $startOfWeek = Carbon::parse($validate['delivery_date'])->startOfWeek();
            $endOfWeek = Carbon::parse($validate['delivery_date'])->endOfWeek();

            $pcsTerpakai = OrderItem::whereHas('order', function ($q) use ($validate, $startOfWeek, $endOfWeek) {
                $q->where('user_id', $validate['user_id'])
                ->where('type_order_id', 1)
                ->whereBetween('delivery_date', [$startOfWeek, $endOfWeek]);
            })->sum('pcs');

            if (($pcsTerpakai + $pcsBaru) > 50) {
                return response()->json([
                    'message' => 'Melebihi kuota maksimal 50 pcs untuk pesanan custom minggu ini.'
                ], 422);
            }

        } elseif ($validate['type_order_id'] == 2) {
            $pcsTerpakai = OrderItem::whereHas('order', function ($q) use ($validate) {
                $q->where('delivery_date', $validate['delivery_date'])
                ->where('type_order_id', 2);
            })->sum('pcs');

            if (($pcsTerpakai + $pcsBaru) > 150) {
                return response()->json([
                    'message' => 'Melebihi kuota maksimal 150 pcs untuk tanggal ini (regular order).'
                ], 422);
            }
        }

        $generatedCode = 'VA' . now()->timestamp . rand(100, 999);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $validate['user_id'],
                'total_price' => $validate['total_price'],
                'note_order' => $validate['note_order'],
                'type_order_id' => $validate['type_order_id'],
                'payment_method_id' => $validate['payment_method_id'],
                'product_type_id' => $validate['product_type_id'],
                'address' => $validate['address'],
                'delivery_date' => $validate['delivery_date'],
                'status_id' => 1,
                'periode' => $periode,
                'payment_code' => $generatedCode,
                'expired_at' => now()->addHour(1)->setTimezone('Asia/Jakarta')->toIso8601String(),
                'is_paid' => false,
            ]);

            foreach ($validate['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                    'pcs' => $item['pcs'],
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Pesanan berhasil disimpan',
                'data' => $order->load(['user', 'items', 'status', 'typeOrder'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan pesanan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Order $order)
    {
        if (!$order->is_paid && now()->gt($order->expired_at)) {
            $order->delete();
            return response()->json(['message' => 'Pesanan sudah kadaluarsa'], 410);
        }

        return response()->json($order->load(['user', 'items', 'status']));
    }

    public function update(Request $request, Order $order)
    {
        $order->update($request->all());
        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => $order,
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json([
            'message' => 'Pesanan berhasil dihapus',
        ]);
    }

    public function destroyorder($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order berhasil dihapus'], 200);
    }


    public function ordersPerDay()
    {
        $orders = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('
                DATE(orders.created_at) as date,
                orders.type_order_id,
                SUM(order_items.quantity * order_items.pcs) as total_terjual
            ')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->groupBy('date', 'orders.type_order_id')
            ->orderBy('date')
            ->get();

        // Tambahkan nama periode berdasarkan type_order_id
        $orders->transform(function ($item) {
            $item->periode = match ((int) $item->type_order_id) {
                1 => 'custom',
                2 => 'periode_1',
                3 => 'periode_2',
                4 => 'periode_3',
                default => 'unknown'
            };

            unset($item->type_order_id);

            return $item;
        });

        return response()->json($orders);
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|integer|exists:order_statuses,id',
        ]);

        $order = Order::findOrFail($id);

        if ($order->status_id === 5) {
            return response()->json(['message' => 'Pesanan telah selesai dan status tidak dapat diubah'], 400);
        }

        $order->status_id = $request->status_id;
        $order->save();

        return response()->json(['message' => 'Status pesanan berhasil diperbarui.']);
    }

    public function new_print()
    {
        $tanggalAwal = now()->startOfMonth()->format('Y-m-d');
        $tanggalAkhir = now()->endOfMonth()->format('Y-m-d');

        $orders = order::with(['user', 'typeOrder', 'status'])
                    ->where('status_id', 5)
                    ->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
                    ->get();

        return view('laporan.penjualan', compact('orders', 'tanggalAwal', 'tanggalAkhir'));
    }

    public function print()
    {
        $periode1 = order::with(['user', 'typeOrder', 'status'])
                    ->where('status_id', 5 )
                    ->where('periode', 1)
                    ->get();

        $periode2 = order::with(['user', 'typeOrder', 'status'])
                    ->where('status_id', 5 )
                    ->where('periode', 2)
                    ->get();

        $periode3 = order::with(['user', 'typeOrder', 'status'])
                    ->where('status_id', 5 )
                    ->where('periode', 3)
                    ->get();

        $periodeco = order::with(['user', 'typeOrder', 'status'])
                    ->where('status_id', 5 )
                    ->where('periode', 'Custom Order')
                    ->get();
                    
        $periode1 = $periode1->filter(function ($order) {
                        return $order->user && $order->typeOrder && $order->status;
                    });

        $periode2 = $periode2->filter(function ($order) {
                        return $order->user && $order->typeOrder && $order->status;
                    });

        $periode3 = $periode3->filter(function ($order) {
                        return $order->user && $order->typeOrder && $order->status;
                    });

        return view('laporan.penjualan', compact('periode1', 'periode2', 'periode3', 'periodeco'));
    }

    public function quota(Request $request)
    {
        $tanggal = $request->query('tanggal');

        $totalPcs = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.delivery_date', $tanggal)
            ->sum(DB::raw('order_items.quantity * order_items.pcs'));

        $kuotaMaks = 150;
        $sisaKuota = $kuotaMaks - $totalPcs;

        return response()->json([
            'total_pcs_terpakai' => $totalPcs,
            'sisa_kuota' => max($sisaKuota, 0),
        ]);
    }

}
