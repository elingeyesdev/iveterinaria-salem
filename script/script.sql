-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2026 at 05:42 PM
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
-- Database: `proyectoprueba`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab', 'i:2;', 1793074337),
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1793074337;', 1793074337),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:3;', 1793077839),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1793077839;', 1793077839);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cliente`
--

CREATE TABLE `cliente` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cliente`
--

INSERT INTO `cliente` (`id`, `nombre`, `apellido`, `direccion`, `correo`, `telefono`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'jesus', 'cabrera', '8vo anillo', 'susej.co2016@gmail.com', '78588886', 1, '2026-10-18 01:24:16', '2026-10-18 01:24:16'),
(2, 'jimena', 'churata borges', '8vo anillo cambodromo', 'jimena@gmail.com', '60955559', 1, '2026-10-18 01:24:43', '2026-10-18 01:24:43'),
(3, 'ronald', 'churata borges', 'la pampa de la isla', 'ronald@gmail.com', '99999999', 1, '2026-10-18 01:25:08', '2026-10-18 01:25:08'),
(4, 'jose jose', 'luis', 'villa 1ero de mayo', 'jose@gmail.com', '77777777', 1, '2026-10-25 23:13:54', '2026-10-25 23:13:54'),
(5, 'aquiles', 'cabrera romay', '8vo anillo olimpica', 'aquiles@gmail.com', '56674535', 1, '2026-10-26 00:06:50', '2026-10-26 00:06:50'),
(6, 'raul', 'mamani mamani', 'la cuchilla', 'raul@gmail.com', '77777779', 1, '2026-10-27 04:09:47', '2026-10-27 04:09:47'),
(7, 'tontin', 'tonto tonto', 'mamaracho', 'tontin@gmail.com', '76565676', 1, '2026-10-27 04:27:28', '2026-10-27 04:27:28');

-- --------------------------------------------------------

--
-- Table structure for table `consulta`
--

CREATE TABLE `consulta` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('Reserva','Emergencia') NOT NULL DEFAULT 'Reserva',
  `motivo` varchar(255) DEFAULT NULL,
  `diagnostico` varchar(255) DEFAULT NULL,
  `tratamiento` varchar(255) DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `estado_clinico` enum('En atención','Finalizada') NOT NULL DEFAULT 'En atención',
  `finalizada_at` timestamp NULL DEFAULT NULL,
  `reserva_id` bigint(20) UNSIGNED DEFAULT NULL,
  `veterinario_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consulta`
--

INSERT INTO `consulta` (`id`, `fecha`, `tipo`, `motivo`, `diagnostico`, `tratamiento`, `peso`, `estado_clinico`, `finalizada_at`, `reserva_id`, `veterinario_id`, `created_at`, `updated_at`) VALUES
(7, '2026-10-20', 'Reserva', 'v1:MVvsPenwmU/gKNn6BjwynuSXOYL77zhBXHLwPZ0tkByM5rk=', 'v1:SQko20mQs1pfiz8LkVZ3eYZGZcUlnv3E2lROAw1WQ0RGF2s=', 'v1:HklSXklytsJPNvsL6oinQrankU56asrxvsMKyRYzNeWzc9A=', 5.00, 'Finalizada', '2026-10-19 23:41:40', 11, 1, '2026-10-19 23:41:16', '2026-10-19 23:41:40'),
(8, '2026-10-19', 'Reserva', 'v1:LOZfw7LPyQ0nXntTeDVwQMedDZS9X3TzaH9UEDqKuIaZRw==', 'v1:J7sXSdPVDjGGFRMLx9bcX/fFCImRrGxEsnwAkWLh380CQA==', 'v1:nlJWoHSLKwcy+p5if62QkEbtMxCN530fMXkoxRRyNOr4p38=', 3.00, 'Finalizada', '2026-10-20 06:36:10', 9, 1, '2026-10-20 02:42:57', '2026-10-20 06:36:10'),
(9, '2026-10-20', 'Reserva', 'v1:z7yzCiyDtqmK1W0yBmH7gjHDTSqDNvt//IgA48fUyPfXojOF0C5H', 'v1:e9cU0NVByi+zzAxXw92+x/urZ5jQKOhMNNc05NfoL2+0VswH3UQ=', 'v1:Q9WVp1lS0hgN7hksnX57ugV04N22dAgle7hjh2jaLogyHDSLkM0D', 3.70, 'Finalizada', '2026-10-20 05:34:46', 10, 1, '2026-10-20 05:34:37', '2026-10-20 05:34:46'),
(10, '2026-10-21', 'Reserva', 'v1:pupCZ2Ye/mT/uQvoE6qaXBpyBpo7Rp3MrffQPm4L4fGnZug=', 'v1:ihFbXFHmUgkbif34zeF812oZSHpi188YInP2R6gFJbBKu7A=', 'v1:B1CyIuzWOM6shw+zwLngEMhNfqyfeErEDl7AOBT15f+T0AlaKL0=', 5.00, 'Finalizada', '2026-10-20 06:39:22', 12, 1, '2026-10-20 06:31:43', '2026-10-20 06:39:22'),
(11, '2026-10-21', 'Reserva', 'v1:3yb3Ge+lxWUs9wHVaQ/RLlufQrsRLL/BuBSuWdcXypI=', 'v1:VVIelE4TMaATmfHHD+nykMHCfhip7uIdacSyYe1q5Q4=', 'v1:RrhYudA7chnxREOOx6UvRhP2PNdTxNDTPrnncf5VHw==', 4.00, 'Finalizada', '2026-10-21 04:49:36', 13, 1, '2026-10-21 04:48:49', '2026-10-21 04:49:36'),
(12, '2026-10-21', 'Reserva', 'v1:6ulIb9eXwMSXtO0scFZy3wQzDM7izyGVlQm6BytimwuP4op2EQbZZzw=', 'v1:2Z6DrK8jBZy6pC9/tyqjFHDsb7Iigl2mqcniXyhUtVnqEK7BV5cdXitjLA==', 'v1:cqyZrFxASdO/vlr1QpMc6Bhv0whvKlhVETO/AUHQ8TLM6CphjRYiUKfgxKlQ', 10.00, 'Finalizada', '2026-10-21 05:07:17', 14, 1, '2026-10-21 05:06:56', '2026-10-21 05:07:17'),
(13, '2026-10-22', 'Reserva', 'v1:cauNqLj+rMKpskT3atHP/a6sAL4zR0GzfYk+HWB0N3Cqfw==', 'v1:sC9G6ReA/WLs9+ifVgnQOxCNGYfnaxjm1kGXQI5s7yM5VIDGvCGBA/55rRU=', 'v1:36Ukb0j9m0og13I/69RbTsitC/0nvyUB4EQbAdn0MesNAwrBZha6qn6wAYNu34K/', 4.00, 'Finalizada', '2026-10-21 05:25:23', 15, 1, '2026-10-21 05:20:48', '2026-10-21 05:25:23'),
(14, '2026-10-22', 'Reserva', 'v1:i4qfnnGMx3jDc0JZfyLAv7Eurj1/ZyveJclvfHFvIkc52g==', 'v1:TmH7CQ9q7Vuv6pmWgiF++NxXp1lqIjgGPGkxueeOs9ji/98RQf6vMQUzrnA=', 'v1:98GQrwlRk8q9iHZrudsCEARPQky8ku1ds6bk4XCK4Pk7U7K1Opty6C+rRh/Q1olR', 4.00, 'Finalizada', '2026-10-21 05:22:55', 15, 1, '2026-10-21 05:21:26', '2026-10-21 05:22:55'),
(15, '2026-10-23', 'Reserva', 'v1:mO6mNdzT6YYv77gcFgZ2XN0ElsFSNzzJjMZAhHF+bkPFG38tDpc/lLI=', 'v1:k0Vbh7znfrPbP+RyXqhaVNlsNVXproYITjLrHnUpqvnUjxJ9CYuBxPNtZ+c=', 'v1:G56x91XdLRM9g2iFEwJhl1I1f4FhaoWHI1G3ITyUpFNU5hcWrAAbj8xWXQ==', 4.00, 'Finalizada', '2026-10-21 06:04:26', 16, 1, '2026-10-21 06:02:53', '2026-10-21 06:04:26'),
(16, '2026-10-21', 'Reserva', 'v1:J6iRqS/NG7Tu2/dtYgu/6N/IQ9gFsQyrTicqytUVgg==', 'v1:GtTSqw1VB8jxZeLYe5oZw9VKQHI3Mnw0+UfEoL7uoGcopvlpSg==', 'v1:LSOUXBgMErWYPKlLc2pIXgI+ZGNuIYe3/8UKX9nqlYyziuPqhk+qfgeGlYgCVkA=', 3.00, 'Finalizada', '2026-10-21 06:19:18', 17, 1, '2026-10-21 06:19:09', '2026-10-21 06:19:18'),
(17, '2026-10-24', 'Reserva', 'v1:IkQQbKhg1jqm0bI93JShv9CSvM7cjOAV3h4/kNYIeYkudw==', 'v1:KlVRsFopeU9UUV3g5pFISxb6aEV9KvPOmu4ipvrbfQcded4=', 'v1:aJyu09ExOLDTkg4+XBom/K9SwBuIWE6Pt2Sc9fPeVp00BA==', 4.00, 'Finalizada', '2026-10-21 07:25:10', 18, 1, '2026-10-21 07:25:04', '2026-10-21 07:25:10'),
(18, '2026-10-23', 'Reserva', 'v1:Sd4EfbfvxFfMSL45Ij0Mh1DOub7L4lc8moDHlrkamdNy', 'v1:DY2uU29J1femKb9Pwm4NktVXzLNiv4NxsBKWpFt04AtYUQ==', 'v1:nRpeclxKeJgibnmt7DEfDcUXz+A65aNJ3sSEwWaBI9jXIZQToQ==', 4.00, 'Finalizada', '2026-10-23 05:10:34', 19, 1, '2026-10-23 05:10:18', '2026-10-23 05:10:34'),
(19, '2026-10-23', 'Reserva', 'v1:ZXNlAOUUTC9lmrw2l8agQ7zC0r6Rrn0i9SMqivlgmLFHN+bZasSa74BiJ00=', 'v1:F0HhXlRJtWMNC9WS9oo0Fne6AJSyO8ogADqHe/ffbXSgPW7lcb+5e3HViGI=', 'v1:PH+kNMeUV59exmabEJjoE+sBYHzh+jju8Qn1usbROn95VyrGGn5dMqak', 5.00, 'Finalizada', '2026-10-23 05:25:06', 20, 1, '2026-10-23 05:24:56', '2026-10-23 05:25:06'),
(20, '2026-10-26', 'Reserva', 'v1:ZiRZwwAJY+GYDjqDTDblSLYLgJ0PC3m/rntDIGuU3xiuoKU=', 'v1:8dk8jbs9ut9p6eZWORKw493Y2t/KKsMlGseRkno3spNfnYk=', 'v1:UO3wpn4xUPYXhjG/wwyDti4DR+AfRsGqQTYBilLt5uBCddE=', 4.00, 'Finalizada', '2026-10-25 05:11:33', 21, 1, '2026-10-25 05:11:24', '2026-10-25 05:11:33'),
(21, '2026-10-26', 'Reserva', 'v1:2ZUyaVF7qSy+VutWhb+MPTcQK9Be8NMwKrZBx0/+xNzgSwQ=', 'v1:F0h9uNRHo50hM77oygg7kZazVp8TjIq6Owxr/qGqw3L663A=', 'v1:5XPJuwiGuCzj8jg1ldPGKCHV8Wgp+SMOtGDqSKGzw3XEWWw=', 4.00, 'Finalizada', '2026-10-25 23:15:58', 28, 1, '2026-10-25 23:15:38', '2026-10-25 23:15:58'),
(22, '2026-10-26', 'Reserva', 'v1:fRTRjdA2zWUL/SUO4c3aPHeEKU9yhqnROfjMhTsY31v6Cg==', 'v1:P0MvPhf5HOHRX9++uK9+U2IuRMphc+f2Rmba7x0BZLI=', 'v1:tYK5hxbWqgosYOvDcF0Mo9Y2TfazOw79OZT2/AeAq4WMBQ==', 8.00, 'Finalizada', '2026-10-26 00:18:45', 29, 1, '2026-10-26 00:16:29', '2026-10-26 00:18:45');

-- --------------------------------------------------------

--
-- Table structure for table `consulta_servicio`
--

CREATE TABLE `consulta_servicio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha_actual` date NOT NULL,
  `fecha_prox` date DEFAULT NULL,
  `peso` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `servicio_id` bigint(20) UNSIGNED NOT NULL,
  `consulta_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consulta_servicio`
--

INSERT INTO `consulta_servicio` (`id`, `fecha_actual`, `fecha_prox`, `peso`, `precio`, `servicio_id`, `consulta_id`, `created_at`, `updated_at`) VALUES
(16, '2026-10-20', '2026-12-19', 5.00, 150.00, 1, 7, '2026-10-19 23:41:16', '2026-10-19 23:41:16'),
(17, '2026-10-20', NULL, 5.00, 120.00, 3, 7, '2026-10-19 23:41:16', '2026-10-19 23:41:16'),
(18, '2026-10-19', '2026-11-19', 3.00, 150.00, 1, 8, '2026-10-20 02:42:57', '2026-10-20 02:42:57'),
(19, '2026-10-20', NULL, 3.70, 150.00, 1, 9, '2026-10-20 05:34:37', '2026-10-20 05:34:37'),
(20, '2026-10-20', NULL, 3.70, 150.00, 2, 9, '2026-10-20 05:34:37', '2026-10-20 05:34:37'),
(21, '2026-10-20', NULL, 3.70, 120.00, 3, 9, '2026-10-20 05:34:37', '2026-10-20 05:34:37'),
(22, '2026-10-21', NULL, 5.00, 120.00, 3, 10, '2026-10-20 06:31:43', '2026-10-20 06:38:50'),
(23, '2026-10-21', NULL, 4.00, 150.00, 1, 11, '2026-10-21 04:48:49', '2026-10-21 04:48:49'),
(24, '2026-10-21', NULL, 4.00, 120.00, 3, 11, '2026-10-21 04:48:49', '2026-10-21 04:48:49'),
(25, '2026-10-21', NULL, 10.00, 150.00, 2, 12, '2026-10-21 05:06:56', '2026-10-21 05:06:56'),
(26, '2026-10-22', NULL, 4.00, 150.00, 1, 13, '2026-10-21 05:20:48', '2026-10-21 05:25:18'),
(27, '2026-10-22', '2026-11-22', 4.00, 150.00, 1, 14, '2026-10-21 05:21:26', '2026-10-21 05:21:26'),
(28, '2026-10-23', NULL, 4.00, 120.00, 3, 15, '2026-10-21 06:02:53', '2026-10-21 06:04:18'),
(29, '2026-10-23', NULL, 4.00, 150.00, 1, 15, '2026-10-21 06:02:53', '2026-10-21 06:04:18'),
(30, '2026-10-23', NULL, 4.00, 150.00, 2, 15, '2026-10-21 06:02:53', '2026-10-21 06:04:18'),
(31, '2026-10-21', NULL, 3.00, 150.00, 1, 16, '2026-10-21 06:19:09', '2026-10-21 06:19:09'),
(32, '2026-10-24', NULL, 4.00, 150.00, 2, 17, '2026-10-21 07:25:04', '2026-10-21 07:25:04'),
(33, '2026-10-24', NULL, 4.00, 150.00, 1, 17, '2026-10-21 07:25:04', '2026-10-21 07:25:04'),
(34, '2026-10-23', NULL, 4.00, 120.00, 3, 18, '2026-10-23 05:10:18', '2026-10-23 05:10:18'),
(35, '2026-10-23', NULL, 4.00, 150.00, 2, 18, '2026-10-23 05:10:18', '2026-10-23 05:10:18'),
(36, '2026-10-23', NULL, 5.00, 120.00, 3, 19, '2026-10-23 05:24:56', '2026-10-23 05:24:56'),
(37, '2026-10-26', NULL, 4.00, 150.00, 1, 20, '2026-10-25 05:11:24', '2026-10-25 05:11:24'),
(38, '2026-10-26', NULL, 4.00, 150.00, 2, 20, '2026-10-25 05:11:24', '2026-10-25 05:11:24'),
(39, '2026-10-26', NULL, 4.00, 120.00, 3, 21, '2026-10-25 23:15:38', '2026-10-25 23:15:38'),
(40, '2026-10-26', NULL, 8.00, 150.00, 1, 22, '2026-10-26 00:16:29', '2026-10-26 00:18:29'),
(41, '2026-10-26', NULL, 8.00, 150.00, 2, 22, '2026-10-26 00:16:29', '2026-10-26 00:18:29');

-- --------------------------------------------------------

--
-- Table structure for table `especie`
--

CREATE TABLE `especie` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `especie`
--

INSERT INTO `especie` (`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'perro', 1, '2026-10-18 01:21:38', '2026-10-18 01:21:38'),
(2, 'ave', 1, '2026-10-18 01:21:48', '2026-10-18 01:21:48'),
(3, 'gato', 1, '2026-10-18 01:21:59', '2026-10-18 01:21:59'),
(4, 'otros', 1, '2026-10-18 01:22:10', '2026-10-18 01:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
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
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"b5882185-bd2c-40c7-b3d6-388d6637b33d\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792287208,\"delay\":null}', 0, NULL, 1792287208, 1792287208),
(2, 'default', '{\"uuid\":\"a4116de0-3e65-44df-b513-414e3d12076b\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792287232,\"delay\":null}', 0, NULL, 1792287232, 1792287232),
(3, 'default', '{\"uuid\":\"9f189a1e-2ac3-4ee1-b898-cfc947182d4d\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"ronald@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792287290,\"delay\":null}', 0, NULL, 1792287290, 1792287290),
(4, 'default', '{\"uuid\":\"79a98dcb-cde8-476b-8d53-c0024c24f74c\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792379050,\"delay\":null}', 0, NULL, 1792379050, 1792379050),
(5, 'default', '{\"uuid\":\"c5ce6e3f-486b-40a3-aba2-83398d46046e\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792379203,\"delay\":null}', 0, NULL, 1792379203, 1792379203),
(6, 'default', '{\"uuid\":\"2d2e93de-266e-450b-817f-e3540269f6b4\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:6;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"ronald@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792379288,\"delay\":null}', 0, NULL, 1792379288, 1792379288),
(7, 'default', '{\"uuid\":\"8ccc6a2a-0be0-4fd9-8443-76b4ec567ec6\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:7;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"ronald@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792450954,\"delay\":null}', 0, NULL, 1792450954, 1792450954),
(8, 'default', '{\"uuid\":\"bc8c651b-4df7-49c7-b34a-bf1a47ed66d4\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:8;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792450978,\"delay\":null}', 0, NULL, 1792450978, 1792450978),
(9, 'default', '{\"uuid\":\"246a99ea-abb3-44d9-a6f5-04f7ee955962\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:9;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792453093,\"delay\":null}', 0, NULL, 1792453093, 1792453093),
(10, 'default', '{\"uuid\":\"7328d08b-343a-4757-85a1-9feae20feefd\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"ronald@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792453134,\"delay\":null}', 0, NULL, 1792453134, 1792453134),
(11, 'default', '{\"uuid\":\"dddd3b48-cf9e-4193-94e5-a46b51f17163\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:11;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792453202,\"delay\":null}', 0, NULL, 1792453202, 1792453202),
(12, 'default', '{\"uuid\":\"7101d02c-ece7-4eb7-8fe4-af54c4c9ec26\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:12;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792475397,\"delay\":null}', 0, NULL, 1792475397, 1792475397),
(13, 'default', '{\"uuid\":\"7c5d6e0d-78c2-4ebd-a3aa-098bdf521266\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:13;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792479558,\"delay\":null}', 0, NULL, 1792479558, 1792479558),
(14, 'default', '{\"uuid\":\"1fa51b1a-96ce-43c6-8847-bcc61f4ec613\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792479587,\"delay\":null}', 0, NULL, 1792479587, 1792479587),
(15, 'default', '{\"uuid\":\"6729b394-24ae-4fcf-a579-cfb8b19f73a2\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:15;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792559977,\"delay\":null}', 0, NULL, 1792559977, 1792559977),
(16, 'default', '{\"uuid\":\"818a7920-6ffd-4c89-b96e-3e990a16bbb8\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:16;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"ronald@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792562529,\"delay\":null}', 0, NULL, 1792562529, 1792562529),
(17, 'default', '{\"uuid\":\"cd6767e1-4331-44f6-b443-9781d6b470ef\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:17;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792563460,\"delay\":null}', 0, NULL, 1792563460, 1792563460),
(18, 'default', '{\"uuid\":\"dd2f4e6d-970a-413a-952c-9dea25f7d9af\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:18;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"ronald@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792567477,\"delay\":null}', 0, NULL, 1792567477, 1792567477),
(19, 'default', '{\"uuid\":\"c619bae6-320b-495c-9b4d-ee4a700982f9\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:19;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792732169,\"delay\":null}', 0, NULL, 1792732169, 1792732169),
(20, 'default', '{\"uuid\":\"0c4b462c-0b6e-437b-8f0e-959224680efb\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:20;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792733061,\"delay\":null}', 0, NULL, 1792733061, 1792733061),
(21, 'default', '{\"uuid\":\"1452456c-f4dc-414d-bdb1-f755d2cbfcbe\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:21;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792905029,\"delay\":null}', 0, NULL, 1792905029, 1792905029),
(22, 'default', '{\"uuid\":\"3e197f7a-7514-49d8-a5c2-bd5cc3fe3a4c\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:22;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792905049,\"delay\":null}', 0, NULL, 1792905049, 1792905049),
(23, 'default', '{\"uuid\":\"fa1a344a-362e-4076-8cf9-e2ee2c47083f\",\"displayName\":\"App\\\\Mail\\\\VerificacionReservaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:32:\\\"App\\\\Mail\\\\VerificacionReservaMail\\\":3:{s:6:\\\"enlace\\\";s:206:\\\"http:\\/\\/127.0.0.1:8000\\/reservar\\/verificar-correo\\/V8pNdkM6LqChTvvaavKiLfEbIupR8YEKTIk9qMMpcD9U7hQjsjg6O6lfbQsD5iAH?expires=1792906188&signature=d33eda6f355b351c279ca67b473f0cdca427cb6d3d515925eea4ebd87613669f\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792905288,\"delay\":null}', 0, NULL, 1792905288, 1792905288),
(24, 'default', '{\"uuid\":\"6a44592c-14d1-43ad-a19e-ca890b974578\",\"displayName\":\"App\\\\Mail\\\\VerificacionReservaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:32:\\\"App\\\\Mail\\\\VerificacionReservaMail\\\":3:{s:6:\\\"enlace\\\";s:206:\\\"http:\\/\\/127.0.0.1:8000\\/reservar\\/verificar-correo\\/mYgKpJ6N386LzRrIW32B3zT3PUeiQciW7hf6YaIS7FSJJVbTq8vL8Ekx3fkw58F2?expires=1792908541&signature=5d6f676ba203b8b897e3683516f2bb5de19539ae80ccdb2b32d95db6e6b8f01c\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792907642,\"delay\":null}', 0, NULL, 1792907642, 1792907642),
(25, 'default', '{\"uuid\":\"a9a8ae2e-cad1-4e53-9d4e-2555c9d497e5\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:23;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792913424,\"delay\":null}', 0, NULL, 1792913425, 1792913425),
(26, 'default', '{\"uuid\":\"9655f896-ca0d-41ce-8643-1ca5b0c93bee\",\"displayName\":\"App\\\\Mail\\\\VerificacionReservaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:32:\\\"App\\\\Mail\\\\VerificacionReservaMail\\\":3:{s:6:\\\"enlace\\\";s:206:\\\"http:\\/\\/127.0.0.1:8000\\/reservar\\/verificar-correo\\/P2RxADh2shrwwDctWFHLB7LtqkJ281w2UE1YAUbzLwtu2u8v9NkLv6Lue7ekx27C?expires=1792915096&signature=9431998837edfd404142bc25e42def2178763b58aadd0914443744dd09bbdacb\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792914199,\"delay\":null}', 0, NULL, 1792914199, 1792914199),
(27, 'default', '{\"uuid\":\"1813f8aa-70a8-41b6-ace6-571c131b4a13\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:24;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792919175,\"delay\":null}', 0, NULL, 1792919175, 1792919175),
(28, 'default', '{\"uuid\":\"71696efc-49dd-481f-b706-96f7403f31ad\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:25;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"ronald@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792923149,\"delay\":null}', 0, NULL, 1792923149, 1792923149),
(29, 'default', '{\"uuid\":\"69063907-34e3-4ced-acee-8132301dad0d\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:26;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792923451,\"delay\":null}', 0, NULL, 1792923451, 1792923451),
(30, 'default', '{\"uuid\":\"069a34c8-b2c3-4b09-8f00-e64f9afbdb64\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:27;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:16:\\\"jimena@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1792923672,\"delay\":null}', 0, NULL, 1792923672, 1792923672);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(31, 'default', '{\"uuid\":\"9a82d959-c0ae-4de6-befa-f972aa05deea\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:30;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:14:\\\"raul@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1793074305,\"delay\":null}', 0, NULL, 1793074305, 1793074305),
(32, 'default', '{\"uuid\":\"83452e1d-020e-4739-9441-7c27a7ba7be6\",\"displayName\":\"App\\\\Mail\\\\ReservaConfirmadaMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":19:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\ReservaConfirmadaMail\\\":3:{s:7:\\\"reserva\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Reserva\\\";s:2:\\\"id\\\";i:31;s:9:\\\"relations\\\";a:2:{i:0;s:7:\\\"mascota\\\";i:1;s:15:\\\"mascota.cliente\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"susej.co2016@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:15:\\\"uniqueLockOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1793077794,\"delay\":null}', 0, NULL, 1793077794, 1793077794);

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
-- Table structure for table `mascota`
--

CREATE TABLE `mascota` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `especie_id` bigint(20) UNSIGNED NOT NULL,
  `raza_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `fecha_nac` date NOT NULL,
  `peso` double NOT NULL,
  `sexo` enum('Macho','Hembra','No especificado') NOT NULL DEFAULT 'Macho',
  `imagen` varchar(255) DEFAULT NULL,
  `imagen_hash` varchar(64) DEFAULT NULL COMMENT 'A08: Checksum SHA-256 del archivo de imagen para verificación de integridad',
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mascota`
--

INSERT INTO `mascota` (`id`, `cliente_id`, `especie_id`, `raza_id`, `nombre`, `fecha_nac`, `peso`, `sexo`, `imagen`, `imagen_hash`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'estrellita', '2026-04-08', 4, 'Hembra', 'mascotas/a84c1f3caecd1eae34772d824ccb4706.jpg', '4e7321d4ae902f8cd613f8b32c293bd6da2ed86f63af3fdf264f8d2ec5514865', 1, '2026-10-18 01:31:04', '2026-10-23 05:10:18'),
(2, 2, 4, 5, 'fatiga', '2000-10-07', 5, 'Hembra', NULL, NULL, 1, '2026-10-18 01:31:47', '2026-10-23 05:24:56'),
(3, 3, 1, 3, 'panchito', '2025-12-22', 4, 'Macho', NULL, NULL, 1, '2026-10-18 01:32:21', '2026-10-21 06:02:53'),
(4, 4, 1, 4, 'ponisillo', '2026-06-25', 4, 'Hembra', NULL, NULL, 1, '2026-10-25 23:14:35', '2026-10-25 23:15:38'),
(5, 5, 1, 2, 'neron', '2025-10-10', 10, 'Macho', NULL, NULL, 1, '2026-10-26 00:07:43', '2026-10-26 00:07:43'),
(6, 5, 1, 5, 'kiarilla', '2026-10-02', 8, 'Hembra', NULL, NULL, 1, '2026-10-26 00:15:05', '2026-10-26 00:57:57'),
(7, 6, 1, 5, 'negrilla', '2023-06-22', 5, 'Hembra', NULL, NULL, 1, '2026-10-27 04:10:45', '2026-10-27 04:10:45');

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
(17, '2026_10_15_120229_create_consulta_table', 1),
(19, '2026_10_15_123024_create_consulta_servicio_table', 1),
(22, '2026_10_15_123300_add_consulta_servicio_fields_to_recordatorio_table', 1),
(61, '0001_01_01_000000_create_users_table', 2),
(62, '0001_01_01_000001_create_cache_table', 2),
(63, '0001_01_01_000002_create_jobs_table', 2),
(64, '2026_06_08_142920_create_roles_table', 2),
(65, '2026_06_08_143714_create_role_user_table', 2),
(66, '2026_06_18_144535_create_cliente_table', 2),
(67, '2026_06_18_145124_create_veterinario_table', 2),
(68, '2026_07_28_031947_create_personal_access_tokens_table', 2),
(69, '2026_08_22_222229_create_raza_table', 2),
(70, '2026_08_22_222239_create_especie_table', 2),
(71, '2026_08_22_222246_create_mascota_table', 2),
(72, '2026_09_02_100511_create_reserva_table', 2),
(73, '2026_09_11_222247_create_sub_servicio_table', 2),
(74, '2026_10_15_060000_create_permisos_table', 2),
(75, '2026_10_15_060100_create_permiso_user_table', 2),
(76, '2026_10_15_114155_create_servicio_table', 2),
(77, '2026_10_15_123100_create_recordatorio_table', 2),
(78, '2026_10_15_123200_add_reserva_email_fields_to_recordatorio_table', 2),
(79, '2026_10_17_182506_create_consulta_table', 2),
(80, '2026_10_17_182607_create_consulta_servicio_table', 2),
(81, '2026_10_17_183346_create_consulta_servicio_fields_to_recordatorio_table', 2),
(82, '2026_10_17_204756_create_pago_table', 2),
(83, '2026_10_18_222942_create_pago_table', 3),
(84, '2026_10_19_000001_convertir_pago_a_efectivo_y_estado_enum', 4),
(85, '2026_10_19_230854_create_pago_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `pago`
--

CREATE TABLE `pago` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` enum('Pendiente','Pagado','Anulado') NOT NULL DEFAULT 'Pendiente',
  `consulta_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pago`
--

INSERT INTO `pago` (`id`, `monto`, `estado`, `consulta_id`, `created_at`, `updated_at`) VALUES
(1, 420.00, 'Pagado', 9, '2026-10-20 05:58:45', '2026-10-20 05:58:45'),
(2, 270.00, 'Pagado', 7, '2026-10-20 06:26:35', '2026-10-20 06:26:35'),
(3, 150.00, 'Pagado', 8, '2026-10-20 06:27:29', '2026-10-20 06:27:29'),
(4, 120.00, 'Anulado', 10, '2026-10-21 04:47:16', '2026-10-21 05:05:09'),
(5, 120.00, 'Pagado', 10, '2026-10-21 05:05:40', '2026-10-21 05:05:40'),
(6, 420.00, 'Pagado', 15, '2026-10-21 06:04:26', '2026-10-21 06:15:40'),
(7, 150.00, 'Pagado', 16, '2026-10-21 06:19:18', '2026-10-21 07:03:47'),
(8, 300.00, 'Anulado', 17, '2026-10-21 07:25:10', '2026-10-21 19:03:44'),
(9, 300.00, 'Pagado', 17, '2026-10-21 19:04:04', '2026-10-21 19:04:04'),
(10, 270.00, 'Pagado', 18, '2026-10-23 05:10:34', '2026-10-23 05:12:44'),
(11, 120.00, 'Pagado', 19, '2026-10-23 05:25:06', '2026-10-23 06:09:41'),
(12, 300.00, 'Pagado', 20, '2026-10-25 05:11:33', '2026-10-25 05:13:04'),
(13, 120.00, 'Pagado', 21, '2026-10-25 23:15:58', '2026-10-26 00:31:50'),
(14, 300.00, 'Pagado', 22, '2026-10-26 00:18:45', '2026-10-26 00:32:05');

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
-- Table structure for table `permisos`
--

CREATE TABLE `permisos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clave` varchar(80) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permisos`
--

INSERT INTO `permisos` (`id`, `clave`, `nombre`, `modulo`, `descripcion`, `orden`, `created_at`, `updated_at`) VALUES
(1, 'clientes', 'Administrar clientes', 'Parametrización', 'Ver, registrar y editar clientes.', 1, '2026-10-20 06:25:16', '2026-10-20 06:25:16'),
(2, 'veterinarios', 'Administrar veterinarios', 'Parametrización', 'Ver, registrar y editar veterinarios.', 2, '2026-10-20 06:25:17', '2026-10-20 06:25:17'),
(3, 'especies', 'Administrar especies', 'Parametrización', 'Ver, registrar y editar especies.', 3, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(4, 'razas', 'Administrar razas', 'Parametrización', 'Ver, registrar y editar razas.', 4, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(5, 'mascotas', 'Administrar mascotas', 'Parametrización', 'Ver, registrar y editar mascotas.', 5, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(6, 'subservicios', 'Administrar subservicios', 'Parametrización', 'Ver, registrar y editar subservicios.', 6, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(7, 'servicios', 'Administrar servicios y precios', 'Parametrización', 'Ver, registrar y editar servicios, incluido su precio.', 7, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(8, 'reservas', 'Administrar reservas', 'Transaccional', 'Ver el calendario, registrar, editar y cambiar el estado de reservas.', 8, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(9, 'consultas', 'Consultas y emergencias', 'Transaccional', 'Registrar y editar consultas, atención rápida/emergencias y monitor médico.', 9, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(10, 'pagos', 'Cobrar consultas', 'Transaccional', 'Registrar pagos de una consulta. Anular pagos sigue siendo exclusivo del administrador.', 10, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(11, 'recordatorios', 'Recordatorios', 'Transaccional', 'Ver y gestionar recordatorios de vacunas y controles.', 11, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(12, 'reportes.ingresos', 'Reporte de ingresos', 'Reportes', 'Dinero cobrado por período y método de pago (información financiera).', 12, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(13, 'reportes.servicios', 'Reporte de servicios', 'Reportes', 'Servicios más solicitados.', 13, '2026-10-20 06:25:18', '2026-10-20 06:25:18'),
(14, 'reportes.veterinarios', 'Reporte de veterinarios', 'Reportes', 'Productividad por veterinario.', 14, '2026-10-20 06:25:19', '2026-10-20 06:25:19'),
(15, 'reportes.reservas', 'Reporte de reservas y asistencia', 'Reportes', 'Reservas por estado, asistencia y horas de mayor demanda.', 15, '2026-10-20 06:25:19', '2026-10-20 06:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `permiso_user`
--

CREATE TABLE `permiso_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `permiso_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permiso_user`
--

INSERT INTO `permiso_user` (`id`, `user_id`, `permiso_id`, `created_at`, `updated_at`) VALUES
(1, 2, 8, '2026-10-20 06:25:51', '2026-10-20 06:25:51'),
(2, 2, 9, '2026-10-20 06:25:51', '2026-10-20 06:25:51'),
(3, 2, 10, '2026-10-20 06:25:51', '2026-10-20 06:25:51'),
(4, 2, 12, '2026-10-20 06:25:51', '2026-10-20 06:25:51'),
(5, 2, 13, '2026-10-20 06:25:52', '2026-10-20 06:25:52'),
(6, 2, 15, '2026-10-20 06:25:52', '2026-10-20 06:25:52');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `raza`
--

CREATE TABLE `raza` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `raza`
--

INSERT INTO `raza` (`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'yorkie', 1, '2026-10-18 01:20:49', '2026-10-18 01:23:06'),
(2, 'boxer', 1, '2026-10-18 01:20:57', '2026-10-18 01:22:49'),
(3, 'salchicha', 1, '2026-10-18 01:21:07', '2026-10-18 01:22:40'),
(4, 'GOLDEN', 1, '2026-10-18 01:21:16', '2026-10-18 01:22:26'),
(5, 'otros', 1, '2026-10-18 01:23:15', '2026-10-18 01:23:15');

-- --------------------------------------------------------

--
-- Table structure for table `recordatorio`
--

CREATE TABLE `recordatorio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mascota_id` bigint(20) UNSIGNED NOT NULL,
  `reserva_id` bigint(20) UNSIGNED DEFAULT NULL,
  `consulta_id` bigint(20) UNSIGNED DEFAULT NULL,
  `servicio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo` enum('Vacunación','Desparasitación','Control veterinario','Baño/Peluquería','Limpieza dental','Otro') NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  `enviado_at` datetime DEFAULT NULL,
  `canal` varchar(30) NOT NULL DEFAULT 'correo',
  `error_envio` text DEFAULT NULL,
  `estado` enum('Pendiente','Completado','Cancelado') NOT NULL DEFAULT 'Pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reserva`
--

CREATE TABLE `reserva` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `mascota_id` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('Pendiente','Atendida','Cancelado','No asistio') NOT NULL DEFAULT 'Pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reserva`
--

INSERT INTO `reserva` (`id`, `fecha`, `hora_inicio`, `hora_fin`, `mascota_id`, `estado`, `created_at`, `updated_at`) VALUES
(9, '2026-10-20', '10:00:00', '10:20:00', 1, 'Atendida', '2026-10-19 23:38:13', '2026-10-20 02:42:57'),
(10, '2026-10-20', '11:00:00', '11:20:00', 3, 'Atendida', '2026-10-19 23:38:54', '2026-10-20 05:34:37'),
(11, '2026-10-20', '09:20:00', '09:40:00', 2, 'Atendida', '2026-10-19 23:40:02', '2026-10-19 23:41:16'),
(12, '2026-10-21', '10:00:00', '10:20:00', 1, 'Atendida', '2026-10-20 05:49:56', '2026-10-20 06:31:43'),
(13, '2026-10-21', '15:00:00', '15:20:00', 1, 'Atendida', '2026-10-20 06:59:17', '2026-10-21 04:48:49'),
(14, '2026-10-21', '16:00:00', '16:20:00', 2, 'Atendida', '2026-10-20 06:59:47', '2026-10-21 05:06:56'),
(15, '2026-10-22', '10:00:00', '10:20:00', 2, 'Atendida', '2026-10-21 05:19:36', '2026-10-21 05:20:48'),
(16, '2026-10-23', '15:00:00', '15:20:00', 3, 'Atendida', '2026-10-21 06:02:08', '2026-10-21 06:02:53'),
(17, '2026-10-22', '15:00:00', '15:20:00', 1, 'Atendida', '2026-10-21 06:17:40', '2026-10-21 06:19:09'),
(18, '2026-10-24', '10:00:00', '10:20:00', 3, 'Atendida', '2026-10-21 07:24:37', '2026-10-21 07:25:04'),
(19, '2026-10-23', '10:00:00', '10:20:00', 1, 'Atendida', '2026-10-23 05:09:29', '2026-10-23 05:10:18'),
(20, '2026-10-23', '11:00:00', '11:20:00', 2, 'Atendida', '2026-10-23 05:24:20', '2026-10-23 05:24:56'),
(21, '2026-10-26', '10:00:00', '10:20:00', 1, 'Atendida', '2026-10-25 05:10:29', '2026-10-25 05:11:24'),
(22, '2026-10-26', '15:00:00', '15:20:00', 2, 'Cancelado', '2026-10-25 05:10:49', '2026-10-25 10:11:14'),
(23, '2026-10-26', '16:00:00', '16:20:00', 2, 'Cancelado', '2026-10-25 07:30:09', '2026-10-25 10:10:32'),
(24, '2026-10-26', '17:00:00', '17:20:00', 1, 'No asistio', '2026-10-25 09:06:15', '2026-10-27 04:08:23'),
(25, '2026-10-26', '15:20:00', '15:40:00', 3, 'Cancelado', '2026-10-25 10:12:28', '2026-10-25 10:17:05'),
(26, '2026-10-26', '14:40:00', '15:00:00', 2, 'Cancelado', '2026-10-25 10:17:31', '2026-10-25 10:18:23'),
(27, '2026-10-26', '15:40:00', '16:00:00', 2, 'Cancelado', '2026-10-25 10:21:12', '2026-10-25 10:22:17'),
(28, '2026-10-26', '09:00:00', '09:20:00', 4, 'Atendida', '2026-10-25 23:14:46', '2026-10-25 23:15:38'),
(29, '2026-10-26', '09:20:00', '09:40:00', 6, 'Atendida', '2026-10-26 00:15:10', '2026-10-26 00:16:29'),
(30, '2026-10-27', '10:00:00', '10:20:00', 7, 'No asistio', '2026-10-27 04:11:45', '2026-10-27 22:01:29'),
(31, '2026-10-27', '15:00:00', '15:20:00', 1, 'No asistio', '2026-10-27 05:09:54', '2026-10-27 22:01:29');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'admin', 1, '2026-10-18 01:17:42', '2026-10-18 01:17:42'),
(2, 'veterinario', 1, '2026-10-18 01:20:08', '2026-10-18 01:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `servicio`
--

CREATE TABLE `servicio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `sub_servicio_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `servicio`
--

INSERT INTO `servicio` (`id`, `nombre`, `descripcion`, `precio`, `estado`, `sub_servicio_id`, `created_at`, `updated_at`) VALUES
(1, 'vacuna antirrabica', NULL, 150.00, 1, 1, '2026-10-18 01:28:55', '2026-10-18 01:28:55'),
(2, 'netgard', 'via oral', 150.00, 1, 2, '2026-10-18 01:29:30', '2026-10-18 01:29:30'),
(3, 'baño y peluqueria', NULL, 120.00, 1, 3, '2026-10-18 01:30:20', '2026-10-18 01:30:20');

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
('ATsM1xim4CBtfpHA8DsEXBPNZWVE2zGChmy2T7zN', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ1YndtRk9rbVVISXhWUkFHbUhBbjhuSXJySFlFQU4yVVBlY2JLQ0tEIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvY29uc3VsdGFcL2NyZWF0ZSIsInJvdXRlIjoiY29uc3VsdGEuY3JlYXRlIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1793138502),
('JaWV7arn7VHdWjmOs1jbvKdQSBV6BHpnB6ZrSgF3', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJwdFBVZEV1ZnpKWFplUUJTYXQxeHJzSFNTSUpvRHRBSDhOSkREcnR2IiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvbWFzY290YVwvMT9zZWNjaW9uPWhpc3RvcmlhbCIsInJvdXRlIjoibWFzY290YS5zaG93In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1793205662),
('kceNLmSMqv0dtC1rvFoQXIotHR0wa4TZWHxdoHID', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJQc2ZiUDYwS0VNQUUybnpidUVQVlpMcXRXS2syUmxLQXg3ZFdmYzJKIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3Jlc2VydmFcL2NyZWF0ZSIsInJvdXRlIjoicmVzZXJ2YS5jcmVhdGUifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYzg0OWViNzZiZTEzZTZmZmM4NTk5ZjVlNDM0Mzg4MzkyYzM3YzI0MmJkMzU2ZDY1OTQyMTQxZjM3M2RhYjA5ZCIsImF1dGgiOnsicGFzc3dvcmRfY29uZmlybWVkX2F0IjoxNzkzMDgxOTE0fX0=', 1793084861),
('skawM7saBb8PStEvNnHtAkxwSsr3AMDaNVlZPdpz', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJjd0wzcXluNXlPUHNuWDhZbTd2M3owTGVUZmk3cnNmVWoyQ2ozQjNQIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9yZXNlcnZhciIsInJvdXRlIjoicmVzZXJ2YS5wdWJsaWNhIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1793085173);

-- --------------------------------------------------------

--
-- Table structure for table `sub_servicio`
--

CREATE TABLE `sub_servicio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_servicio`
--

INSERT INTO `sub_servicio` (`id`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'VACUNA', 'vacuna contra alguna enfermedad', 1, '2026-10-18 01:27:14', '2026-10-18 01:27:14'),
(2, 'DESPARACITACION', 'desparacita via oral', 1, '2026-10-18 01:27:30', '2026-10-18 01:27:30'),
(3, 'PELUQUERIA', 'peluqueada mas baño', 1, '2026-10-18 01:27:48', '2026-10-18 01:27:48'),
(4, 'CIRUGIA', 'operacion', 1, '2026-10-18 01:28:00', '2026-10-18 01:28:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `estado`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'susej.co2016@gmail.com', NULL, '$2y$12$T2NneIIdNjPm5/IdTqV5LeGy2GIfbbtwQLkS3tX5AQkiZg3KYOPsW', 1, 'NYArHP4iMx29lfn5rK5Z69deM3TRyDhWSReJn7OsGIFPJiQyRnumRXLevHGs', '2026-10-18 01:17:43', '2026-10-23 06:39:27'),
(2, 'jimena churata borges', 'jimena@gmail.com', NULL, '$2y$12$BgbyjjGJ7t78KT81otgCVOXpBh0F0aoKTi8yBEiO2QGIdbZdcuw5m', 1, NULL, '2026-10-18 01:19:37', '2026-10-25 05:14:19');

-- --------------------------------------------------------

--
-- Table structure for table `veterinario`
--

CREATE TABLE `veterinario` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `veterinario`
--

INSERT INTO `veterinario` (`id`, `nombre`, `apellido`, `direccion`, `correo`, `telefono`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'yesica', 'mamani', 'viva blooming', 'yesica@gmail.com', '99999999', 1, '2026-10-18 01:32:49', '2026-10-18 01:32:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cliente_correo_unique` (`correo`);

--
-- Indexes for table `consulta`
--
ALTER TABLE `consulta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consulta_reserva_id_foreign` (`reserva_id`),
  ADD KEY `consulta_veterinario_id_foreign` (`veterinario_id`);

--
-- Indexes for table `consulta_servicio`
--
ALTER TABLE `consulta_servicio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `consulta_servicio_servicio_id_consulta_id_unique` (`servicio_id`,`consulta_id`),
  ADD KEY `consulta_servicio_consulta_id_foreign` (`consulta_id`);

--
-- Indexes for table `especie`
--
ALTER TABLE `especie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `especie_nombre_unique` (`nombre`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

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
-- Indexes for table `mascota`
--
ALTER TABLE `mascota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mascota_nombre_cliente_id_unique` (`nombre`,`cliente_id`),
  ADD KEY `mascota_cliente_id_foreign` (`cliente_id`),
  ADD KEY `mascota_especie_id_foreign` (`especie_id`),
  ADD KEY `mascota_raza_id_foreign` (`raza_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pago_consulta_id_foreign` (`consulta_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permisos_clave_unique` (`clave`);

--
-- Indexes for table `permiso_user`
--
ALTER TABLE `permiso_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permiso_user_user_id_permiso_id_unique` (`user_id`,`permiso_id`),
  ADD KEY `permiso_user_permiso_id_foreign` (`permiso_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `raza`
--
ALTER TABLE `raza`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `raza_nombre_unique` (`nombre`);

--
-- Indexes for table `recordatorio`
--
ALTER TABLE `recordatorio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recordatorio_reserva_id_unique` (`reserva_id`),
  ADD KEY `recordatorio_mascota_id_foreign` (`mascota_id`),
  ADD KEY `recordatorio_fecha_estado_index` (`fecha`,`estado`),
  ADD KEY `recordatorio_fecha_envio_enviado_at_estado_index` (`fecha_envio`,`enviado_at`,`estado`),
  ADD KEY `recordatorio_servicio_id_foreign` (`servicio_id`),
  ADD KEY `recordatorio_consulta_id_servicio_id_index` (`consulta_id`,`servicio_id`);

--
-- Indexes for table `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reserva_fecha_hora_inicio_unique` (`fecha`,`hora_inicio`),
  ADD KEY `reserva_mascota_id_foreign` (`mascota_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_user_user_id_foreign` (`user_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `servicio_nombre_unique` (`nombre`),
  ADD KEY `servicio_sub_servicio_id_foreign` (`sub_servicio_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sub_servicio`
--
ALTER TABLE `sub_servicio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sub_servicio_nombre_unique` (`nombre`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `veterinario`
--
ALTER TABLE `veterinario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `veterinario_correo_unique` (`correo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `consulta`
--
ALTER TABLE `consulta`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `consulta_servicio`
--
ALTER TABLE `consulta_servicio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `especie`
--
ALTER TABLE `especie`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `mascota`
--
ALTER TABLE `mascota`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `pago`
--
ALTER TABLE `pago`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `permiso_user`
--
ALTER TABLE `permiso_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `raza`
--
ALTER TABLE `raza`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recordatorio`
--
ALTER TABLE `recordatorio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sub_servicio`
--
ALTER TABLE `sub_servicio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `veterinario`
--
ALTER TABLE `veterinario`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consulta`
--
ALTER TABLE `consulta`
  ADD CONSTRAINT `consulta_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `consulta_veterinario_id_foreign` FOREIGN KEY (`veterinario_id`) REFERENCES `veterinario` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `consulta_servicio`
--
ALTER TABLE `consulta_servicio`
  ADD CONSTRAINT `consulta_servicio_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `consulta_servicio_servicio_id_foreign` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `mascota`
--
ALTER TABLE `mascota`
  ADD CONSTRAINT `mascota_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mascota_especie_id_foreign` FOREIGN KEY (`especie_id`) REFERENCES `especie` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mascota_raza_id_foreign` FOREIGN KEY (`raza_id`) REFERENCES `raza` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `permiso_user`
--
ALTER TABLE `permiso_user`
  ADD CONSTRAINT `permiso_user_permiso_id_foreign` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permiso_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recordatorio`
--
ALTER TABLE `recordatorio`
  ADD CONSTRAINT `recordatorio_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consulta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recordatorio_mascota_id_foreign` FOREIGN KEY (`mascota_id`) REFERENCES `mascota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recordatorio_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `recordatorio_servicio_id_foreign` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_mascota_id_foreign` FOREIGN KEY (`mascota_id`) REFERENCES `mascota` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `servicio_sub_servicio_id_foreign` FOREIGN KEY (`sub_servicio_id`) REFERENCES `sub_servicio` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
