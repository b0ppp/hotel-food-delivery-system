<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Events\OrderStatusUpdated; // <-- Tambahkan ini


class DashboardController extends Controller
{
    public function index(): View
    {
        $relevantStatuses = ['Diproses', 'Siap Dihantar', 'Dihantarkan', 'Diterima'];

        $orders = Order::whereIn('order_status', $relevantStatuses)
                        ->whereDate('created_at', today())
                        ->with(['room.roomType', 'orderItems.menuItem', 'kitchenStaff', 'deliveryStaff'])
                        ->orderByRaw("FIELD(order_status, 'Siap Dihantar', 'Dihantarkan', 'Diproses', 'Diterima')")
                        ->latest('order_time')
                        ->get();

        $cooks = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Staf Dapur');
        })->where('status', 'Aktif')->select('user_id', 'fullname')->get();

        $deliveryStaffs = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Staf Antar');
        })->where('status', 'Aktif')->select('user_id', 'fullname')->get();

        return view('delivery.dashboard', compact('orders', 'cooks', 'deliveryStaffs'));
    }

    public function assignStaff(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
            'type'    => 'required|string|in:cook,delivery',
        ]);

        if ($validated['type'] === 'cook') {
            $order->kitchen_staff_user_id = $validated['user_id'];
        } 
        elseif ($validated['type'] === 'delivery') {
            $order->delivery_staff_user_id = $validated['user_id'];
            if($order->order_status === 'Siap Dihantar') {
                $order->order_status = 'Dihantarkan';
                $order->delivery_assignment_time = now();
            }
        }

        $order->save();

        OrderStatusUpdated::dispatch($order); // <-- Panggil event

        return response()->json($order->load(['room.roomType', 'orderItems.menuItem', 'kitchenStaff', 'deliveryStaff']));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        // 1. Perluas validasi untuk mengizinkan status mundur
        $validated = $request->validate([
            'status' => 'required|string|in:Dihantarkan,Diterima,Siap Dihantar',
        ]);

        $newStatus = $validated['status'];
        $currentStatus = $order->order_status;

        // 2. Logika utama menggunakan switch untuk menangani setiap transisi status
        switch ($newStatus) {
            case 'Diterima':
                if ($currentStatus !== 'Dihantarkan') {
                    return response()->json(['message' => 'Hanya pesanan yang sedang DIHANTARKAN yang bisa diselesaikan.'], 422);
                }
                
                $order->delivery_actual_time = now();

                $orderTime = $order->order_time;
                $deliveryTime = $order->delivery_actual_time;

                if ($orderTime && $deliveryTime) {
                    // --- PERBAIKAN FINAL: Gunakan metode getTimestamp() untuk perhitungan paling andal ---
                    
                    // Konversi kedua waktu ke Unix Timestamp (total detik)
                    $orderTimestamp = $orderTime->getTimestamp();
                    $deliveryTimestamp = $deliveryTime->getTimestamp();

                    // Hitung selisihnya dalam detik
                    $durationInSeconds = $deliveryTimestamp - $orderTimestamp;

                    // Bandingkan dengan 1800 detik (30 menit)
                    if ($durationInSeconds > 1800) {
                        $order->sop_violation_flag = true;
                        $order->sop_violation_notes = 'Durasi total dari pemesanan hingga diterima melebihi 30 menit.';
                    } else {
                        $order->sop_violation_flag = false;
                        $order->sop_violation_notes = null; // Kosongkan catatan jika tidak melanggar
                    }
                }
                break;
                
            case 'Dihantarkan':
                // Ini adalah logika untuk membatalkan dari "Diterima" kembali ke "Dihantarkan"
                if ($currentStatus !== 'Diterima') {
                    return response()->json(['message' => 'Hanya pesanan yang sudah DITERIMA yang bisa dibatalkan kembali ke Dihantarkan.'], 422);
                }
                // Kosongkan kembali waktu diterima
                $order->delivery_actual_time = null;
                break;
                
            case 'Siap Dihantar':
                // Ini adalah logika untuk membatalkan dari "Dihantarkan" kembali ke "Siap Dihantar"
                if ($currentStatus !== 'Dihantarkan') {
                    return response()->json(['message' => 'Hanya pesanan yang sedang DIHANTARKAN yang bisa dibatalkan kembali ke Siap Dihantar.'], 422);
                }
                // Kosongkan data pengantaran
                $order->delivery_staff_user_id = null;
                $order->delivery_assignment_time = null;
                $order->delivery_actual_time = null; // Pastikan ini juga null
                break;
        }

        // 3. Set status baru dan simpan
        $order->order_status = $newStatus;
        $order->save();

        // 4. Kirim event agar semua dashboard sinkron
        OrderStatusUpdated::dispatch($order);

        // 5. Kembalikan data order yang sudah ter-update lengkap dengan semua relasinya
        return response()->json($order->load(['room.roomType', 'orderItems.menuItem', 'kitchenStaff', 'deliveryStaff']));
    }
}