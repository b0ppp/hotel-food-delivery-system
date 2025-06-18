<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class SopViolationController extends Controller
{
    public function index(Request $request): View
    {
        // Query dasar untuk mengambil pelanggaran SOP
        $query = Order::where('sop_violation_flag', true)
                      ->with([
                          'room.roomType',
                          'orderItems.menuItem',
                          'kitchenStaff',
                          'deliveryStaff'
                      ]);

        // Ambil filter dari request
        $filter = $request->query('filter', 'today'); // Default ke 'today'
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($startDate && $endDate) {
            // Filter custom range
            $query->whereBetween('order_time', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
            $filter = 'custom';
        } else {
            // Filter preset (today, week, month)
            switch ($filter) {
                case 'week':
                    $query->whereBetween('order_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('order_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'today':
                default:
                    $query->whereDate('order_time', Carbon::today());
                    break;
            }
        }
        
        $violations = $query->latest('order_time')->paginate(15);

        return view('admin.sop-violations.index', [
            'violations' => $violations,
            'activeFilter' => $filter,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}