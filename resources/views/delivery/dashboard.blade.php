<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-4xl text-black leading-tight">
            Dashboard Pengantaran
        </h2>
    </x-slot>

    {{-- Div utama dengan x-data yang hanya memanggil listener --}}
    <div class="py-8" x-data="deliveryDashboardManager">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Notifikasi --}}
            <div x-show="$store.deliveryOrders.showNotification" 
                 x-transition
                 class="mb-6 p-4 bg-yellow-500 text-white rounded-lg shadow-lg flex justify-between items-center" 
                 style="display: none;">
                <p class="font-bold" x-text="$store.deliveryOrders.notificationMessage"></p>
                <button @click="$store.deliveryOrders.showNotification = false" class="text-2xl font-bold leading-none">&times;</button>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto bg-white p-4 rounded-lg shadow-xl border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamar</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.Check</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesanan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Order</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">By (Cook)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Delivery</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">By (Delivery)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Pesanan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="$store.deliveryOrders.items.length === 0">
                            <tr><td colspan="10" class="px-4 py-10 text-center text-gray-500">Tidak ada pesanan untuk hari ini.</td></tr>
                        </template>

                        {{-- Loop mengambil data dari store global --}}
                        <template x-for="order in $store.deliveryOrders.items" :key="order.order_id">
                            <tr class="align-top" x-data="deliveryOrderRow(order.order_id)">
                                <td class="px-4 py-4 whitespace-nowrap font-medium" x-text="`${order.room.room_type.name} - ${order.room.room_number}`"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-gray-600" x-text="order.order_id"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <template x-for="item in order.order_items" :key="item.order_item_id">
                                        <div x-text="`${item.menu_item.item_name} x${item.quantity}`"></div>
                                    </template>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600" x-text="new Date(order.order_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div x-show="!isEditingCook && order.kitchen_staff" @dblclick="isEditingCook = true" class="cursor-pointer p-1" x-text="order.kitchen_staff ? order.kitchen_staff.fullname : ''"></div>
                                    <div x-show="isEditingCook || !order.kitchen_staff">
                                        <select x-model="selectedCook" @change="assignStaff('cook')" @click.outside="isEditingCook = false" class="text-sm rounded-md border-gray-300">
                                            <option value="">Pilih Koki</option>
                                            <template x-for="cook in $store.deliveryOrders.cooks">
                                                <option :value="cook.user_id" x-text="cook.fullname"></option>
                                            </template>
                                        </select>
                                    </div>
                                </td>
                                {{-- PERBAIKAN TIMER DI SINI --}}
                                <td class="px-4 py-4 whitespace-nowrap font-mono font-bold" :class="timer.isLate ? 'text-gray-400' : 'text-red-600'" x-text="timer.display"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600" x-text="order.delivery_actual_time ? new Date(order.delivery_actual_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-bold" x-text="durationText"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div x-show="!isEditingDelivery && order.delivery_staff" @dblclick="isEditingDelivery = true" class="cursor-pointer p-1" x-text="order.delivery_staff ? order.delivery_staff.fullname : ''"></div>
                                    <div x-show="isEditingDelivery || !order.delivery_staff">
                                        <select x-model="selectedDelivery" @change="assignStaff('delivery')" @click.outside="isEditingDelivery = false" class="text-sm rounded-md border-gray-300" :disabled="order.order_status === 'Diproses'">
                                            <option value="">Pilih Staf</option>
                                            <template x-for="staff in $store.deliveryOrders.deliveryStaffs">
                                                <option :value="staff.user_id" x-text="staff.fullname"></option>
                                            </template>
                                        </select>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div @dblclick="if(['Siap Dihantar', 'Dihantarkan', 'Diterima'].includes(order.order_status)) isEditingStatus = true" 
                                         :class="{'cursor-pointer': ['Siap Dihantar', 'Dihantarkan', 'Diterima'].includes(order.order_status)}">
                                        
                                        <div x-show="!isEditingStatus">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="statusClass()" x-text="order.order_status"></span>
                                        </div>

                                        <div x-show="isEditingStatus" style="display: none;">
                                            <select x-model="selectedStatus" @change="updateStatus" @click.outside="isEditingStatus = false" class="text-sm rounded-md border-gray-300">
                                                <option value="" disabled>Ubah Status</option>
                                                <option value="Dihantarkan" x-show="order.order_status === 'Siap Dihantar'">Dihantarkan</option>
                                                <option value="Diterima" x-show="order.order_status === 'Dihantarkan'">Diterima</option>
                                                <option value="Siap Dihantar" x-show="order.order_status === 'Dihantarkan'">[Batalkan] Siap Dihantar</option>
                                                <option value="Dihantarkan" x-show="order.order_status === 'Diterima'">[Batalkan] Dihantarkan</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        // Store tidak berubah, sudah bagus.
        const initialOrders = {{ Js::from($orders) }};
        const statusOrder = ['Siap Dihantar', 'Dihantarkan', 'Diproses', 'Diterima'];
        initialOrders.sort((a, b) => {
            const statusComparison = statusOrder.indexOf(a.order_status) - statusOrder.indexOf(b.order_status);
            if (statusComparison !== 0) return statusComparison;
            return new Date(b.order_time) - new Date(a.order_time);
        });
        
        Alpine.store('deliveryOrders', {
            items: initialOrders,
            cooks: {{ Js::from($cooks) }},
            deliveryStaffs: {{ Js::from($deliveryStaffs) }},
            showNotification: false,
            notificationMessage: '',
            notificationSound: new Audio('/sounds/notification2.mp3'),

            update(updatedOrder) {
                const index = this.items.findIndex(o => o.order_id === updatedOrder.order_id);
                const oldOrder = index !== -1 ? this.items[index] : null;

                if (updatedOrder.order_status === 'Siap Dihantar' && (!oldOrder || oldOrder.order_status !== 'Siap Dihantar')) {
                    this.notificationMessage = `🔔 Pesanan #${updatedOrder.order_id} siap dihantar!`;
                    this.showNotification = true;
                    this.notificationSound.play().catch(e => console.error("Gagal memainkan suara.", e));
                    setTimeout(() => { this.showNotification = false; }, 6000);
                }

                if (index !== -1) {
                    this.items.splice(index, 1, updatedOrder);
                } else {
                    this.items.unshift(updatedOrder);
                }
                this.sort();
            },

            sort() {
                this.items.sort((a, b) => {
                    const statusComparison = statusOrder.indexOf(a.order_status) - statusOrder.indexOf(b.order_status);
                    if (statusComparison !== 0) return statusComparison;
                    return new Date(b.order_time) - new Date(a.order_time);
                });
            }
        });
    });

    // Komponen Manager utama untuk memulai listener
    const deliveryDashboardManager = {
        init() {
            if (typeof window.Echo === 'undefined') return;
            window.Echo.private('delivery-dashboard')
                .listen('.order-status-updated', (e) => {
                    Alpine.store('deliveryOrders').update(e.order);
                })
                .listen('.new-order', (e) => {
                    Alpine.store('deliveryOrders').update(e.order);
                });
        }
    }

    // ==========================================================
    // --- PERBAIKAN LOGIKA REAL-TIME ADA DI DALAM FUNGSI INI ---
    // ==========================================================
    function deliveryOrderRow(orderId) {
        return {
            orderId: orderId,
            isEditingCook: false,
            isEditingDelivery: false,
            isEditingStatus: false,
            selectedCook: '',
            selectedDelivery: '',
            selectedStatus: '',
            // State untuk timer dan durasi
            timer: { display: '30:00', isLate: false },
            durationText: '-',
            interval: null, // Untuk menyimpan ID dari setInterval

            // Ambil data order terbaru dari store
            get order() { return Alpine.store('deliveryOrders').items.find(o => o.order_id === this.orderId) || {}; },
            
            init() {
                this.selectedCook = this.order.kitchen_staff_user_id || '';
                this.selectedDelivery = this.order.delivery_staff_user_id || '';
                
                // Watcher untuk merespon perubahan data dari Pusher
                this.$watch('order', (newOrder) => {
                    if(!newOrder) return; 
                    this.selectedCook = newOrder.kitchen_staff_user_id || '';
                    this.selectedDelivery = newOrder.delivery_staff_user_id || '';
                    this.calculateDuration();
                    // Mulai atau hentikan timer berdasarkan status baru
                    this.startTimer(); 
                });
                
                // Kalkulasi awal saat komponen dimuat
                this.calculateDuration();
                this.startTimer();
            },

            // Fungsi untuk memulai/menghentikan timer
            startTimer() {
                if(this.interval) clearInterval(this.interval); // Hentikan timer lama jika ada
                
                this.updateTimer(); // Jalankan sekali agar tidak ada delay 1 detik
                
                // Hanya jalankan interval jika pesanan belum diterima
                if (this.order && this.order.order_status !== 'Diterima') {
                   this.interval = setInterval(() => this.updateTimer(), 1000);
                }
            },

            // Fungsi inti yang dijalankan setiap detik
            updateTimer() {
                if (!this.order || !this.order.order_time) {
                    this.timer.display = '-'; return;
                }

                if (this.order.order_status === 'Diterima') {
                    this.timer.display = '✓'; 
                    this.timer.isLate = true;
                    clearInterval(this.interval); 
                    return;
                }

                const timeRemaining = (30 * 60 * 1000) - (new Date() - new Date(this.order.order_time));

                if (timeRemaining <= 0) {
                    this.timer.display = 'LATE'; 
                    this.timer.isLate = true; 
                    clearInterval(this.interval); 
                    return;
                }
                
                // PERBAIKAN: Tambahkan baris di bawah ini
                this.timer.isLate = false; // <-- Pastikan statusnya tidak 'late' jika timer masih berjalan

                const minutes = Math.floor(timeRemaining / 60000);
                const seconds = Math.floor((timeRemaining % 60000) / 1000);
                this.timer.display = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            },
            
            // Fungsi untuk menghitung durasi total (tidak real-time, hanya dihitung saat data berubah)
            calculateDuration() {
                if (this.order && this.order.delivery_actual_time && this.order.order_time) {
                    let durationInSeconds = (new Date(this.order.delivery_actual_time) - new Date(this.order.order_time)) / 1000;
                    let minutes = Math.floor(durationInSeconds / 60);
                    let seconds = Math.round(durationInSeconds % 60);
                    this.durationText = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                } else { 
                    this.durationText = '-'; 
                }
            },
            
            // Sisa fungsi lainnya tetap sama
            statusClass() {
                if(!this.order) return {};
                return {
                    'bg-gray-100 text-gray-800': this.order.order_status === 'Diproses',
                    'bg-yellow-100 text-yellow-800': this.order.order_status === 'Siap Dihantar',
                    'bg-blue-100 text-blue-800': this.order.order_status === 'Dihantarkan',
                    'bg-green-100 text-green-800': this.order.order_status === 'Diterima',
                }
            },
            assignStaff(type) {
                let userId = (type === 'cook') ? this.selectedCook : this.selectedDelivery;
                if (!userId) return;
                axios.patch(`/delivery/orders/${this.order.order_id}/assign-staff`, { user_id: userId, type: type })
                    .then(res => { this.isEditingCook = false; this.isEditingDelivery = false; })
                    .catch(err => alert('Gagal: ' + (err.response?.data?.message || 'Error')));
            },
            updateStatus() {
                if(!this.selectedStatus) return;
                axios.patch(`/delivery/orders/${this.order.order_id}/update-status`, { status: this.selectedStatus })
                    .then(res => { this.isEditingStatus = false; this.selectedStatus = ''; })
                    .catch(err => alert('Gagal: ' + (err.response?.data?.message || 'Error')));
            }
        }
    }
    </script>
</x-app-layout>