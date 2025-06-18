<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-4xl text-black leading-tight">
            {{ __('Laporan Pelanggaran SOP') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Daftar Pesanan Melebihi 30 Menit') }}</h3>

                    <div class="mb-6 flex flex-wrap items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.sop-violations.index', ['filter' => 'today']) }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $activeFilter == 'today' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Hari Ini</a>
                            <a href="{{ route('admin.sop-violations.index', ['filter' => 'week']) }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $activeFilter == 'week' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Minggu Ini</a>
                            <a href="{{ route('admin.sop-violations.index', ['filter' => 'month']) }}" class="px-4 py-2 text-sm font-medium rounded-md {{ $activeFilter == 'month' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Bulan Ini</a>
                        </div>
                        <form method="GET" action="{{ route('admin.sop-violations.index') }}" class="flex items-center space-x-2">
                            <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm border-gray-300 rounded-md shadow-sm">
                            <span>-</span>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm border-gray-300 rounded-md shadow-sm">
                            <button type="submit" class="px-4 py-2 text-sm font-medium rounded-md bg-green-500 text-white hover:bg-green-600">Filter</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamar</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.Check</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesanan</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Order</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Checklist Dapur</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Mulai Antar</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Diterima</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi Total</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">By (Cook)</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">By (Delivery)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($violations as $order)
                                    <tr class="align-top">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->room?->roomType?->name }} - {{ $order->room?->room_number }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->order_id }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                                            @foreach($order->orderItems as $item)
                                                <div>{{ $item->menuItem?->item_name }} x{{ $item->quantity }}</div>
                                            @endforeach
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->order_time ? $order->order_time->format('H:i:s') : '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->kitchen_marked_ready_time ? $order->kitchen_marked_ready_time->format('H:i:s') : '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->delivery_assignment_time ? $order->delivery_assignment_time->format('H:i:s') : '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->delivery_actual_time ? $order->delivery_actual_time->format('H:i:s') : '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                                            @if ($order->order_time && $order->delivery_actual_time)
                                                {{ $order->delivery_actual_time->diff($order->order_time)->format('%I:%S') }} Menit
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $order->kitchenStaff?->fullname ?? 'N/A' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $order->deliveryStaff?->fullname ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-6 py-10 text-center text-gray-500">Tidak ada data pelanggaran SOP untuk periode yang dipilih.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4">
                        {{ $violations->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>