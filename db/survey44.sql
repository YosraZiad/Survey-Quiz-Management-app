-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 14, 2025 at 08:41 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `survey`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
CREATE TABLE IF NOT EXISTS `answers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `response_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `option_id` bigint UNSIGNED DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `answers_response_id_foreign` (`response_id`),
  KEY `answers_question_id_foreign` (`question_id`),
  KEY `answers_option_id_foreign` (`option_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `response_id`, `question_id`, `option_id`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 25, NULL, '2025-09-14 16:40:07', '2025-09-14 16:40:07'),
(2, 1, 9, 33, NULL, '2025-09-14 16:40:07', '2025-09-14 16:40:07'),
(3, 1, 10, NULL, '40', '2025-09-14 16:40:07', '2025-09-14 16:40:07'),
(4, 1, 11, 35, NULL, '2025-09-14 16:40:07', '2025-09-14 16:40:07'),
(5, 1, 12, 41, NULL, '2025-09-14 16:40:07', '2025-09-14 16:40:07'),
(6, 1, 13, 47, NULL, '2025-09-14 16:40:07', '2025-09-14 16:40:07'),
(7, 1, 14, NULL, '10', '2025-09-14 16:40:07', '2025-09-14 16:40:07'),
(8, 2, 8, 25, NULL, '2025-09-14 17:15:58', '2025-09-14 17:15:58'),
(9, 2, 9, 33, NULL, '2025-09-14 17:15:58', '2025-09-14 17:15:58'),
(10, 2, 10, NULL, '40', '2025-09-14 17:15:58', '2025-09-14 17:15:58'),
(11, 2, 11, 35, NULL, '2025-09-14 17:15:58', '2025-09-14 17:15:58'),
(12, 2, 12, 41, NULL, '2025-09-14 17:15:58', '2025-09-14 17:15:58'),
(13, 2, 13, 47, NULL, '2025-09-14 17:15:58', '2025-09-14 17:15:58'),
(14, 2, 14, NULL, '10', '2025-09-14 17:15:58', '2025-09-14 17:15:58'),
(15, 3, 26, NULL, 'ATEF00000000000', '2025-09-14 17:18:03', '2025-09-14 17:18:03'),
(16, 3, 27, NULL, 'atefakl800000@gmail.com', '2025-09-14 17:18:03', '2025-09-14 17:18:03'),
(17, 3, 28, 95, NULL, '2025-09-14 17:18:03', '2025-09-14 17:18:03'),
(18, 3, 30, 102, NULL, '2025-09-14 17:18:03', '2025-09-14 17:18:03'),
(19, 3, 31, 123, NULL, '2025-09-14 17:18:03', '2025-09-14 17:18:03'),
(20, 3, 32, 124, NULL, '2025-09-14 17:18:03', '2025-09-14 17:18:03');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_09_10_111511_create_surveys_table', 1),
(6, '2025_09_10_120001_create_questions_table', 1),
(7, '2025_09_10_120002_create_options_table', 1),
(8, '2025_09_10_120003_create_respondents_table', 1),
(9, '2025_09_10_120004_create_responses_table', 1),
(10, '2025_09_10_120005_create_answers_table', 1),
(11, '2025_09_12_070410_add_score_and_account_type_to_users_table', 1),
(12, '2025_09_12_083600_add_survey_number_to_surveys_table', 1),
(13, '2025_09_12_092200_add_weight_to_questions_table', 1),
(14, '2025_09_12_092300_add_weight_and_is_correct_to_options_table', 1),
(15, '2025_09_12_999999_fix_options_label_default', 1),
(16, '2025_09_13_135031_add_weight_to_questions_and_points_to_options', 1),
(17, '2025_09_13_210501_add_description_to_questions_table', 1),
(18, '2025_09_14_144033_add_unique_constraint_to_surveys_title', 1),
(19, '2025_09_14_180215_add_is_active_to_surveys_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
CREATE TABLE IF NOT EXISTS `options` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` bigint UNSIGNED NOT NULL,
  `label` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Option',
  `weight` double(8,2) DEFAULT NULL,
  `points` int DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `options_question_id_foreign` (`question_id`)
) ENGINE=MyISAM AUTO_INCREMENT=149 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `label`, `weight`, `points`, `is_correct`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'مصر', NULL, NULL, 0, 0, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(2, 1, 'السعودية', NULL, NULL, 0, 1, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(3, 1, 'الإمارات', NULL, NULL, 0, 2, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(4, 1, 'الكويت', NULL, NULL, 0, 3, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(5, 1, 'قطر', NULL, NULL, 0, 4, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(6, 1, 'البحرين', NULL, NULL, 0, 5, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(7, 1, 'عمان', NULL, NULL, 0, 6, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(8, 1, 'الأردن', NULL, NULL, 0, 7, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(9, 2, 'ذكر', NULL, NULL, 0, 0, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(10, 2, 'أنثى', NULL, NULL, 0, 1, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(11, 4, 'أقل من الثانوية', NULL, NULL, 0, 0, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(12, 4, 'ثانوية عامة', NULL, NULL, 0, 1, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(13, 4, 'دبلوم', NULL, NULL, 0, 2, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(14, 4, 'بكالوريوس', NULL, NULL, 0, 3, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(15, 4, 'ماجستير', NULL, NULL, 0, 4, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(16, 4, 'دكتوراه', NULL, NULL, 0, 5, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(17, 5, 'موظف حكومي', NULL, NULL, 0, 0, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(18, 5, 'موظف قطاع خاص', NULL, NULL, 0, 1, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(19, 5, 'أعمال حرة', NULL, NULL, 0, 2, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(20, 5, 'طالب', NULL, NULL, 0, 3, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(21, 5, 'متقاعد', NULL, NULL, 0, 4, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(22, 5, 'عاطل عن العمل', NULL, NULL, 0, 5, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(23, 6, 'نعم', NULL, NULL, 0, 0, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(24, 6, 'لا', NULL, NULL, 0, 1, '2025-09-14 16:39:40', '2025-09-14 16:39:40'),
(25, 8, 'مصر', NULL, NULL, 0, 0, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(26, 8, 'السعودية', NULL, NULL, 0, 1, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(27, 8, 'الإمارات', NULL, NULL, 0, 2, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(28, 8, 'الكويت', NULL, NULL, 0, 3, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(29, 8, 'قطر', NULL, NULL, 0, 4, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(30, 8, 'البحرين', NULL, NULL, 0, 5, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(31, 8, 'عمان', NULL, NULL, 0, 6, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(32, 8, 'الأردن', NULL, NULL, 0, 7, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(33, 9, 'ذكر', NULL, NULL, 0, 0, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(34, 9, 'أنثى', NULL, NULL, 0, 1, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(35, 11, 'أقل من الثانوية', NULL, NULL, 0, 0, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(36, 11, 'ثانوية عامة', NULL, NULL, 0, 1, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(37, 11, 'دبلوم', NULL, NULL, 0, 2, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(38, 11, 'بكالوريوس', NULL, NULL, 0, 3, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(39, 11, 'ماجستير', NULL, NULL, 0, 4, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(40, 11, 'دكتوراه', NULL, NULL, 0, 5, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(41, 12, 'موظف حكومي', NULL, NULL, 0, 0, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(42, 12, 'موظف قطاع خاص', NULL, NULL, 0, 1, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(43, 12, 'أعمال حرة', NULL, NULL, 0, 2, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(44, 12, 'طالب', NULL, NULL, 0, 3, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(45, 12, 'متقاعد', NULL, NULL, 0, 4, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(46, 12, 'عاطل عن العمل', NULL, NULL, 0, 5, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(47, 13, 'نعم', NULL, NULL, 0, 0, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(48, 13, 'لا', NULL, NULL, 0, 1, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(49, 17, 'أقل من 15 سنة', NULL, NULL, 0, 0, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(50, 17, 'من 15 إلى 25 سنة', NULL, NULL, 0, 1, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(51, 17, 'من 25 إلى 35 سنة', NULL, NULL, 0, 2, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(52, 17, 'أكبر من 35 سنة', NULL, NULL, 0, 3, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(53, 18, 'ذكر', NULL, NULL, 0, 0, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(54, 18, 'أنثى', NULL, NULL, 0, 1, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(55, 18, 'أفضل عدم الإجابة', NULL, NULL, 0, 2, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(56, 19, 'مصر', NULL, NULL, 0, 0, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(57, 19, 'السعودية', NULL, NULL, 0, 1, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(58, 19, 'الإمارات', NULL, NULL, 0, 2, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(59, 19, 'الكويت', NULL, NULL, 0, 3, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(60, 19, 'قطر', NULL, NULL, 0, 4, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(61, 19, 'البحرين', NULL, NULL, 0, 5, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(62, 19, 'عمان', NULL, NULL, 0, 6, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(63, 19, 'الأردن', NULL, NULL, 0, 7, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(64, 19, 'فلسطين', NULL, NULL, 0, 8, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(65, 19, 'لبنان', NULL, NULL, 0, 9, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(66, 19, 'سوريا', NULL, NULL, 0, 10, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(67, 19, 'العراق', NULL, NULL, 0, 11, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(68, 19, 'اليمن', NULL, NULL, 0, 12, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(69, 19, 'ليبيا', NULL, NULL, 0, 13, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(70, 19, 'تونس', NULL, NULL, 0, 14, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(71, 19, 'الجزائر', NULL, NULL, 0, 15, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(72, 19, 'المغرب', NULL, NULL, 0, 16, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(73, 19, 'موريتانيا', NULL, NULL, 0, 17, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(74, 19, 'الصومال', NULL, NULL, 0, 18, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(75, 19, 'جيبوتي', NULL, NULL, 0, 19, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(76, 19, 'جزر القمر', NULL, NULL, 0, 20, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(77, 20, '(حقل نصي)*', NULL, NULL, 0, 0, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(78, 21, 'طالب ثانوي', NULL, NULL, 0, 0, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(79, 21, 'طالب جامعي', NULL, NULL, 0, 1, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(80, 21, 'خريج جامعي', NULL, NULL, 0, 2, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(81, 21, 'ماجستير', NULL, NULL, 0, 3, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(82, 21, 'دكتوراه', NULL, NULL, 0, 4, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(83, 22, '(حقل نصي)*', NULL, NULL, 0, 0, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(84, 23, 'طالب', NULL, NULL, 0, 0, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(85, 23, 'خريج', NULL, NULL, 0, 1, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(86, 23, 'موظف', NULL, NULL, 0, 2, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(87, 23, 'أعمل لحسابي', NULL, NULL, 0, 3, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(88, 23, 'أبحث عن عمل', NULL, NULL, 0, 4, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(89, 24, 'مبتدئ', NULL, NULL, 0, 0, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(90, 24, 'متوسط', NULL, NULL, 0, 1, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(91, 24, 'متقدم', NULL, NULL, 0, 2, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(92, 24, 'متمكن', NULL, NULL, 0, 3, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(93, 25, 'نعم', NULL, NULL, 0, 0, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(94, 25, 'لا', NULL, NULL, 0, 1, '2025-09-14 17:17:29', '2025-09-14 17:17:29'),
(95, 28, 'أقل من 15 سنة', NULL, NULL, 0, 0, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(96, 28, 'من 15 إلى 25 سنة', NULL, NULL, 0, 1, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(97, 28, 'من 25 إلى 35 سنة', NULL, NULL, 0, 2, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(98, 28, 'أكبر من 35 سنة', NULL, NULL, 0, 3, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(99, 29, 'ذكر', NULL, NULL, 0, 0, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(100, 29, 'أنثى', NULL, NULL, 0, 1, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(101, 29, 'أفضل عدم الإجابة', NULL, NULL, 0, 2, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(102, 30, 'مصر', NULL, NULL, 0, 0, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(103, 30, 'السعودية', NULL, NULL, 0, 1, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(104, 30, 'الإمارات', NULL, NULL, 0, 2, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(105, 30, 'الكويت', NULL, NULL, 0, 3, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(106, 30, 'قطر', NULL, NULL, 0, 4, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(107, 30, 'البحرين', NULL, NULL, 0, 5, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(108, 30, 'عمان', NULL, NULL, 0, 6, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(109, 30, 'الأردن', NULL, NULL, 0, 7, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(110, 30, 'فلسطين', NULL, NULL, 0, 8, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(111, 30, 'لبنان', NULL, NULL, 0, 9, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(112, 30, 'سوريا', NULL, NULL, 0, 10, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(113, 30, 'العراق', NULL, NULL, 0, 11, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(114, 30, 'اليمن', NULL, NULL, 0, 12, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(115, 30, 'ليبيا', NULL, NULL, 0, 13, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(116, 30, 'تونس', NULL, NULL, 0, 14, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(117, 30, 'الجزائر', NULL, NULL, 0, 15, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(118, 30, 'المغرب', NULL, NULL, 0, 16, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(119, 30, 'موريتانيا', NULL, NULL, 0, 17, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(120, 30, 'الصومال', NULL, NULL, 0, 18, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(121, 30, 'جيبوتي', NULL, NULL, 0, 19, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(122, 30, 'جزر القمر', NULL, NULL, 0, 20, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(123, 31, '(حقل نصي)*', NULL, NULL, 0, 0, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(124, 32, 'طالب ثانوي', NULL, NULL, 0, 0, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(125, 32, 'طالب جامعي', NULL, NULL, 0, 1, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(126, 32, 'خريج جامعي', NULL, NULL, 0, 2, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(127, 32, 'ماجستير', NULL, NULL, 0, 3, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(128, 32, 'دكتوراه', NULL, NULL, 0, 4, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(129, 33, '(حقل نصي)*', NULL, NULL, 0, 0, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(130, 34, 'طالب', NULL, NULL, 0, 0, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(131, 34, 'خريج', NULL, NULL, 0, 1, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(132, 34, 'موظف', NULL, NULL, 0, 2, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(133, 34, 'أعمل لحسابي', NULL, NULL, 0, 3, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(134, 34, 'أبحث عن عمل', NULL, NULL, 0, 4, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(135, 35, 'مبتدئ', NULL, NULL, 0, 0, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(136, 35, 'متوسط', NULL, NULL, 0, 1, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(137, 35, 'متقدم', NULL, NULL, 0, 2, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(138, 35, 'متمكن', NULL, NULL, 0, 3, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(139, 36, 'نعم', NULL, NULL, 0, 0, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(140, 36, 'لا', NULL, NULL, 0, 1, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(141, 37, 'الخيار 1', NULL, NULL, 0, 0, '2025-09-14 17:21:59', '2025-09-14 17:21:59'),
(142, 37, 'الخيار 2', NULL, NULL, 0, 1, '2025-09-14 17:21:59', '2025-09-14 17:21:59'),
(143, 38, 'الخيار 1', NULL, NULL, 0, 0, '2025-09-14 17:22:05', '2025-09-14 17:22:05'),
(144, 38, 'الخيار 2', NULL, NULL, 0, 1, '2025-09-14 17:22:05', '2025-09-14 17:22:05'),
(145, 39, 'الخيار 1', NULL, NULL, 0, 0, '2025-09-14 17:22:09', '2025-09-14 17:22:09'),
(146, 39, 'الخيار 2', NULL, NULL, 0, 1, '2025-09-14 17:22:09', '2025-09-14 17:22:09'),
(147, 40, 'الخيار 1', NULL, NULL, 0, 0, '2025-09-14 17:31:35', '2025-09-14 17:31:35'),
(148, 40, 'الخيار 2', NULL, NULL, 0, 1, '2025-09-14 17:31:35', '2025-09-14 17:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `survey_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `points` int DEFAULT NULL,
  `display_order` int UNSIGNED NOT NULL DEFAULT '0',
  `weight` double(8,2) DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_survey_id_foreign` (`survey_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `survey_id`, `title`, `description`, `type`, `required`, `points`, `display_order`, `weight`, `metadata`, `created_at`, `updated_at`) VALUES
(13, 4, 'هل أنت راض عن الخدمة؟', NULL, 'dropdown', 0, NULL, 5, 1.00, NULL, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(12, 4, 'Employment Status (حالة التوظيف)', NULL, 'dropdown', 0, NULL, 4, 1.00, NULL, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(11, 4, 'Education Level (المستوى التعليمي)', NULL, 'dropdown', 0, NULL, 3, 1.00, NULL, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(10, 4, 'Age Group (الفئة العمرية)', NULL, 'number', 0, NULL, 2, 1.00, NULL, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(9, 4, 'Gender (الجنس)', NULL, 'dropdown', 0, NULL, 1, 1.00, NULL, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(8, 4, 'Country (اختر الدولة)', NULL, 'dropdown', 0, NULL, 0, 1.00, NULL, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(14, 4, 'كم مرة تستخدم هذه الخدمة؟', NULL, 'number', 0, NULL, 6, 1.00, NULL, '2025-09-14 16:39:44', '2025-09-14 16:39:44'),
(33, 5, 'What is your major?', NULL, 'radio', 0, 1, 7, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(32, 5, 'Educational Level', NULL, 'dropdown', 0, 1, 6, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(31, 5, 'Residence  (Please provide your current city and country of residence)', NULL, 'dropdown', 0, 1, 5, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(30, 5, 'Country  (اختر الدولة)', NULL, 'dropdown', 0, 1, 4, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(29, 5, 'Gender  (اختر الجنس)', NULL, 'radio', 0, 1, 3, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(28, 5, 'Age  (اختر الفئة العمرية)', NULL, 'dropdown', 0, 1, 2, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(26, 5, 'Full Name', NULL, 'short', 0, 1, 0, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(27, 5, 'Email', NULL, 'short', 0, 1, 1, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(34, 5, 'What is your employment status?', NULL, 'radio', 0, 1, 8, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(35, 5, 'What is your English language proficiency?', NULL, 'radio', 0, 1, 9, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(36, 5, 'Do you own a personal computer (PC or laptop) that you can work on?', NULL, 'radio', 0, 1, 10, NULL, NULL, '2025-09-14 17:17:33', '2025-09-14 17:17:33'),
(39, 6, 'Choose one answer', NULL, 'radio', 0, NULL, 0, 1.00, NULL, '2025-09-14 17:22:09', '2025-09-14 17:22:09'),
(40, 7, 'Choose one answer', NULL, 'radio', 1, NULL, 0, 1.00, NULL, '2025-09-14 17:31:35', '2025-09-14 17:31:35'),
(41, 7, 'Short text question', NULL, 'short', 1, NULL, 1, 1.00, NULL, '2025-09-14 17:31:35', '2025-09-14 17:31:35'),
(42, 7, 'Question', NULL, 'q_1757881872384_olvo', 1, NULL, 2, 1.00, NULL, '2025-09-14 17:31:35', '2025-09-14 17:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `respondents`
--

DROP TABLE IF EXISTS `respondents`;
CREATE TABLE IF NOT EXISTS `respondents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` smallint UNSIGNED DEFAULT NULL,
  `education` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `respondents`
--

INSERT INTO `respondents` (`id`, `name`, `email`, `gender`, `age`, `education`, `location`, `created_at`, `updated_at`) VALUES
(1, 'Atef Akl88', 'atefakl80@gmail.com', NULL, NULL, NULL, NULL, '2025-09-14 16:40:07', '2025-09-14 16:40:07'),
(2, 'Atef Akl88', 'atefakl80@gmail.com', NULL, NULL, NULL, NULL, '2025-09-14 17:15:58', '2025-09-14 17:15:58'),
(3, 'Atef Akl880000', 'atefakl80@gmail.com', NULL, NULL, NULL, NULL, '2025-09-14 17:18:03', '2025-09-14 17:18:03');

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

DROP TABLE IF EXISTS `responses`;
CREATE TABLE IF NOT EXISTS `responses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `survey_id` bigint UNSIGNED NOT NULL,
  `respondent_id` bigint UNSIGNED DEFAULT NULL,
  `score` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `responses_survey_id_foreign` (`survey_id`),
  KEY `responses_respondent_id_foreign` (`respondent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`id`, `survey_id`, `respondent_id`, `score`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 2.00, '2025-09-14 16:40:07', '2025-09-14 16:40:07'),
(2, 4, 2, 2.00, '2025-09-14 17:15:58', '2025-09-14 17:15:58'),
(3, 5, 3, 2.00, '2025-09-14 17:18:03', '2025-09-14 17:18:03');

-- --------------------------------------------------------

--
-- Table structure for table `surveys`
--

DROP TABLE IF EXISTS `surveys`;
CREATE TABLE IF NOT EXISTS `surveys` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `survey_number` int NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('survey','quiz') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'survey',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `surveys_title_unique` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surveys`
--

INSERT INTO `surveys` (`id`, `survey_number`, `title`, `description`, `type`, `is_published`, `is_active`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, 'استطلاع رضا العملاء', 'استطلاع لقياس مستوى رضا العملاء عن الخدمات المقدمة', 'survey', 1, 1, NULL, '2025-09-14 16:04:57', '2025-09-14 16:06:54'),
(2, 2, 'اختبار المعرفة العامة', 'اختبار لقياس المعرفة العامة في مختلف المجالات', 'quiz', 1, 1, NULL, '2025-09-14 16:04:57', '2025-09-14 17:16:45'),
(3, 3, 'استطلاع تقييم الموقع', 'استطلاع لتقييم سهولة استخدام الموقع الإلكتروني', 'survey', 1, 0, NULL, '2025-09-14 16:04:57', '2025-09-14 16:04:57'),
(4, 4, 'New Survey', 'Survey description', 'survey', 1, 1, NULL, '2025-09-14 16:39:40', '2025-09-14 16:39:47'),
(5, 5, 'كوز', 'Survey description', 'quiz', 1, 1, NULL, '2025-09-14 17:17:29', '2025-09-14 17:17:36'),
(6, 6, 'New Survey2', 'Survey description', 'survey', 0, 1, NULL, '2025-09-14 17:21:59', '2025-09-14 17:22:09'),
(7, 7, 'New Surveyض', 'Survey description', 'survey', 1, 1, NULL, '2025-09-14 17:31:35', '2025-09-14 17:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `account_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
