-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 02:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_food_delivery`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(100) NOT NULL COMMENT 'Nama item menu, harus unik',
  `availability_status` enum('Tersedia','Tidak Tersedia') NOT NULL DEFAULT 'Tersedia' COMMENT 'Status ketersediaan menu',
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_item_id`, `item_name`, `availability_status`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 'Nasi + Ayam Goreng', 'Tersedia', 1, '2025-06-17 11:51:27', '2025-06-17 11:51:27'),
(2, 'Nasi + Ayam Bakar', 'Tersedia', 1, '2025-06-17 11:51:35', '2025-06-17 11:51:35'),
(3, 'Steak', 'Tersedia', 1, '2025-06-17 11:51:40', '2025-06-17 11:51:40'),
(4, 'Es Teh', 'Tersedia', 1, '2025-06-17 11:51:46', '2025-06-17 11:51:46'),
(5, 'Es Jeruk', 'Tersedia', 1, '2025-06-17 11:51:51', '2025-06-17 11:51:51');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(11, '0001_01_01_000000_create_users_table', 1),
(12, '0001_01_01_000001_create_cache_table', 1),
(13, '0001_01_01_000002_create_jobs_table', 1),
(14, '2025_06_02_112122_create_roles_table', 1),
(15, '2025_06_02_112344_create_menu_items_table', 1),
(16, '2025_06_02_112446_create_room_types_table', 1),
(17, '2025_06_02_112527_create_rooms_table', 1),
(18, '2025_06_02_112916_create_orders_table', 1),
(19, '2025_06_02_113019_create_order_items_table', 1),
(20, '2025_06_02_113220_add_role_id_foreign_key_to_users_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` bigint(20) UNSIGNED NOT NULL COMMENT 'No.Check, ID unik pesanan',
  `room_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Foreign key ke tabel rooms',
  `order_time` datetime NOT NULL COMMENT 'Waktu pesanan dibuat oleh resepsionis',
  `receptionist_user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID Resepsionis yang membuat pesanan',
  `kitchen_staff_user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID Staf Dapur (individu) yang memasak (By Cook)',
  `delivery_staff_user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID Staf Antar (individu) yang mengantar (By Delivery)',
  `order_status` enum('Diproses','Siap Dihantar','Dihantarkan','Diterima','Dibatalkan') NOT NULL DEFAULT 'Diproses',
  `kitchen_timer_start_time` datetime DEFAULT NULL COMMENT 'Waktu notifikasi pesanan muncul di dapur & timer mulai',
  `kitchen_marked_ready_time` datetime DEFAULT NULL COMMENT 'Waktu Staf Dapur checklist pesanan siap',
  `kitchen_uncheck_allowed_until` datetime DEFAULT NULL COMMENT 'Batas waktu Staf Dapur boleh uncheck',
  `delivery_assignment_time` datetime DEFAULT NULL COMMENT 'Waktu Staf Antar dipilih dan status diubah ke Dihantarkan',
  `delivery_actual_time` datetime DEFAULT NULL COMMENT 'Waktu makanan diterima tamu (Waktu Delivery)',
  `delivery_correction_allowed_until` datetime DEFAULT NULL COMMENT 'Batas waktu Staf Antar boleh koreksi status Diterima',
  `sop_violation_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'True jika ada pelanggaran SOP',
  `sop_violation_notes` varchar(255) DEFAULT NULL COMMENT 'Catatan singkat mengenai pelanggaran SOP',
  `is_cancelled_by_receptionist` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'True jika pesanan dibatalkan',
  `cancellation_time` datetime DEFAULT NULL COMMENT 'Waktu pembatalan oleh resepsionis',
  `receptionist_cancellation_allowed_until` datetime DEFAULT NULL COMMENT 'Batas waktu Resepsionis boleh membatalkan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `room_id`, `order_time`, `receptionist_user_id`, `kitchen_staff_user_id`, `delivery_staff_user_id`, `order_status`, `kitchen_timer_start_time`, `kitchen_marked_ready_time`, `kitchen_uncheck_allowed_until`, `delivery_assignment_time`, `delivery_actual_time`, `delivery_correction_allowed_until`, `sop_violation_flag`, `sop_violation_notes`, `is_cancelled_by_receptionist`, `cancellation_time`, `receptionist_cancellation_allowed_until`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-06-17 18:53:31', 4, 3, 2, 'Diterima', '2025-06-17 18:53:31', '2025-06-17 18:56:50', '2025-06-17 18:57:20', '2025-06-17 19:02:19', '2025-06-17 19:43:10', NULL, 1, 'Durasi total dari pemesanan hingga diterima melebihi 30 menit.', 0, NULL, '2025-06-17 18:54:01', '2025-06-17 11:53:31', '2025-06-17 12:43:10');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Foreign key ke tabel orders',
  `menu_item_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Foreign key ke tabel menu_items',
  `quantity` int(11) NOT NULL,
  `item_notes` varchar(255) DEFAULT NULL COMMENT 'Catatan spesifik per item, cth: Pedas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `menu_item_id`, `quantity`, `item_notes`) VALUES
(1, 1, 5, 1, NULL),
(2, 1, 4, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL COMMENT 'Nama peran, cth: Resepsionis, Staf Dapur, Staf Antar, Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Resepsionis'),
(4, 'Staf Antar'),
(3, 'Staf Dapur');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `room_number` varchar(20) NOT NULL COMMENT 'Nomor kamar, harus unik',
  `room_type_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Foreign key ke tabel room_types',
  `status` enum('Terisi','Kosong','Dalam Perbaikan') NOT NULL DEFAULT 'Kosong' COMMENT 'Status kamar saat ini',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_type_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '101', 1, 'Terisi', '2025-06-17 11:52:37', '2025-06-17 11:52:37'),
(2, '102', 1, 'Terisi', '2025-06-17 11:52:44', '2025-06-17 11:52:50'),
(3, '103', 3, 'Terisi', '2025-06-17 11:52:58', '2025-06-17 11:52:58'),
(4, '104', 3, 'Terisi', '2025-06-17 11:53:06', '2025-06-17 11:53:06');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Nama tipe kamar, cth: Standard, Deluxe',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Standard', '2025-06-17 11:52:01', '2025-06-17 11:52:01'),
(2, 'Superior', '2025-06-17 11:52:17', '2025-06-17 11:52:17'),
(3, 'Deluxe', '2025-06-17 11:52:23', '2025-06-17 11:52:23');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('9mqSYfdEeUGkIWXvK5aOZcK38pI9uu1iTzOAwfYW', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRnR5SGdlM3NoaTNadTh5b0hKNnMwZ1FNUGFVV1ZVeU1VdGFzTmJiTCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozOToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2tpdGNoZW4vZGFzaGJvYXJkIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9raXRjaGVuL2Rhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1750164364),
('Eyg1OsbbQMFNsPdoJvCucA79COO0g2Q89ahzdLB0', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicUhJSGhvQzNFdkFYYTh4WHQxdXc4Y3ZJZ1ByMVp2VW1RS25DSTFMYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3JlY2VwdGlvbmlzdC9vcmRlci9jcmVhdGUiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo0NzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3JlY2VwdGlvbmlzdC9vcmRlci9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=', 1750164364),
('kOvjaaJI1wIdXTbqOg02IptfmcHDT13GhoK00sQO', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZDVKcmI4ZkplNFRlSU9KUjh0RGZNMFpDQ1M1SU9EU0pURlZ2bWcxRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9zb3AtdmlvbGF0aW9ucz9maWx0ZXI9dG9kYXkiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1750164373),
('VDJIJCG7EHubfvfD1pQyLaFOLVcEFgUuUIpO3Rqo', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibHVLaXRvRHN5VUd5WHR1YXFobERyWWtzREJtZUU2SEhpUmd5ZlJEQSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3NvcC12aW9sYXRpb25zP2ZpbHRlcj10b2RheSI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGVsaXZlcnkvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1750164364);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fullname` varchar(100) NOT NULL COMMENT 'Nama lengkap pengguna',
  `username` varchar(50) NOT NULL COMMENT 'Username untuk login',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email pengguna, unik dan opsional',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Password yang sudah di-hash',
  `role_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID untuk peran pengguna (akan dihubungkan nanti)',
  `status` enum('Aktif','Tidak Aktif') NOT NULL DEFAULT 'Aktif' COMMENT 'Status akun pengguna',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `username`, `email`, `email_verified_at`, `password`, `role_id`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator Utama', 'admin', 'admin@hotelemail.com', '2025-06-17 11:46:59', '$2y$12$LBINYs4YG9u9Zjw3XS0IseWQDd3rGcOL5dmqbl7bGVnrRgNZUX4fm', 1, 'Aktif', NULL, '2025-06-17 11:46:59', '2025-06-17 11:46:59'),
(2, 'Ahmad', 'delivery', 'delivery@gmail.com', NULL, '$2y$12$V2KBdNDbZhXZ8Xn7YDKzz.12TTTHt2jDKaE5lE4B9ep1iZBU2rxSC', 4, 'Aktif', NULL, '2025-06-17 11:49:25', '2025-06-17 11:49:25'),
(3, 'Raja', 'cook', 'cook@gmail.com', NULL, '$2y$12$FueFNl/i1g0sCzWGfcG4qu6P6IsNBIkEJqle63y0VL2.yxQyOQy4y', 3, 'Aktif', NULL, '2025-06-17 11:50:03', '2025-06-17 11:50:03'),
(4, 'Salsa', 'sisi', 'salsa@gmail.com', NULL, '$2y$12$qex9cieL9ThmdjCil/7lbu6nILOsPg3oFETON3XUO5/MmvwZv.ije', 2, 'Aktif', NULL, '2025-06-17 11:50:56', '2025-06-17 11:50:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`menu_item_id`),
  ADD UNIQUE KEY `menu_items_item_name_unique` (`item_name`),
  ADD KEY `menu_items_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `orders_room_id_foreign` (`room_id`),
  ADD KEY `orders_receptionist_user_id_foreign` (`receptionist_user_id`),
  ADD KEY `orders_kitchen_staff_user_id_foreign` (`kitchen_staff_user_id`),
  ADD KEY `orders_delivery_staff_user_id_foreign` (`delivery_staff_user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_menu_item_id_foreign` (`menu_item_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `roles_role_name_unique` (`role_name`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `rooms_room_number_unique` (`room_number`),
  ADD KEY `rooms_room_type_id_foreign` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_types_name_unique` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `menu_item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'No.Check, ID unik pesanan', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_delivery_staff_user_id_foreign` FOREIGN KEY (`delivery_staff_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_kitchen_staff_user_id_foreign` FOREIGN KEY (`kitchen_staff_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_receptionist_user_id_foreign` FOREIGN KEY (`receptionist_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`menu_item_id`),
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_room_type_id_foreign` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
