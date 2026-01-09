-- --------------------------------------------------------
-- Máy chủ:                      127.0.0.1
-- Phiên bản máy chủ:            8.0.44-0ubuntu0.24.04.1 - (Ubuntu)
-- HĐH máy chủ:                  Linux
-- HeidiSQL Phiên bản:           12.13.0.7147
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Đang kết xuất đổ cấu trúc cơ sở dữ liệu cho reverb_mini
CREATE DATABASE IF NOT EXISTS `reverb_mini` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `reverb_mini`;

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.cache: ~0 rows (xấp xỉ)
DELETE FROM `cache`;

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.cache_locks: ~0 rows (xấp xỉ)
DELETE FROM `cache_locks`;

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.failed_jobs: ~0 rows (xấp xỉ)
DELETE FROM `failed_jobs`;

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.job_batches: ~0 rows (xấp xỉ)
DELETE FROM `job_batches`;

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.jobs: ~0 rows (xấp xỉ)
DELETE FROM `jobs`;

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `from_user_id` bigint unsigned DEFAULT NULL,
  `to_user_id` bigint unsigned DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_from_user_id_foreign` (`from_user_id`),
  KEY `messages_to_user_id_foreign` (`to_user_id`),
  CONSTRAINT `messages_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.messages: ~22 rows (xấp xỉ)
DELETE FROM `messages`;
INSERT INTO `messages` (`id`, `from_user_id`, `to_user_id`, `content`, `is_read`, `created_at`, `updated_at`) VALUES
	(12, 1, 2, 'vo dong son', 1, '2026-01-05 13:20:02', '2026-01-05 13:56:54'),
	(13, 2, 1, 'bach thu ha', 1, '2026-01-05 13:20:23', '2026-01-05 13:20:28'),
	(14, 2, 1, 'bien cuong la roi thu ha em oi', 1, '2026-01-05 13:20:47', '2026-01-05 13:20:53'),
	(15, 1, 2, 'hay', 1, '2026-01-05 13:57:08', '2026-01-05 14:04:40'),
	(16, 1, 2, 'oke', 1, '2026-01-05 14:11:00', '2026-01-05 14:11:06'),
	(17, 1, 2, 'oke v2', 1, '2026-01-05 14:22:47', '2026-01-05 14:33:44'),
	(18, 1, 2, 'dfdfđsfsf', 1, '2026-01-05 14:40:15', '2026-01-05 15:09:06'),
	(19, 1, 2, 'dfdfdf', 1, '2026-01-08 18:05:38', '2026-01-08 18:13:13'),
	(20, 2, 1, 'dsdsd', 1, '2026-01-08 18:05:50', '2026-01-08 18:05:54'),
	(21, 2, 1, 'âss', 1, '2026-01-08 18:41:43', '2026-01-08 18:41:50'),
	(22, 1, 2, 'd', 1, '2026-01-08 18:42:25', '2026-01-08 18:45:39'),
	(23, 1, 2, 'xzxzx', 1, '2026-01-08 18:45:35', '2026-01-08 18:45:39'),
	(24, 2, 1, 'huhu]', 1, '2026-01-08 18:45:44', '2026-01-08 18:47:54'),
	(25, 1, 2, 'bghghgd', 1, '2026-01-08 18:54:32', '2026-01-08 19:04:10'),
	(26, 2, 1, 'rewrwr', 1, '2026-01-08 18:54:36', '2026-01-08 18:58:55'),
	(27, 2, 1, 'dsdsd', 1, '2026-01-09 03:09:55', '2026-01-09 03:10:31'),
	(28, 2, 1, 'sdsdsdsd', 1, '2026-01-09 03:10:11', '2026-01-09 03:10:31'),
	(29, 2, 1, 'fsfsdfsdfdsfs', 1, '2026-01-09 03:10:40', '2026-01-09 03:10:47'),
	(30, 1, 2, 'sfsdf', 1, '2026-01-09 03:10:50', '2026-01-09 03:13:50'),
	(31, 1, 2, 'dfdfsdf', 1, '2026-01-09 03:13:37', '2026-01-09 03:13:50'),
	(32, 1, 2, 'áas', 1, '2026-01-09 03:13:45', '2026-01-09 03:13:50'),
	(33, 1, 2, 'adassad', 1, '2026-01-09 03:13:58', '2026-01-09 03:28:55'),
	(34, 1, 2, 'dsdasd', 1, '2026-01-09 03:55:19', '2026-01-09 03:56:10'),
	(35, 1, 2, 'đấ', 1, '2026-01-09 03:55:47', '2026-01-09 03:56:10'),
	(36, 1, 2, 'têtrtre', 1, '2026-01-09 03:56:20', '2026-01-09 03:57:44'),
	(37, 1, 2, 'fdkfsf', 1, '2026-01-09 04:12:12', '2026-01-09 04:17:39'),
	(38, 2, 1, 'hello', 0, '2026-01-09 04:17:46', '2026-01-09 04:17:46');

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.migrations: ~9 rows (xấp xỉ)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_01_02_133115_create_messages_table', 1),
	(5, '2026_01_04_095316_add_avatar_users_table', 2),
	(6, '2026_01_05_073538_add_is_read_to_users', 3),
	(7, '2026_01_05_074405_add_chat_columns_to_messages_table', 4),
	(8, '2026_01_05_074733_drop_user_id_from_messages_table', 5),
	(9, '2026_01_05_074953_drop_is_read_from_users_table', 6),
	(10, '2026_01_09_032625_add_last_seen_at_to_users_table', 7);

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.password_reset_tokens: ~0 rows (xấp xỉ)
DELETE FROM `password_reset_tokens`;

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.sessions: ~2 rows (xấp xỉ)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('22NIEafo7jkBmFiCXsKYwoKQ6t3Bg6Qy7PRyalSO', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Avast/143.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiejF6a2xIeExRcUhvQlJGVlpHbmVrSlNpdHVJQVN4dWdmTnFSWmNIOCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tZXNzYWdlcy91bnJlYWQtdG90YWwiO3M6NToicm91dGUiO047fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767933475),
	('eIxpJNAjC4AgJe6CHIbKR0nlQ5jtBMOLiMvUWM47', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibjlUMFN1ak1oQXpYQ0tuNTR2QUJ6alRPejgwYmJxQkpOcUJ5cmlSQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tZXNzYWdlcy91bnJlYWQtdG90YWwiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1767933416);

-- Đang kết xuất đổ cấu trúc cho bảng reverb_mini.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đang kết xuất đổ dữ liệu cho bảng reverb_mini.users: ~3 rows (xấp xỉ)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `last_seen_at`, `created_at`, `updated_at`, `avatar`) VALUES
	(1, 'testa', 'testa@gmail.com', NULL, '$2y$12$rUA7r73nJzbFw2h0bkbDROPSW5LIaVrr8ETHvQRHiVK22REF3W9cG', NULL, '2026-01-09 04:03:21', '2026-01-02 16:12:03', '2026-01-09 04:03:21', 'https://api.dicebear.com/9.x/adventurer/svg?seed=Jameson'),
	(2, 'testb', 'testb@gmail.com', NULL, '$2y$12$QJshZS.YBztKfNezCTypuO77FEZ1TqN9/Ge4XmXdj4Z1j4rb2mj12', NULL, '2026-01-09 03:57:54', '2026-01-03 07:08:12', '2026-01-09 03:57:54', 'https://api.dicebear.com/9.x/adventurer/svg?seed=Jude'),
	(3, 'testc', 'testc@gmail.com', NULL, '$2y$12$Jy/jO/mcnfirbfPk6peHOOeXSypxxh8baZesUxzahE/7/yAyUGca6', NULL, NULL, '2026-01-04 07:36:53', '2026-01-04 07:36:53', 'https://api.dicebear.com/9.x/adventurer/svg?seed=Emery');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
