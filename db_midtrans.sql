-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2024 at 02:18 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_midtrans`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2024_07_29_131354_create_products_table', 2),
(7, '2024_07_30_045105_create_carts_table', 3),
(8, '2024_07_30_073038_create_transactions_table', 4),
(9, '2024_07_30_091216_create_transaction_items_table', 5),
(10, '2024_07_31_033356_add_snap_token_to_transactions_table', 6),
(11, '2024_07_31_064449_add_order_id_to_transactions_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `image`, `name`, `description`, `price`, `created_at`, `updated_at`) VALUES
(4, 'products/converse_hitam.jpg', 'Converse Hitam', 'Deskripsi untuk Converse Hitam', 100000, '2024-07-30 02:23:37', '2024-07-30 02:23:37'),
(5, 'products/converse_putih.jpg', 'Converse Putih', 'Deskripsi untuk Converse Putih', 200000, '2024-07-30 02:23:37', '2024-07-30 02:23:37'),
(6, 'products/converse_merah.jpg', 'Converse Merah', 'Deskripsi untuk Converse Merah', 300000, '2024-07-30 02:23:37', '2024-07-30 02:23:37');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `snap_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `user_id`, `total_price`, `status`, `snap_token`, `created_at`, `updated_at`) VALUES
(12, 'be5a1179-c5fd-436a-9884-3b22003e3b9d', 1, '300000.00', 'Selesai', '8ff566ac-64c9-496c-9b85-693af374567b', '2024-07-31 00:05:52', '2024-07-31 00:16:45'),
(13, '269759ee-16dc-47e3-80da-7450ad26427d', 1, '200000.00', 'Dibatalkan', 'd91b55bd-d5a1-402e-b45d-84e65f76c17b', '2024-07-31 00:13:33', '2024-07-31 02:45:07'),
(14, '14951b36-0610-4cb8-b693-b45bc6019f24', 1, '300000.00', 'Dibatalkan', '827c9d52-11d8-4614-9dff-7336b26976a0', '2024-07-31 00:17:23', '2024-07-31 02:45:05'),
(15, '02efc35d-72f6-49f7-ba05-bc71f1a43489', 1, '200000.00', 'Dibatalkan', '657d854e-2dff-4784-81c9-fe2801b1d334', '2024-07-31 00:19:04', '2024-07-31 02:45:05'),
(18, '54264193-3cc2-490c-a3a4-af55ab829d66', 1, '300000.00', 'Dibatalkan', '131845dd-80ee-4fdc-9c99-28c4a23cc724', '2024-07-31 00:46:23', '2024-07-31 02:45:05'),
(19, 'cb9f7425-6e9f-4309-93f4-d940eaee8c80', 1, '200000.00', 'Selesai', '51e7e462-513f-4f96-bc7e-08a7d3d229d5', '2024-07-31 00:49:23', '2024-07-31 00:50:08'),
(20, 'c6686d9c-88e9-45de-b94b-a64e81387e9e', 1, '200000.00', 'Selesai', '7572915f-7d13-478e-9a13-9ac0cdd54724', '2024-07-31 00:57:50', '2024-07-31 00:57:54'),
(21, 'e1811bcd-8865-4b53-912d-cdb21a01b401', 1, '100000.00', 'Dibatalkan', '1966365c-a586-4308-aa4c-14294d923375', '2024-07-31 01:00:13', '2024-07-31 02:45:04'),
(22, 'e51a664a-3785-47df-9478-5b74c26f6460', 1, '1600000.00', 'Dibatalkan', 'f7cf7a77-acac-49d0-a7fb-9c9bf68f5f2c', '2024-07-31 01:33:29', '2024-07-31 02:45:04'),
(24, '476adaf2-39ff-4839-8064-5f631d2a841f', 1, '700000.00', 'Dibatalkan', '2af22353-6cff-4da1-8c15-99a529735605', '2024-07-31 01:42:45', '2024-07-31 02:45:04'),
(25, '1c243c17-cb08-4933-bc3b-3ae6aa69a306', 1, '200000.00', 'Dibatalkan', 'af0b3fdd-4495-4fb1-b1f3-4f21f3f5d7df', '2024-07-31 01:48:45', '2024-07-31 02:45:03'),
(26, '8419a790-49a1-48c1-a5a6-127a44f99e4c', 1, '200000.00', 'Dibatalkan', '89eb4edc-358b-4b8f-8ecd-c6f8068e2aca', '2024-07-31 01:50:54', '2024-07-31 02:45:03'),
(27, 'b366956c-0335-4ed5-a254-9e1f5b238507', 1, '100000.00', 'Selesai', '8c6f33c1-15b3-4157-a00f-a8730ff90781', '2024-07-31 01:51:57', '2024-07-31 01:53:43'),
(28, '318ebe0c-34e1-439a-8584-dbfa5d9066c1', 1, '200000.00', 'Selesai', '69a3ad79-bd19-4bd9-90af-7fe0df4d1328', '2024-07-31 01:55:12', '2024-07-31 01:57:49'),
(29, '59ed8ff8-2c63-4545-b754-b805ecf919aa', 1, '100000.00', 'Dibatalkan', '022f9675-428c-43c7-921d-12ae07a698b3', '2024-07-31 01:58:07', '2024-07-31 02:45:03'),
(30, '79dce794-baab-45ae-824a-6f77731167f8', 1, '200000.00', 'Dibatalkan', '2dcdfaa6-382d-425e-b0cf-ca043d194b71', '2024-07-31 01:59:22', '2024-07-31 02:45:01'),
(31, '5ffb8413-eb69-4f4c-aaa5-ca26bc7150c3', 1, '300000.00', 'Dibatalkan', '15bfc50b-88bc-4461-b092-df322ee996ec', '2024-07-31 02:02:42', '2024-07-31 02:45:01'),
(32, '86ffe1c5-d7ce-416c-bd65-081ecc35c7fe', 1, '100000.00', 'Dibatalkan', '693e2c45-bb1b-4eb6-9a9c-4033e7a4bad3', '2024-07-31 02:05:18', '2024-07-31 02:45:01'),
(33, 'e14b2db1-5fd2-409b-98d5-c297047d6036', 1, '1000000.00', 'Selesai', '9c51eaeb-ae2b-4cd8-bca9-b51979220017', '2024-07-31 02:06:56', '2024-07-31 02:08:42'),
(34, '45245db5-66c8-456d-b9f2-6c0428ab4f42', 1, '100000.00', 'Selesai', '9fea6c65-4a8a-44b1-b68d-cdda887cb6e5', '2024-07-31 02:20:46', '2024-07-31 02:21:09'),
(35, 'a96871a4-3797-476b-9dc4-5f8a421b6004', 1, '1800000.00', 'Dibatalkan', 'a441e451-b7a2-4dfb-a84f-e036f5bd5114', '2024-07-31 02:27:50', '2024-07-31 02:44:59'),
(36, 'feb81204-a753-4271-b2f7-ba65e8540fba', 1, '200000.00', 'Selesai', '71af5700-7463-46c5-840a-e0435926aa1d', '2024-07-31 02:57:51', '2024-07-31 02:58:27'),
(39, '4b3935ee-dde1-4b3f-820e-aae41014ea9f', 1, '100000.00', 'Selesai', 'ed1d930d-bed1-402a-b8db-0608f70e4ff6', '2024-07-31 03:01:03', '2024-07-31 03:01:32'),
(40, '73c898b1-1fd6-4db7-9b31-3bfad2094265', 1, '2100000.00', 'Selesai', '74717ef8-22ca-44da-ab76-dcbf1b737855', '2024-07-31 03:01:43', '2024-07-31 03:01:57'),
(41, 'c2381795-ecab-4f1f-875e-3939540b7671', 1, '800000.00', 'Dibatalkan', '4f95cc12-ec93-4d2c-b7c4-a91664ab5ae0', '2024-07-31 03:06:33', '2024-07-31 03:07:07'),
(42, '346edf4a-7781-435a-b41b-4e5a47a66e19', 1, '100000.00', 'Dibatalkan', 'd796e4f4-bf2b-4ffa-8ec7-b0136c0be8d9', '2024-07-31 03:07:17', '2024-07-31 03:07:51'),
(43, '981a24cd-e572-474b-bee5-42654f3013f9', 1, '100000.00', 'Dibatalkan', '5e42c0bf-e456-45f2-a0ed-33a874d9db96', '2024-07-31 03:13:10', '2024-07-31 03:27:35'),
(44, '1d54e107-9768-4179-a6b9-718f4ea1bb7d', 1, '500000.00', 'Dibatalkan', '131b305b-553b-4e5a-8dac-b2b8a0886758', '2024-07-31 03:27:53', '2024-07-31 03:28:57'),
(45, 'a1c92456-1edc-4153-95a3-54fd31701624', 1, '500000.00', 'Selesai', '04df7ba0-6b0a-4ce6-9e14-21f8e557ca00', '2024-07-31 03:29:43', '2024-07-31 03:29:58'),
(46, 'fa680fa1-6da7-48c2-a472-d6b22de7b000', 3, '100000.00', 'Dibatalkan', '8bfaa4ef-ff88-4fee-af2e-cb757ad1a5aa', '2024-07-31 03:46:16', '2024-07-31 03:47:30'),
(51, '5162a046-3903-453c-93da-be3accb15ebd', 3, '100000.00', 'Dibatalkan', '907e6e5e-0c28-4578-9bff-44fa86778ee9', '2024-07-31 04:30:46', '2024-07-31 04:31:09'),
(52, 'b1cce130-b8e0-434a-b400-630b1a018bb5', 1, '400000.00', 'Dibatalkan', '99e227b5-c986-4e53-b28f-2da7fd9b5326', '2024-07-31 17:16:16', '2024-07-31 17:16:28'),
(53, 'ab2d31a0-8b71-4906-8ef2-5058f124f636', 1, '200000.00', 'Dibatalkan', 'bc160983-ab64-48e8-9c31-6ccacb1eab4c', '2024-07-31 17:16:36', '2024-07-31 17:17:22');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(15, 12, 4, 1, '2024-07-31 00:05:52', '2024-07-31 00:05:52'),
(16, 12, 5, 1, '2024-07-31 00:05:52', '2024-07-31 00:05:52'),
(17, 13, 5, 1, '2024-07-31 00:13:33', '2024-07-31 00:13:33'),
(18, 14, 6, 1, '2024-07-31 00:17:23', '2024-07-31 00:17:23'),
(19, 15, 5, 1, '2024-07-31 00:19:04', '2024-07-31 00:19:04'),
(22, 18, 6, 1, '2024-07-31 00:46:23', '2024-07-31 00:46:23'),
(23, 19, 5, 1, '2024-07-31 00:49:23', '2024-07-31 00:49:23'),
(24, 20, 5, 1, '2024-07-31 00:57:50', '2024-07-31 00:57:50'),
(25, 21, 4, 1, '2024-07-31 01:00:13', '2024-07-31 01:00:13'),
(26, 22, 5, 5, '2024-07-31 01:33:29', '2024-07-31 01:33:29'),
(27, 22, 4, 3, '2024-07-31 01:33:29', '2024-07-31 01:33:29'),
(28, 22, 6, 1, '2024-07-31 01:33:29', '2024-07-31 01:33:29'),
(30, 24, 4, 7, '2024-07-31 01:42:45', '2024-07-31 01:42:45'),
(31, 25, 5, 1, '2024-07-31 01:48:45', '2024-07-31 01:48:45'),
(32, 26, 5, 1, '2024-07-31 01:50:54', '2024-07-31 01:50:54'),
(33, 27, 4, 1, '2024-07-31 01:51:57', '2024-07-31 01:51:57'),
(34, 28, 5, 1, '2024-07-31 01:55:12', '2024-07-31 01:55:12'),
(35, 29, 4, 1, '2024-07-31 01:58:07', '2024-07-31 01:58:07'),
(36, 30, 5, 1, '2024-07-31 01:59:22', '2024-07-31 01:59:22'),
(37, 31, 6, 1, '2024-07-31 02:02:42', '2024-07-31 02:02:42'),
(38, 32, 4, 1, '2024-07-31 02:05:18', '2024-07-31 02:05:18'),
(39, 33, 5, 5, '2024-07-31 02:06:56', '2024-07-31 02:06:56'),
(40, 34, 4, 1, '2024-07-31 02:20:46', '2024-07-31 02:20:46'),
(41, 35, 6, 6, '2024-07-31 02:27:50', '2024-07-31 02:27:50'),
(42, 36, 5, 1, '2024-07-31 02:57:51', '2024-07-31 02:57:51'),
(45, 39, 4, 1, '2024-07-31 03:01:03', '2024-07-31 03:01:03'),
(46, 40, 6, 7, '2024-07-31 03:01:43', '2024-07-31 03:01:43'),
(47, 41, 5, 1, '2024-07-31 03:06:33', '2024-07-31 03:06:33'),
(48, 41, 6, 1, '2024-07-31 03:06:33', '2024-07-31 03:06:33'),
(49, 41, 4, 3, '2024-07-31 03:06:33', '2024-07-31 03:06:33'),
(50, 42, 4, 1, '2024-07-31 03:07:17', '2024-07-31 03:07:17'),
(51, 43, 4, 1, '2024-07-31 03:13:10', '2024-07-31 03:13:10'),
(52, 44, 6, 1, '2024-07-31 03:27:53', '2024-07-31 03:27:53'),
(53, 44, 5, 1, '2024-07-31 03:27:53', '2024-07-31 03:27:53'),
(54, 45, 5, 1, '2024-07-31 03:29:43', '2024-07-31 03:29:43'),
(55, 45, 6, 1, '2024-07-31 03:29:43', '2024-07-31 03:29:43'),
(56, 46, 4, 1, '2024-07-31 03:46:16', '2024-07-31 03:46:16'),
(61, 51, 4, 1, '2024-07-31 04:30:46', '2024-07-31 04:30:46'),
(62, 52, 5, 2, '2024-07-31 17:16:16', '2024-07-31 17:16:16'),
(63, 53, 5, 1, '2024-07-31 17:16:36', '2024-07-31 17:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'irchamzah', 'irchamzah@gmail.com', NULL, '$2y$10$taeKMZwvlY86FvOROLZplu0X2/un7PNuTq39NsNVXuhjywDkYMfkm', NULL, '2024-07-29 06:09:00', '2024-07-29 06:09:00'),
(2, 'user1', 'user1@gmail.com', NULL, '$2y$10$1r6vwjnF1XkZxYib8pmCl.GGUAXgCXKE4GGuBAWZLKmX3nchK/8fC', NULL, '2024-07-29 21:30:35', '2024-07-29 21:30:35'),
(3, 'user', 'user@gmail.com', NULL, '$2y$10$KvDnnpJ8Uqg9ec.o8Wuu3O/T0kCSErD/RSilKqCFZDI.GcLaKzXPO', NULL, '2024-07-29 21:30:48', '2024-07-29 21:30:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`),
  ADD KEY `carts_product_id_foreign` (`product_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_items_transaction_id_foreign` (`transaction_id`),
  ADD KEY `transaction_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `transaction_items_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
