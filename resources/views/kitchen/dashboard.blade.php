<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-4xl text-black leading-tight">
            Pesanan Dapur
        </h2>
    </x-slot>

    {{-- Komponen Alpine utama, sekarang hanya bertugas menginisialisasi listener --}}
    <div class="py-8" x-data="kitchenDashboardManager">
        
        {{-- Banner Notifikasi akan dikontrol oleh Alpine Store --}}
        <div x-show="$store.orders.showNotification" 
             x-transition
             class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6" style="display: none;">
            <div class="p-4 bg-blue-600 text-white rounded-lg shadow-lg flex justify-between items-center">
                <p class="font-bold" x-text="$store.orders.notificationMessage"></p>
                <button @click="$store.orders.showNotification = false" class="text-2xl font-bold leading-none">&times;</button>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-4 sm:p-6 shadow-xl rounded-lg border border-gray-200">
                <div class="space-y-4">
                    {{-- Tampilan saat tidak ada pesanan --}}
                    <template x-if="$store.orders.items.length === 0">
                        <p class="text-center text-gray-500 py-16 text-2xl">Tidak ada pesanan aktif.</p>
                    </template>
                    
                    {{-- Loop sekarang mengambil data dari Alpine Store --}}
                    <template x-for="order in $store.orders.items" :key="order.order_id">
                         {{-- Komponen anak sekarang diinisialisasi hanya dengan ID, dan mengambil data dari store --}}
                         <div x-data="kitchenOrderItem(order.order_id)"
                             class="p-4 border rounded-lg flex items-center justify-between transition-all duration-500"
                             :class="{ 
                                 'opacity-50': order.order_status === 'Dihantarkan', 
                                 'opacity-60 bg-gray-50 text-gray-600': order.order_status === 'Diterima' 
                             }">
                            
                            <div class="flex-grow">
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div>
                                        <template x-for="item in order.order_items" :key="item.order_item_id">
                                            <p>
                                                <span class="font-medium" x-text="item.menu_item.item_name"></span>
                                                <span x-text="'x' + item.quantity"></span>
                                                <template x-if="item.item_notes">
                                                    <p class="text-xs text-red-600 italic pl-2" x-text="'- ' + item.item_notes"></p>
                                                </template>
                                            </p>
                                        </template>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg" :class="timer.isLate ? 'text-gray-400' : 'text-red-600'" x-text="timer.display"></p>
                                        <p class="text-sm" x-text="'No. #' + order.order_id"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-shrink-0 ml-6">
                                <div @click="toggleCheck()"
                                     class="w-12 h-12 border-2 rounded-md flex items-center justify-center"
                                     :class="{
                                         'bg-green-500 border-green-600': order.order_status === 'Siap Dihantar',
                                         'border-gray-400 hover:bg-gray-100': order.order_status === 'Diproses',
                                         'bg-blue-100': order.order_status === 'Dihantarkan',
                                         'bg-gray-200': order.order_status === 'Diterima',
                                         'cursor-pointer': canInteract,
                                         'cursor-not-allowed': !canInteract || isLoading,
                                         'animate-pulse': canUncheck
                                     }">
                                    <svg x-show="order.order_status !== 'Diproses'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" class="w-8 h-8" 
                                        :class="order.order_status === 'Siap Dihantar' ? 'stroke-white' : 'stroke-gray-500'" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                                <p x-show="canUncheck" class="text-xs text-center text-blue-600" x-text="uncheckTimer.display"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        // Mendefinisikan Store terpusat untuk data pesanan
        Alpine.store('orders', {
            items: [],
            showNotification: false,
            notificationMessage: '',
            
            init(initialOrders) {
                this.items = initialOrders;
            },

            add(newOrder) {
                // Mencegah duplikat jika event datang berkali-kali
                if (!this.items.some(o => o.order_id === newOrder.order_id)) {
                    this.items.unshift(newOrder);
                    this.notificationMessage = '🔔 Pesanan Baru Diterima! Daftar diperbarui.';
                    this.showNotification = true;
                    new Audio('/sounds/notification.mp3').play().catch(e => console.error(e));
                    setTimeout(() => this.showNotification = false, 5000);
                }
            },

            update(updatedOrder) {
                let index = this.items.findIndex(o => o.order_id === updatedOrder.order_id);
                if (index !== -1) {
                    this.items[index] = updatedOrder;
                } else {
                    this.add(updatedOrder); // Jika belum ada, tambahkan
                }
            }
        });
        
        // Inisialisasi store dengan data awal dari server
        Alpine.store('orders').init({{ Js::from($orders) }});
    });

    // Komponen Manager utama untuk memulai listener
    const kitchenDashboardManager = {
        init() {
            if (typeof window.Echo === 'undefined') {
                console.error('Error: Laravel Echo tidak terkonfigurasi.');
                return;
            }

            window.Echo.private('kitchen-orders')
                .listen('.new-order', (e) => {
                    Alpine.store('orders').add(e.order);
                })
                .listen('.order-status-updated', (e) => {
                    Alpine.store('orders').update(e.order);
                });
        }
    }

    // Komponen untuk setiap baris pesanan
    function kitchenOrderItem(orderId) {
        return {
            orderId: orderId,
            isLoading: false,
            // ...properti lain untuk logic internal baris...
            get order() {
                // Ini adalah kunci utama: selalu ambil data terbaru dari store!
                return Alpine.store('orders').items.find(o => o.order_id === this.orderId);
            },
            // Salin SEMUA method lain dari fungsi kitchenOrderItem(order) LAMA ke sini
            // (seperti: timer, uncheckTimer, canInteract, init(), updateMainTimer(), dll)
            // Contoh...
            timer: { display: '30:00', isLate: false },
            uncheckTimer: { display: '0:30' },
            canUncheck: false,
            uncheckDeadline: null,
            mainInterval: null,
            uncheckInterval: null,
                get canInteract() {
                    return this.order.order_status === 'Diproses' || (this.order.order_status === 'Siap Dihantar' && this.canUncheck);
                },
                init() {
                    this.updateMainTimer();
                    this.mainInterval = setInterval(() => this.updateMainTimer(), 1000);
                    if (this.order.order_status === 'Siap Dihantar') {
                        this.uncheckDeadline = new Date(this.order.kitchen_uncheck_allowed_until);
                        this.startUncheckTimer();
                    }
                },
                updateMainTimer() {
                    if (['Dihantarkan', 'Diterima'].includes(this.order.order_status)) {
                        this.timer.display = '✓'; this.timer.isLate = true; clearInterval(this.mainInterval); return;
                    }
                    let timeRemaining = (30 * 60 * 1000) - (new Date() - new Date(this.order.order_time));
                    if (timeRemaining <= 0) { timeRemaining = 0; this.timer.isLate = true; clearInterval(this.mainInterval); }
                    let minutes = Math.floor(timeRemaining / 60000);
                    let seconds = Math.floor((timeRemaining % 60000) / 1000);
                    this.timer.display = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                },
                startUncheckTimer() {
                    if (!this.uncheckDeadline || new Date() > this.uncheckDeadline) { this.canUncheck = false; return; }
                    this.canUncheck = true;
                    if(this.uncheckInterval) clearInterval(this.uncheckInterval);
                    this.uncheckInterval = setInterval(() => {
                        let timeRemaining = this.uncheckDeadline - new Date();
                        if (timeRemaining < 0) {
                            this.canUncheck = false; this.uncheckTimer.display = ''; clearInterval(this.uncheckInterval);
                        } else {
                            this.uncheckTimer.display = `0:${String(Math.ceil(timeRemaining/1000)).padStart(2, '0')}`;
                        }
                    }, 1000);
                },
                toggleCheck() {
                    if (!this.canInteract || this.isLoading) return;
                    if (this.order.order_status === 'Siap Dihantar') {
                        this.uncheckOrder();
                    } else {
                        this.checkOrder();
                    }
                },
                checkOrder() {
                    this.isLoading = true;
                    axios.patch(`/kitchen/orders/${this.order.order_id}/mark-as-ready`)
                        .then(res => {
                            this.order.order_status = 'Siap Dihantar';
                            this.uncheckDeadline = new Date(new Date().getTime() + 30000);
                            this.startUncheckTimer();
                        }).catch(err => alert('Gagal: ' + (err.response?.data?.message || 'Terjadi kesalahan')))
                        .finally(() => this.isLoading = false);
                },
                uncheckOrder() {
                    this.isLoading = true;
                    axios.patch(`/kitchen/orders/${this.order.order_id}/uncheck`)
                        .then(res => {
                            this.order.order_status = 'Diproses';
                            this.canUncheck = false;
                            clearInterval(this.uncheckInterval);
                            this.uncheckTimer.display = '';
                        }).catch(err => alert('Gagal: ' + (err.response?.data?.message || 'Terjadi kesalahan')))
                        .finally(() => this.isLoading = false);
                }
            }
        }
    </script>
</x-app-layout>