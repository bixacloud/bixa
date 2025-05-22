-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 21, 2025 at 05:21 PM
-- Server version: 8.0.39-cll-lve
-- PHP Version: 8.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fhostbiz_my`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

CREATE TABLE `advertisements` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `html_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `slot_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clicks` int NOT NULL DEFAULT '0',
  `impressions` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ad_slots`
--

CREATE TABLE `ad_slots` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('predefined','dynamic') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'predefined',
  `selector` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` enum('before','after','prepend','append') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `allowed_domains`
--

CREATE TABLE `allowed_domains` (
  `id` bigint UNSIGNED NOT NULL,
  `domain_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `authentication_log`
--

CREATE TABLE `authentication_log` (
  `id` bigint UNSIGNED NOT NULL,
  `authenticatable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authenticatable_id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `login_at` timestamp NULL DEFAULT NULL,
  `login_successful` tinyint(1) NOT NULL DEFAULT '0',
  `logout_at` timestamp NULL DEFAULT NULL,
  `cleared_by_user` tinyint(1) NOT NULL DEFAULT '0',
  `location` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ;

-- --------------------------------------------------------

--
-- Table structure for table `auth_log_settings`
--

CREATE TABLE `auth_log_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `new_device_notification` tinyint(1) DEFAULT '0',
  `failed_login_notification` tinyint(1) DEFAULT '0',
  `location_tracking` tinyint(1) DEFAULT '1',
  `language_detection` tinyint(1) NOT NULL DEFAULT '1',
  `save_user_agent` tinyint(1) NOT NULL DEFAULT '1',
  `retention_days` int NOT NULL DEFAULT '90',
  `geoip_update_frequency` int NOT NULL DEFAULT '30',
  `geoip_license_key` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `geoip_last_update` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_ticket`
--

CREATE TABLE `category_ticket` (
  `category_id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dns_validation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `private_key` text COLLATE utf8mb4_unicode_ci,
  `certificate` text COLLATE utf8mb4_unicode_ci,
  `csr` text COLLATE utf8mb4_unicode_ci,
  `ca_certificate` text COLLATE utf8mb4_unicode_ci,
  `valid_until` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `validation_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_validation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ;

-- --------------------------------------------------------

--
-- Table structure for table `cloudflare_configs`
--

CREATE TABLE `cloudflare_configs` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `api_key` text COLLATE utf8mb4_general_ci NOT NULL,
  `proxy_domain` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hosting_accounts`
--

CREATE TABLE `hosting_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_domain` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sql_server` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cpanel_verified` tinyint(1) DEFAULT '0',
  `cpanel_verified_at` timestamp NULL DEFAULT NULL,
  `admin_deactivated` tinyint(1) DEFAULT '0',
  `admin_deactivation_reason` text COLLATE utf8mb4_unicode_ci,
  `admin_deactivated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `icon_captcha_settings`
--

CREATE TABLE `icon_captcha_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `iperf_servers`
--

CREATE TABLE `iperf_servers` (
  `id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int NOT NULL DEFAULT '8080',
  `country_code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `iperf_servers`
--

INSERT INTO `iperf_servers` (`id`, `ip_address`, `port`, `country_code`, `country_name`, `provider`, `last_checked_at`, `is_active`, `created_at`, `updated_at`) VALUES
(1611, '41.110.39.130', 8080, 'DZ', 'DZ', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1612, '213.158.175.240', 8080, 'EG', 'EG', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1613, '102.214.66.19', 8080, 'GN', 'GN', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1614, '102.214.66.39', 8080, 'GN', 'GN', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1615, '105.235.237.2', 5201, 'GQ', 'GQ', 'Guineanet', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1616, 'speedtestfl.telecom.mu', 5201, 'MU', 'MU', 'Mauritius Telecom', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1617, 'speedtest.telecom.mu', 5201, 'MU', 'MU', 'Mauritius Telecom', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1618, '197.26.19.243', 9200, 'TN', 'TN', 'Orange.tn', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1619, '41.226.22.119', 9202, 'TN', 'TN', 'Topnet', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1620, '41.210.185.162', 8080, 'UG', 'UG', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1621, '169.150.238.161', 8080, 'ZA', 'ZA', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1622, '84.17.57.129', 8080, 'HK', 'HK', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1623, 'speedtest.hkg12.hk.leaseweb.net', 5201, 'HK', 'HK', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1624, '103.185.255.183', 5201, 'ID', 'ID', 'SNT', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1625, 'speedtest.myrepublic.net.id', 9200, 'ID', 'ID', 'MyRepublic', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1626, 'speed.netfiber.net.il', 8080, 'IL', 'IL', 'Netfiber', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1627, 'speed.rimon.net.il', 8080, 'IL', 'IL', 'Rimon', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1628, '169.150.202.193', 8080, 'IL', 'IL', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1629, 'lg-in-mum.webhorizon.net', 8080, 'IN', 'IN', 'WebHorizon', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1630, 'forough.iperf3.ir', 5201, 'IR', 'IR', 'Arvancloud', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1631, 'bamdad.iperf3.ir', 5208, 'IR', 'IR', 'ArvanCloud', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1632, 'shahriar.iperf3.ir', 5207, 'IR', 'IR', 'ArvanCloud', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1633, 'simin.iperf3.ir', 5201, 'IR', 'IR', 'ArvanCloud', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1634, '89.187.160.1', 8080, 'JP', 'JP', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1635, 'speedtest.tyo11.jp.leaseweb.net', 5201, 'JP', 'JP', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1636, '91.185.23.98', 8080, 'KZ', 'KZ', '', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1637, 'iperf.myren.net.my', 5201, 'MY', 'MY', 'MYREN', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1638, '89.187.162.1', 8080, 'SG', 'SG', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1639, 'sgp.proof.ovh.net', 5201, 'SG', 'SG', 'OVH', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1640, 'speedtest.sin1.sg.leaseweb.net', 5201, 'SG', 'SG', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1641, 'speedtest.singnet.com.sg', 5201, 'SG', 'SG', 'Singnet', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1642, '202.29.80.9', 9200, 'TH', 'TH', 'psru', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1643, '58.64.45.56', 9200, 'TH', 'TH', 'Pibul University', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1644, '156.146.52.1', 8080, 'TR', 'TR', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1645, 'speedtest.uztelecom.uz', 5200, 'UZ', 'UZ', 'UZ Telecom', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1646, '185.180.12.40', 8080, 'AT', 'AT', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1647, 'lg.vie.alwyzon.net', 5202, 'AT', 'AT', 'alwyzon', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1648, '207.211.214.65', 8080, 'BE', 'BE', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1649, '37.19.203.1', 8080, 'BG', 'BG', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1650, 'speedtest.shinternet.ch', 5200, 'CH', 'CH', 'Sasag', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1651, 'speedtest.init7.net', 8080, 'CH', 'CH', 'Init7', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1652, '89.187.165.1', 8080, 'CH', 'CH', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1653, 'speedtest.iway.ch', 8080, 'CH', 'CH', 'iWay', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1654, 'rychlost.poda.cz', 5203, 'CZ', 'CZ', 'PODA', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1655, '185.152.65.113', 8080, 'CZ', 'CZ', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1656, 'a110.speedtest.wobcom.de', 8080, 'DE', 'DE', 'WOBCOM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1657, 'a209.speedtest.wobcom.de', 8080, 'DE', 'DE', 'WOBCOM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1658, 'a208.speedtest.wobcom.de', 8080, 'DE', 'DE', 'WOBCOM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1659, '178.215.228.109', 9201, 'DE', 'DE', 'Eseven', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1660, '185.102.219.93', 8080, 'DE', 'DE', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1661, 'a205.speedtest.wobcom.de', 8080, 'DE', 'DE', 'WOBCOM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1662, 'a210.speedtest.wobcom.de', 8080, 'DE', 'DE', 'WOBCOM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1663, 'fra.speedtest.clouvider.net', 5200, 'DE', 'DE', 'Clouvider', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1664, 'spd-desrv.hostkey.com', 5201, 'DE', 'DE', 'HOSTKEY', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1665, 'speedtest.fra1.de.leaseweb.net', 5201, 'DE', 'DE', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1666, 'speedtest.ip-projects.de', 8080, 'DE', 'DE', 'IP Projects', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1667, 'speedtest.wtnet.de', 5200, 'DE', 'DE', 'wilhelm.tel', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1668, 'a400.speedtest.wobcom.de', 8080, 'DE', 'DE', 'WOBCOM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1669, 'speedtest.wobcom.de', 8080, 'DE', 'DE', 'WOBCOM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1670, '121.127.45.65', 8080, 'DK', 'DK', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1671, 'speed.fiberby.dk', 9201, 'DK', 'DK', 'Fiberby', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1672, 'speedtest.hiper.dk', 5201, 'DK', 'DK', 'Hiper', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1673, '185.93.3.50', 8080, 'ES', 'ES', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1674, 'spd-fisrv.hostkey.com', 5201, 'FI', 'FI', 'HOSTKEY', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1675, 'speedtest-hki.netplaza.fi', 8080, 'FI', 'FI', 'Cinia', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1676, 'speedtest.cinia.fi', 8080, 'FI', 'FI', 'Cinia', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1677, '138.199.14.66', 8080, 'FR', 'FR', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1678, '185.93.2.193', 8080, 'FR', 'FR', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1679, 'iperf.online.net', 5200, 'FR', 'FR', 'Scaleway', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1680, 'iperf3.moji.fr', 5200, 'FR', 'FR', 'moji', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1681, 'ping-90ms.online.net', 5200, 'FR', 'FR', 'Scaleway', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1682, 'proof.ovh.net', 5201, 'FR', 'FR', 'OVH', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1683, 'ping.online.net', 5200, 'FR', 'FR', 'Scaleway', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1684, 'scaleway.testdebit.info', 5200, 'FR', 'FR', 'Scaleway', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1685, '185.59.221.51', 8080, 'GB', 'GB', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1686, 'iperf.as42831.net', 5300, 'GB', 'GB', 'UK Servers', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1687, 'lon.speedtest.clouvider.net', 5200, 'GB', 'GB', 'Cloudvider', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1688, 'speedtest.lon1.uk.leaseweb.net', 5201, 'GB', 'GB', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1689, 'speedtest.lon12.uk.leaseweb.net', 5201, 'GB', 'GB', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1690, '169.150.252.2', 8080, 'GR', 'GR', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1691, '169.150.242.129', 8080, 'HR', 'HR', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1692, 'speedtest1.vodafone.hu', 8080, 'HU', 'HU', 'Vodafone', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1693, '87.249.137.8', 8080, 'IR', 'IR', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1694, 'spd-icsrv.hostkey.com', 5201, 'IS', 'IS', 'HOSTKEY', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1695, 'it1.speedtest.aruba.it', 8080, 'IT', 'IT', 'Aruba.it', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1696, 'speed.itgate.net', 5201, 'IT', 'IT', 'IT GATE', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1697, '84.17.59.129', 8080, 'IT', 'IT', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1698, '217.61.40.96', 8080, 'IT', 'IT', 'Aruba.it', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1699, 'speedtest.lu.buyvm.net', 8080, 'LU', 'LU', 'BuyVM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1700, '185.102.218.1', 8080, 'NL', 'NL', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1701, 'a204.speedtest.wobcom.de', 8080, 'NL', 'NL', 'WOBCOM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1702, 'ams.speedtest.clouvider.net', 5200, 'NL', 'NL', 'Clouvider', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1703, 'iperf-ams-nl.eranium.net', 5201, 'NL', 'NL', 'Eranium', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1704, 'lg.ams-nl.terrahost.com', 9201, 'NL', 'NL', 'TerraHost', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1705, 'ping-ams1.online.net', 5200, 'NL', 'NL', 'Scaleway', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1706, 'speedtest.ams1.nl.leaseweb.net', 5201, 'NL', 'NL', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1707, 'speedtest.ams1.novogara.net', 5200, 'NL', 'NL', 'Novogara', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1708, 'speedtest.ams2.nl.leaseweb.net', 5201, 'NL', 'NL', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1709, 'speedtest.macarne.com', 8080, 'NL', 'NL', 'Macarne', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1710, 'speedtest.nl3.mirhosting.net', 5201, 'NL', 'NL', 'Mirhosting', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1711, 'speedtest.novoserve.com', 5201, 'NL', 'NL', 'NovoServe', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1712, 'lg-drn.liteserver.nl', 5200, 'NL', 'NL', 'LiteServer', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1713, 'speedtest.nl1.mirhosting.net', 5201, 'NL', 'NL', 'Mirhosting', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1714, 'iperf.worldstream.nl', 8080, 'NL', 'NL', 'Worldstream', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1715, 'lg.gigahost.no', 9201, 'NO', 'NO', 'Gigahost', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1716, 'lg.terrahost.com', 9200, 'NO', 'NO', 'Terrahost', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1717, '185.246.208.67', 8080, 'PL', 'PL', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1718, 'speedtest-w5-rnp.play.pl', 8080, 'PL', 'PL', 'PLAY', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1719, '109.61.94.65', 8080, 'PT', 'PT', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1720, 'lisboa.speedtest.net.zon.pt', 5201, 'PT', 'PT', 'NOS', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1721, 'porto.speedtest.net.zon.pt', 5201, 'PT', 'PT', 'NOS', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1722, '185.102.217.170', 8080, 'RO', 'RO', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1723, 'speedtest1.sox.rs', 9201, 'RS', 'RS', 'SOX', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1724, 'speedtest.kamel.network', 5201, 'SE', 'SE', 'Kamel Networks', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1725, 'speedtest.cityhost.se', 5201, 'SE', 'SE', 'CityHost', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1726, '185.76.9.135', 8080, 'SE', 'SE', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1727, 'speedtest.ownit.se', 8080, 'SE', 'SE', 'Ownit', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1728, '156.146.40.65', 8080, 'SK', 'SK', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1729, '37.19.218.65', 8080, 'UA', 'UA', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1730, 'speedtest.solver.net.ua', 8080, 'UA', 'UA', 'Conbed', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1731, '138.199.4.1', 8080, 'BR', 'BR', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1732, 'speedtest.sao1.edgoo.net', 9204, 'BR', 'BR', 'as47787.net', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1733, '79.127.209.1', 8080, 'CL', 'CL', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1734, 'sp11.wom.cl', 8080, 'CL', 'CL', 'WOM', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1735, 'speedtest-cncp.grupogtd.com', 5201, 'CL', 'CL', 'GTD', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1736, '152.204.128.194', 55200, 'CO', 'CO', 'Movistar', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1737, '169.150.228.129', 8080, 'CO', 'CO', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:53', '2025-04-01 13:57:53'),
(1738, '156.146.53.53', 8080, 'CR', 'CR', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1739, 'speedtest.masnet.ec', 5201, 'EC', 'EC', 'MásNet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1740, '121.127.43.65', 8080, 'MX', 'MX', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1741, '200.2.166.166', 5201, 'SR', 'SR', 'Telesur', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1742, 'bhs.proof.ovh.ca', 5201, 'CA', 'CA', 'OVH', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1743, 'speedtest-west.eastlink.ca', 8080, 'CA', 'CA', 'eastlink', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1744, 'speedtest.mtl2.ca.leaseweb.net', 5201, 'CA', 'CA', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1745, 'as21723.goco.ca', 9200, 'CA', 'CA', 'goco', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1746, '138.199.57.129', 8080, 'CA', 'CA', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1747, '37.19.206.20', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1748, 'ash.speedtest.clouvider.net', 5200, 'US', 'US', 'Clouvider', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1749, '185.152.66.67', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1750, 'atl.speedtest.clouvider.net', 5200, 'US', 'US', 'Clouvider', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1751, '109.61.86.65', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1752, 'speedtest13.suddenlink.net', 8080, 'US', 'US', 'Optimum', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1753, 'speedtest15.suddenlink.net', 8080, 'US', 'US', 'Optimum', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1754, '185.93.1.65', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1755, 'speedtest.chi11.us.leaseweb.net', 5201, 'US', 'US', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1756, '89.187.164.1', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1757, 'dal.speedtest.clouvider.net', 5200, 'US', 'US', 'Clouvider', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1758, 'speedtest.dal13.us.leaseweb.net', 5201, 'US', 'US', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1759, '84.17.63.68', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1760, '37.19.216.1', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1761, '185.152.67.2', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1762, 'la.speedtest.clouvider.net', 5200, 'US', 'US', 'Clouvider', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1763, 'speedtest.lax12.us.leaseweb.net', 5201, 'US', 'US', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1764, 'speedtest.tds.net', 8080, 'US', 'US', 'TDS', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1765, '195.181.162.195', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1766, 'speedtest.mia11.us.leaseweb.net', 5201, 'US', 'US', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1767, '185.59.223.8', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1768, 'spd-uswb.hostkey.com', 5201, 'US', 'US', 'HOSTKEY', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1769, 'nyc.speedtest.clouvider.net', 5200, 'US', 'US', 'Clouvider', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1770, 'speedtest.nyc1.us.leaseweb.net', 5201, 'US', 'US', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1771, 'speedtest.phx1.us.leaseweb.net', 5201, 'US', 'US', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1772, 'speedtest.sfo12.us.leaseweb.net', 5201, 'US', 'US', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1773, '84.17.41.11', 8080, 'US', 'US', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1774, 'speedtest.sea11.us.leaseweb.net', 5201, 'US', 'US', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1775, 'speedtest.is.cc', 5201, 'US', 'US', 'InterServer', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1776, 'speedtest.wdc2.us.leaseweb.net', 5201, 'US', 'US', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1777, '143.244.63.144', 8080, 'AU', 'AU', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1778, '198.142.237.72', 5202, 'AU', 'AU', 'OPTUS', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1779, '198.142.237.97', 5202, 'AU', 'AU', 'OPTUS', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1780, 'speedtest.syd12.au.leaseweb.net', 5201, 'AU', 'AU', 'LeaseWeb', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1781, 'syd.proof.ovh.net', 5201, 'AU', 'AU', 'OVH', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1782, 'techspeedtest.bla.optusnet.com.au', 5202, 'AU', 'AU', 'OPTUS', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1783, 'akl.linetest.nz', 5300, 'NZ', 'NZ', '2Degrees', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1784, 'chch.linetest.nz', 5300, 'NZ', 'NZ', '2Degrees', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1785, '154.81.51.4', 8080, 'PG', 'PG', 'DATAPACKET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1786, 'st1.simpur.net.bn', 8080, 'BN', 'BN', 'Unified National Networks (UNN) Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1787, 'speedtest1unn.brunet.bn', 8080, 'BN', 'BN', 'Unified National Networks Sdn Bhd(UNN)', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1788, 'speedtest4unn.brunet.bn', 8080, 'BN', 'BN', 'Unified National Networks Sdn Bhd(UNN)', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1789, 'speedtest3unn.brunet.bn', 8080, 'BN', 'BN', 'Unified National Networks Sdn Bhd(UNN)', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1790, 'speedtest2unn.brunet.bn', 8080, 'BN', 'BN', 'Unified National Networks Sdn Bhd(UNN)', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1791, 'speedtest.smart.com.kh', 8080, 'KH', 'KH', 'Smart Axiata', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1792, 'speedtest.sinet.com.kh', 8080, 'KH', 'KH', 'SINET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1793, 'speedtest.metfone.com.kh', 8080, 'KH', 'KH', 'MetfonePNP for App and Web', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1794, 'speedtest.esurfingkh.com', 8080, 'KH', 'KH', 'Esurfing Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1795, 'speedtest.unsyiah.ac.id', 8080, 'ID', 'ID', 'Universitas Syiah Kuala', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1796, 'corebtj1.tri.co.id', 8080, 'ID', 'ID', 'Hutchison 3 Indonesia - ACH', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1797, 'speedtest.golden.net.id', 8080, 'ID', 'ID', 'Golden NET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1798, 'balikpapan.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT. Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1799, 'speedtest.balikpapan.globalxtreme.net', 8080, 'ID', 'ID', 'GlobalXtreme', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1800, 'speedtest.lintasdata.net.id', 8080, 'ID', 'ID', 'Lintasdata Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1801, 'speedtest.tunaslink.net.id', 8080, 'ID', 'ID', 'PT. TUNAS LINK INDONESIA', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1802, 'sp-bdl.itn.net.id', 8080, 'ID', 'ID', 'INDONESIA TRANS NETWORK', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1803, 'speedtest.mynusa.net.id', 8080, 'ID', 'ID', 'mynusa', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1804, 'server-29208.prod.hosts.ooklaserver.net', 8080, 'ID', 'ID', 'ZITLINE-ISP', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1805, 'spt1.cmedia.net.id', 8080, 'ID', 'ID', 'PT Chandra Media Nusantara', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1806, 'speedtest.banjarkab.go.id', 8080, 'ID', 'ID', 'Pemerintah Kabupaten Banjar', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1807, '103.23.233.18', 8080, 'ID', 'ID', 'Universitas Lambung Mangkurat', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1808, 'banjarmasin.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT. Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1809, 'v94.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Banjarmasin', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1810, 'speedtest.poliwangi.ac.id', 8080, 'ID', 'ID', 'Politeknik Negeri Banyuwangi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1811, 'mttnetwork.online', 8080, 'ID', 'ID', 'MTT Networks - BWI', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1812, 'speedtest.jaosing.com', 8080, 'ID', 'ID', 'PT JMI Jaosing', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1813, 'mtt.net.id', 8080, 'ID', 'ID', 'PT Multi Teknologi Telematika Banyuwangi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1814, 'pegasusnetwork.net', 8080, 'ID', 'ID', 'Pegasus Network MTT-BWI', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1815, 'stest.riyadnetwork.id', 8080, 'ID', 'ID', 'PT RIYAD NETWORK TGS', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1816, 'asnet.pegasusnetwork.net', 8080, 'ID', 'ID', 'AS Network Sarongan - MTT BWI', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1817, 'speedtest.banyuwangikab.go.id', 8080, 'ID', 'ID', 'Pemerintah Kabupaten Banyuwangi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1818, 'speedtest.wahyuadidaya.co.id', 8080, 'ID', 'ID', 'PT Wahyu Adidaya Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1819, 'speedtest.permana.net.id', 8080, 'ID', 'ID', 'PT.MEDIANUSA PERMANA', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1820, 'speedtest.cic.net.id', 8080, 'ID', 'ID', 'PT Cipta Informatika Cemerlang', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1821, 'speedtestsgbtm.sdi.net.id', 8080, 'ID', 'ID', 'PT. Sumber Data Indonesia SDI', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1822, 'speedtest-btm.moratelindo.co.id', 8080, 'ID', 'ID', 'PT Mora Telematika Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1823, 'speedtestbtm.sdi.net.id', 8080, 'ID', 'ID', 'PT. SUMBER DATA INDONESIA BATAM', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1824, 'batam.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1825, 'st-btm.kejora.net.id', 8080, 'ID', 'ID', 'kejoraNET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1826, 'speedtest-wir.idola.net.id', 8080, 'ID', 'ID', 'PT. Aplikanusa Lintasarta', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1827, 'corebth4.tri.co.id', 8080, 'ID', 'ID', 'PT. Hutchison 3 Indonesia - BTH', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1828, 'speedtest.batam.telkomsel.com', 8080, 'ID', 'ID', 'Telkomsel', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1829, 'speedtest.manadocoder.com', 8080, 'ID', 'ID', 'JSN Jaringanku', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1830, 'speedtest.wifinan.com', 8080, 'ID', 'ID', 'WIFINAN.COM', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1831, 'speedtest.bloraonline.com', 8080, 'ID', 'ID', 'JSN Jaringanku', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1832, 'speedtest.ujianonlineku.com', 8080, 'ID', 'ID', 'JSN Jaringanku', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1833, 'speedtest.mayatama.net', 8080, 'ID', 'ID', 'PT. Mayatama Solusindo', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1834, 'v95.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Gorontalo', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1835, 'speedtest2.jlm.net.id', 8080, 'ID', 'ID', 'PT. Jala Lintas Media', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1836, 'speedtest.ung.ac.id', 8080, 'ID', 'ID', 'UNIVERSITAS NEGERI GORONTALO', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1837, 'speedtest5.lintas.net.id', 8080, 'ID', 'ID', 'PT Lintas Jaringan Nusantara', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1838, 'speedtest.cbn.id', 8080, 'ID', 'ID', 'CBN', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1839, 'speedtest.primacom.id', 8080, 'ID', 'ID', 'PT Primacom Interbuana', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1840, 'speedtest.angkasa.id', 8080, 'ID', 'ID', 'PT Angkasa Komunikasi Global Utama', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1841, 'speedtest.circleone.net.id', 8080, 'ID', 'ID', 'Circlecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1842, 'speedtest.biznetnetworks.com', 8080, 'ID', 'ID', 'Biznet Networks', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1843, 'speedtest.sti-group.co.id', 8080, 'ID', 'ID', 'PT Semesta Teknologi Informatika', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1844, 'speedtest.balifiber.id', 8080, 'ID', 'ID', 'BALIFIBER (PT Bali Towerindo Sentra Tbk)', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1845, '158.140.187.5', 8080, 'ID', 'ID', 'My Republic Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1846, 'speedtest.vnt.net.id', 8080, 'ID', 'ID', 'PT. JARINGAN VNT INDONESIA', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1847, 'speedtest.iconpln.net.id', 8080, 'ID', 'ID', 'PT Indonesia Comnets Plus', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1848, 'speedtest-jkt01.bit-teknologi.com', 8080, 'ID', 'ID', 'PT. BIT TEKNOLOGI NUSANTARA', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1849, 'speedtest.jagat.net.id', 8080, 'ID', 'ID', 'JAGAT MEDIA TEKNOLOGI', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1850, 'speedtest1.jlm.net.id', 8080, 'ID', 'ID', 'PT. Jala Lintas Media', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1851, 'jakarta.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT. Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1852, 'speedtest.qiandra.net.id', 8080, 'ID', 'ID', 'Qiandra Information Technology', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1853, 'speedtest.indosat.com', 8080, 'ID', 'ID', 'PT Indosat Tbk', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1854, 'speedtest1.moratelindo.co.id', 8080, 'ID', 'ID', 'PT Mora Telematika Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1855, 'speedtest.dna.net.id', 8080, 'ID', 'ID', 'Digital Network Antanusa', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1856, 'speedtest3.lintas.net.id', 8080, 'ID', 'ID', 'PT Lintas Jaringan Nusantara', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1857, 'riznetwork.pw', 8080, 'ID', 'ID', 'JSN Jaringanku', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1858, '849708b9eeb3.sn.mynetname.net', 8080, 'ID', 'ID', 'RizNetwork', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1859, 'v101.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Lampung', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1860, 'v137.2.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Makasar', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1861, 'sp1.faznet.co.id', 8080, 'ID', 'ID', 'PT CITRA PRIMA MEDIA', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1862, 'makasar.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT. Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1863, 'coreupg1.tri.co.id', 8080, 'ID', 'ID', 'Hutchison 3 Indonesia UPG', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1864, 'speedtest-mks.hypernet.co.id', 8080, 'ID', 'ID', 'Hypernet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1865, 'bwtest1.manadocoder.com', 8080, 'ID', 'ID', 'Manado Coder', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1866, 'jsn.manado.manadocoder.com', 8080, 'ID', 'ID', 'JSN Jaringanku', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1867, 'sp1.infotek.net.id', 8080, 'ID', 'ID', 'Infotek Global Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1868, 'speedtest.unsrat.ac.id', 8080, 'ID', 'ID', 'Universitas Sam Ratulangi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1869, 'manado.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT. Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1870, 'speedtest.nusa.net.id', 8080, 'ID', 'ID', 'nusanet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1871, 'myspeed.tnc.co.id', 8080, 'ID', 'ID', 'PT. Telemedia Network Cakrawala', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1872, 'speedtest.ims.net.id', 8080, 'ID', 'ID', 'PT Inter Medialink Solusi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1873, 'speedtest.medan.telkomsel.com', 8080, 'ID', 'ID', 'PT Telekomunikasi Seluler', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1874, 'medan.acehlink.id', 8080, 'ID', 'ID', 'PT Acehlink Media', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1875, 'medan.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT. Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1876, 'speedtest4.indosatooredoo.com', 8080, 'ID', 'ID', 'PT Indosat Tbk', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1877, 'speedtest.trinity.net.id', 8080, 'ID', 'ID', 'PT Trinity Teknologi Nusantara', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1878, 'speedtest.pdu.net.id', 8080, 'ID', 'ID', 'PT Panca Duta Utama', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1879, 'server.dispendik.app', 8080, 'ID', 'ID', 'Dinas Pendidikan Kabupaten Mojokerto', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1880, 'gigacommunity.id', 8080, 'ID', 'ID', 'JSN Jaringanku', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1881, 'speedtest-pdg.wanxp.id', 8080, 'ID', 'ID', 'WANXP', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1882, 'v92.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Padang', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1883, 'coreplm1.tri.co.id', 8080, 'ID', 'ID', 'Hutchison 3 Indonesia PLM', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1884, 'v98.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Palembang', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1885, 'palembang.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT. Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1886, 'speedtest.bnetfiber.id', 8080, 'ID', 'ID', 'PT. Sakti Putra Mandiri', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1887, 'kaili.speedtest.id', 8080, 'ID', 'ID', 'Kaili Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1888, 'v137.1.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Pangkalanbun', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1889, 'v96.faznet.co.id', 8080, 'ID', 'ID', 'faznet-pgk', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1890, 'iix1.mikaila.web.id', 8080, 'ID', 'ID', 'Mikaila-Net', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1891, 'speedtest.gowifi.id', 8080, 'ID', 'ID', 'GOWIFI.ID', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1892, 'speed.intek.web.id', 8080, 'ID', 'ID', 'PT Intimedia Teknologi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1893, 'speed.duniadigitalindo.com', 8080, 'ID', 'ID', 'Yohanz Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1894, 'speedtest-1.wanxp.id', 8080, 'ID', 'ID', 'WANXP', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1895, 'speedtest.pekanbaru.telkomsel.com', 8080, 'ID', 'ID', 'Telkomsel', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1896, 'speedtest.r1net.id', 8080, 'ID', 'ID', 'PT. Riau Satu Net', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1897, 'biznet.rhzahra.com', 8080, 'ID', 'ID', 'HostKita Web Hosting', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1898, 'speedtest.dash.net.id', 8080, 'ID', 'ID', 'Dashnet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1899, 'sppku.centro.net.id', 8080, 'ID', 'ID', 'Centro Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1900, 'speed.centro.net.id', 8080, 'ID', 'ID', 'Mairis Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1901, 'v100.faznet.co.id', 8080, 'ID', 'ID', 'Faznet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1902, 'corepku1.tri.co.id', 8080, 'ID', 'ID', 'Hutchison 3 Indonesia - PKU', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1903, 'speed.gds.net.id', 8080, 'ID', 'ID', 'GDS Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1904, 'sp.centro.net.id', 8080, 'ID', 'ID', 'Centro Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1905, 'corepnk3.tri.co.id', 8080, 'ID', 'ID', 'Hutchison 3 Indonesia - PNK', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1906, 'speedtest.grahamedia.net.id', 8080, 'ID', 'ID', 'PT Grahamedia Informasi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1907, 'v102.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Samarinda', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1908, 'speedtest.samarinda.globalxtreme.net', 8080, 'ID', 'ID', 'GlobalXtreme', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1909, 'v137.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Sampit', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1910, 'speed.undip.ac.id', 8080, 'ID', 'ID', 'Diponegoro University', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1911, 'speedtest.semarangkota.go.id', 8080, 'ID', 'ID', 'Pemerintah Kota Semarang', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1912, 'speedtest.dinus.ac.id', 8080, 'ID', 'ID', 'Dian Nuswantoro Teknologi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1913, 'semarang.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT. Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1914, 'mm1.unnes.ac.id', 8080, 'ID', 'ID', 'Universitas Negeri Semarang', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1915, 'speedtestsmg1.gmedia.net.id', 8080, 'ID', 'ID', 'GMedia Technologies', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1916, 'speedtest.jatengprov.go.id', 8080, 'ID', 'ID', 'Pemerintah Provinsi Jawa Tengah', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1917, 'v93.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Serang', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1918, 'speed.mmd.net.id', 8080, 'ID', 'ID', 'PT. Mitra Media Data', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1919, 'v137.3.faznet.co.id', 8080, 'ID', 'ID', 'Faznet-Sulawesi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1920, 'speedtest.sby.nusa.net.id', 8080, 'ID', 'ID', 'nusanet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1921, 'spd1.inti.net.id', 8080, 'ID', 'ID', 'PT. Inti Data Telematika', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1922, 'speedtest.turbo.net.id', 8080, 'ID', 'ID', 'Turbo Internet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1923, 'speedtest.dwp.net.id', 8080, 'ID', 'ID', 'DNET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1924, 'coresub1.tri.co.id', 8080, 'ID', 'ID', 'Hutchison 3 Indonesia SUB', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1925, 'gms.id', 8080, 'ID', 'ID', 'Gereja Mawar Sharon', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1926, 'speedtest3.indosatooredoo.com', 8080, 'ID', 'ID', 'PT Indosat Tbk', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1927, 'speedtest.sby.datautama.net.id', 8080, 'ID', 'ID', 'PT. Datautama Dinamika', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1928, 'sby1-speedtest.smartfren.com', 8080, 'ID', 'ID', 'Smartfren Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1929, 'ojs.petra.ac.id', 8080, 'ID', 'ID', 'Universitas Kristen Petra', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1930, 'spd1.universal.net.id', 8080, 'ID', 'ID', 'Universal Broadband', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1931, 'speedtest.wow.net.id', 8080, 'ID', 'ID', 'Wownet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1932, 'surabaya.speedtest.telkom.net.id', 8080, 'ID', 'ID', 'PT. Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1933, 'sby-speedtest.link.net.id', 8080, 'ID', 'ID', 'Firstmedia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1934, 'speedtest-sby.moratelindo.co.id', 8080, 'ID', 'ID', 'PT Mora Telematika Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1935, 'speedtest-eir.idola.net.id', 8080, 'ID', 'ID', 'PT. APLIKANUSA LINTASARTA', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1936, 'speedtest-sby.biznetnetworks.com', 8080, 'ID', 'ID', 'Biznet Networks', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1937, 'speedtest.solo.citra.net.id', 8080, 'ID', 'ID', 'CitraNet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1938, 'solo.powertel.co.id', 8080, 'ID', 'ID', 'PowerTel', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1939, 'speedtest.gsmnet.id', 8080, 'ID', 'ID', 'PT. Lintas Data Prima', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1940, 'speedtest2.solo.citra.net.id', 8080, 'ID', 'ID', 'CitraNet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1941, 'bwtest.tanahbumbukab.go.id', 8080, 'ID', 'ID', 'Pemerintah Kabupaten Tanah Bumbu', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1942, 'speedtest.netciti.co.id', 8080, 'ID', 'ID', 'PT Netciti Persada', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1943, 'jkt2-speedtest.smartfren.com', 8080, 'ID', 'ID', 'Smartfren Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1944, 'speedtest.gmis.net.id', 8080, 'ID', 'ID', 'PT Global Media Inti Semesta', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1945, 'speedtest.ublink.id', 8080, 'ID', 'ID', 'PT Palapa Media Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1946, 'speedtest-tebing.core.net.id', 8080, 'ID', 'ID', 'CoreNet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1947, 'speedtest.soppengkab.go.id', 8080, 'ID', 'ID', 'Pemerintah Kabupaten Soppeng', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1948, 'speed.uthm.edu.my', 8080, 'MY', 'MY', 'UTHM', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1949, 'brfspeedtest.webe.com.my', 8080, 'MY', 'MY', 'webe Digital Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1950, 'speed1.globalforway.com', 8080, 'MY', 'MY', 'Global Forway Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1951, 'speed.ipcore.com.my', 8080, 'MY', 'MY', 'IP Core Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1952, 'speedtest.bayam.my', 8080, 'MY', 'MY', 'Bayam (M) Sdn. Bhd.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1953, 'speedtest-cx2.ipserverone.com', 8080, 'MY', 'MY', 'IP ServerOne Solutions Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1954, 'speed.allo.technology', 8080, 'MY', 'MY', 'Allo Technology', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1955, 'cbj-speedtest.ebb.my', 8080, 'MY', 'MY', 'Extreme Broadband', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1956, 'speedtest.ixtelecom.net', 8080, 'MY', 'MY', 'IX Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1957, 'speedtest1.myren.net.my', 8080, 'MY', 'MY', 'MYREN', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1958, 'cj-speedtest.ebb.my', 8080, 'MY', 'MY', 'Extreme Broadband', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1959, 'speedtest.gbnetwork.com', 8080, 'MY', 'MY', 'GB Network Solutions Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1960, 'ipdcspeedtest.webe.com.my', 8080, 'MY', 'MY', 'webe Digital Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1961, 'speedtestjhr.u.net.my', 8080, 'MY', 'MY', 'U Mobile Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1962, 'digi-ookla06.digi.com.my', 8080, 'MY', 'MY', 'Digi Malaysia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1963, 'speedtestlenang.sunwaytech.com.my', 8080, 'MY', 'MY', 'Sunway Digital Wave', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1964, 'sabah-net.com', 8080, 'MY', 'MY', 'Sabah Net Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1965, 'speedtest-10g.ctsabah.net', 8080, 'MY', 'MY', '10G-Celcom Timur (Sabah) Sdn. Bhd.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1966, 'speedokrt1.celcom.net.my', 8080, 'MY', 'MY', 'Celcom Axiata', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1967, 'speedtest.i-skill.com', 8080, 'MY', 'MY', 'i-Skill Dynamics Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1968, 'speedtest.u.com.my', 8080, 'MY', 'MY', 'U Mobile', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1969, 'speed.macrolynx.com', 8080, 'MY', 'MY', 'Macro Lynx Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1970, 'speedtest-ookla1.tm.net.my', 8080, 'MY', 'MY', 'Telekom Malaysia Berhad', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1971, 'speedosht1.celcom.net.my', 8080, 'MY', 'MY', 'Celcom Axiata', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1972, 'speedtest.tm.com.my', 8080, 'MY', 'MY', 'Telekom Malaysia Berhad', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1973, 'jrcspeedtest.webe.com.my', 8080, 'MY', 'MY', 'webe Digital Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1974, 'kl-speedtest.ebb.my', 8080, 'MY', 'MY', 'Extreme Broadband Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1975, 'sp1.shinjiru.com.my', 8080, 'MY', 'MY', 'Shinjiru Technology', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1976, 'infinity.orient-telecoms.com', 8080, 'MY', 'MY', 'Orient Telecoms', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1977, 'speedtest.ytlcomms.my', 8080, 'MY', 'MY', 'Yes 4G', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1978, 'speed.innet.com.my', 8080, 'MY', 'MY', 'InNET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1979, 'speedtest2.mykris.net', 8080, 'MY', 'MY', 'MyKRIS Asia Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1980, 'speedtest.unikl.edu.my', 8080, 'MY', 'MY', 'Universiti Kuala Lumpur', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1981, 'ookla.mschosting.com', 8080, 'MY', 'MY', 'Exabytes Cloud Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1982, 'speedtest.ytlbroadband.my', 8080, 'MY', 'MY', 'YTL Broadband', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1983, 'speedtest.umt.edu.my', 8080, 'MY', 'MY', 'Universiti Malaysia Terengganu', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1984, 'speedtest1.danawa.com.my', 8080, 'MY', 'MY', 'DANAWA Resources Sdn. Bhd.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1985, 'speedopdg1.celcom.net.my', 8080, 'MY', 'MY', 'Celcom Axiata', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1986, 'speedtest.sacofa.com.my', 8080, 'MY', 'MY', 'SACOFA Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1987, 'poweredby.tm.com.my', 8080, 'MY', 'MY', 'TM 5G Langkawi', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1988, 'speedtest-sabah.tm.com.my', 8080, 'MY', 'MY', 'TM Sabah', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1989, 'pngspeedtest.mykris.net', 8080, 'MY', 'MY', 'MyKRIS Asia Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1990, 'speedtest.progenet.com', 8080, 'MY', 'MY', 'Progenet Innovations Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1991, 'speedokpg1.celcom.net.my', 8080, 'MY', 'MY', 'Celcom Axiata', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1992, 'speed.webe.com.my', 8080, 'MY', 'MY', 'webe Digital Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1993, 'speedtest.digi.com.my', 8080, 'MY', 'MY', 'Digi Malaysia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1994, 'speed1.redtone.com', 8080, 'MY', 'MY', 'REDtone Telecommunications Sdn Bhd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1995, 'speedtest.uniten.edu.my', 8080, 'MY', 'MY', 'UNIVERSITI TENAGA NASIONAL', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1996, 'st.digi.com.my', 8080, 'MY', 'MY', 'Digi Malaysia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1997, 'speed.time.com.my', 8080, 'MY', 'MY', 'TIME MY', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1998, 'speedtest.maxis.com.my', 8080, 'MY', 'MY', 'Maxis', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(1999, 'speedtest.digitalwave.net.my', 8080, 'MY', 'MY', 'Sunway Digital Wave', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2000, 'speedtest-alabang.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2001, 'bcn-speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2002, 'speedtest-bac1.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2003, 'brc-spdtst-srv.convergeict.com', 8080, 'PH', 'PH', 'Converge ICT Solutions Inc.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2004, 'speedtest.planetcabletv.com', 8080, 'PH', 'PH', 'Planet Cable Inc.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2005, 'speedtest.speedtest-bataanspacecable.com', 8080, 'PH', 'PH', 'Bataan Space Cable Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2006, 'kabayanbroadband.net', 8080, 'PH', 'PH', 'Kabayan Broadband', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2007, 'speedtest-batangas.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2008, 'cdo2-speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2009, 'speedtestparasat2.cable21.net', 8080, 'PH', 'PH', 'Parasat Cable TV, Inc.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2010, 'speedtestparasat.cable21.net', 8080, 'PH', 'PH', 'Parasat Cable TV', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2011, 'speedtest1-cdo.dctechmicro.com', 8080, 'PH', 'PH', 'DctecH Microservices Incorporated', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2012, '119.92.238.98', 8080, 'PH', 'PH', 'PLDT', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2013, '3.speedtest.dito.ph', 8080, 'PH', 'PH', 'PHtest3', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2014, 'ns37.timdcs.com', 8080, 'PH', 'PH', 'Total Information Management Corporation', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2015, 'speedtst-tim.asianvision.com.ph', 8080, 'PH', 'PH', 'Asian Vision', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54');
INSERT INTO `iperf_servers` (`id`, `ip_address`, `port`, `country_code`, `country_name`, `provider`, `last_checked_at`, `is_active`, `created_at`, `updated_at`) VALUES
(2016, 'speedtest-cavite.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2017, 'speedtest-cbu.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2018, 'cebu.smart.com.ph', 8080, 'PH', 'PH', 'Smart Communications Inc.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2019, '119.92.223.221', 8080, 'PH', 'PH', 'PLDT', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2020, 'fpsti.ddns.net', 8080, 'PH', 'PH', 'Cine Cebu (CCTNi)', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2021, 'speedtest-cebu.rise.as', 8080, 'PH', 'PH', 'Rise.ph', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2022, 'lhg2-speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2023, 'speedtest.dascacable.com', 8080, 'PH', 'PH', 'Dasca Cable Services, INC.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2024, 'speedtest-dvo1.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2025, 'speedtest.infinivan.com', 8080, 'PH', 'PH', 'Infinivan inc', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2026, 'dav3-speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2027, 'speedtest1.dctechmicro.com', 8080, 'PH', 'PH', 'DctecH Microservices Incorporated', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2028, 'min2.smart.com.ph', 8080, 'PH', 'PH', 'Smart Communications', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2029, 'speedtest-dgt1.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2030, 'speedtest.megaspeed.ph', 8080, 'PH', 'PH', 'Megaspeed ICT Solutions Inc', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2031, 'speedtest.filprod-dgte.ph', 8080, 'PH', 'PH', 'Fil Products Internet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2032, 'speedtest-gensan.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2033, 'gscspeedtest1.dctechmicro.com', 8080, 'PH', 'PH', 'DctecH Microservices Incorporated', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2034, 'ilo-speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2035, 'ns3.panaybroadband.com.ph', 8080, 'PH', 'PH', 'Panay Broadband (BCATVi)', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2036, 'speedtest-ilo.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2037, 'imu2-speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2038, 'speedtest.meridianfiberblaze.com', 8080, 'PH', 'PH', 'Meridian Cable TV, INC.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2039, 'speedtest.kcatfiber.com', 8080, 'PH', 'PH', 'KCAT', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2040, 'speedtest.ccvc.com.ph', 8080, 'PH', 'PH', 'Community Cable Vision Cor', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2041, 'speedtst-que.asianvision.com.ph', 8080, 'PH', 'PH', 'Asian Vision', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2042, '119.92.238.90', 8080, 'PH', 'PH', 'PLDT', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2043, 'lucenacity.smart.com.ph', 8080, 'PH', 'PH', 'Smart Communications, Inc.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2044, 'speedtest.ptt.com.ph', 8080, 'PH', 'PH', 'Philippine Telegraph and Telephone Corp.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2045, '122.2.174.30', 8080, 'PH', 'PH', 'PLDT', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2046, 'laspinas1.smart.com.ph', 8080, 'PH', 'PH', 'Smart Communications Inc.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2047, 'speedtest.infinivan.net', 8080, 'PH', 'PH', 'Infinivan', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2048, 'speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2049, 'speedtest-manila.rise.as', 8080, 'PH', 'PH', 'RISE.ph', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2050, 'speedtest-makati.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2051, 'speedtest.eastern-tele.com', 8080, 'PH', 'PH', 'Eastern Communications', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2052, 'speedtest.nctv.com.ph', 8080, 'PH', 'PH', 'Naic Cable TV', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2053, 'speedtst-zmb.asianvision.com.ph', 8080, 'PH', 'PH', 'Asian Vision', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2054, 'speedtest1.cablelink.com.ph', 8080, 'PH', 'PH', 'Cablelink Internet Services Inc', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2055, 'spl-spdtst-srv.convergeict.com', 8080, 'PH', 'PH', 'Converge ICT Solutions Inc.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2056, 'speedtest.cablevisionsystemscorp.com', 8080, 'PH', 'PH', 'Ricklee Enterprises', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2057, 'srs-speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2058, 'speedtest1.royalcable.com.ph', 8080, 'PH', 'PH', 'Royal Cable', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2059, 'speedtest.galaxycable.com.ph', 8080, 'PH', 'PH', 'Galaxy Cable', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2060, 'speedtest-laguna.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2061, 'speedtestbohol.netcity.ph', 8080, 'PH', 'PH', 'NetCity Bohol', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2062, 'tgt-speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2063, 'speedtest.ixs.ph', 8080, 'PH', 'PH', 'iXSforall, inc.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2064, 'speedtest.kmc.solutions', 8080, 'PH', 'PH', 'KMC Solutions', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2065, 'dav2-speedtest.globe.com.ph', 8080, 'PH', 'PH', 'Globe Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2066, 'speedtest-zamboanga.skybroadband.com.ph', 8080, 'PH', 'PH', 'Sky Fiber', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2067, 'speedtest.integranet.ph', 8080, 'PH', 'PH', 'IntegraNet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2068, 'cagayandeoro.smart.com.ph', 8080, 'PH', 'PH', 'Smart Communications Inc.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2069, 'speedtest10.vqbn.com', 8080, 'SG', 'SG', 'Viewqwest Pte Ltd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2070, 'speedtest.myrepublic.com.sg', 8080, 'SG', 'SG', 'MyRepublic', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2071, 'speedtest1.indosat.com', 8080, 'SG', 'SG', 'PT Indosat Tbk', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2072, 'www.speedtest.com.sg', 8080, 'SG', 'SG', 'NewMedia Express', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2073, 'sg-speedtest.fast.net.id', 8080, 'SG', 'SG', 'PT FirstMedia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2074, 'm1speedtest1.m1net.com.sg', 8080, 'SG', 'SG', 'M1 Limited', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2075, 'speedtest-sg.moratelindo.co.id', 8080, 'SG', 'SG', 'PT Mora Telematika Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2076, 'speedtest.sptel.com', 8080, 'SG', 'SG', 'SPTEL PTE. LTD.', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2077, 'singapore.speedtest.athamedia.cloud', 8080, 'SG', 'SG', 'CV. ATHA MEDIA PRIMA', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2078, 'aws-singapore-01.gnnodes.com', 8080, 'SG', 'SG', 'Gnehc Asia Pacific', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2079, 'speedtest-ix.idola.net.id', 8080, 'SG', 'SG', 'PT. Aplikanusa Lintasarta', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2080, 'lg-sin.fdcservers.net', 8080, 'SG', 'SG', 'fdcservers.net', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2081, 'speedtest-aws-sg1.3bb.co.th', 8080, 'SG', 'SG', '3BB', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2082, 'speedtest-sin.melbicom.net', 8080, 'SG', 'SG', 'Melbikomas UAB', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2083, 'speedtest.singnet.com.sg', 8080, 'SG', 'SG', 'Singtel', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2084, 'speedtest.cala.games', 8080, 'SG', 'SG', 'Cala Games', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2085, 'sg.as.speedtest.i3d.net', 8080, 'SG', 'SG', 'i3D.net', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2086, 'speedtest2.pacificinternet.com', 8080, 'SG', 'SG', 'Pacific Internet (S)', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2087, 'speedtest-eqx-sg1.ixtelecom.net', 8080, 'SG', 'SG', 'IX Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2088, 'speedtest-intl.xl.co.id', 8080, 'SG', 'SG', 'PT. XL Axiata Tbk', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2089, 'backupsg.jagoanhosting.com', 8080, 'SG', 'SG', 'PT Beon Intermedia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2090, 'speedtest-sg.napinfo.co.id', 8080, 'SG', 'SG', 'Matrix NAP Info', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2091, 'speedtest.walastikinternet.com.ph', 8080, 'SG', 'SG', 'Walastik Internet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2092, 'sig-speedtest.moratelindo.io', 8080, 'SG', 'SG', 'PT. Mora Telematika Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2093, 'speedtest08.fpt.vn', 8080, 'SG', 'SG', 'FPT Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2094, 'speedtest2.telin.sg', 8080, 'SG', 'SG', 'Telekomunikasi Indonesia International Pte Ltd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2095, 'speedtest.singapore.globalxtreme.net', 8080, 'SG', 'SG', 'GlobalXtreme', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2096, 'broadbandspeedtestuat.starhub.com', 8080, 'SG', 'SG', 'StarHub Mobile Pte Ltd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2097, 'sin.speedtest.contabo.net', 8080, 'SG', 'SG', 'Contabo', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2098, 'speedtest-sgp.apac-tools.ovh', 8080, 'SG', 'SG', 'OVH Cloud', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2099, 'sg3.speedtest.gslnetworks.com', 8080, 'SG', 'SG', 'GSL Networks', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2100, 'speedtest-sg.iconpln.net.id', 8080, 'SG', 'SG', 'PT Indonesia Comnets Plus', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2101, 'speedtestsg.ipungpurbaya.net', 8080, 'SG', 'SG', 'PT Graha Telekomunikasi Indonesia', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2102, 'ookla-sg.ngebuts.com', 8080, 'SG', 'SG', 'Ngebuts', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2103, 'speedtest.super.net.sg', 8080, 'SG', 'SG', 'SuperInternet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2104, 'speedtest.netpluz.asia', 8080, 'SG', 'SG', 'Netpluz Asia Pte Ltd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2105, 'sp1.campanaworks.com', 8080, 'SG', 'SG', 'Campana', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2106, 'ookla.lovelivesupport.com', 8080, 'SG', 'SG', 'KOMANI', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2107, 'speedsin.phoenixnap.com', 8080, 'SG', 'SG', 'PhoenixNAP Global IT Services', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2108, 'sin.archiveofourown.ru', 8080, 'SG', 'SG', 'Boom!VM', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2109, 'suksp1.myaisfibre.com', 8080, 'TH', 'TH', 'AIS Fibre', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2110, 'hatyai.catspeedtest.net', 8080, 'TH', 'TH', 'CAT TELECOM PUBLIC COMPANY LIMITED', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2111, 'speedtest-sv3.kpnhospital.com', 8080, 'TH', 'TH', 'Krongpinang Hospital', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2112, 'speedtest-nrt1.3bb.co.th', 8080, 'TH', 'TH', '3BB', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2113, 'speedtest-ptn1.3bb.co.th', 8080, 'TH', 'TH', '3BB', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2114, 'ptnsp1.myaisfibre.com', 8080, 'TH', 'TH', 'AIS', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2115, 'speedtest-pkt1.3bb.co.th', 8080, 'TH', 'TH', '3BB', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2116, 'speedtest-hyi1.ais-idc.net', 8080, 'TH', 'TH', 'AIS', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2117, 'speedtest-hyi1.3bb.co.th', 8080, 'TH', 'TH', '3BB', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2118, 'spd-ska.scms-tech.co.th', 8080, 'TH', 'TH', 'SCM Technologies', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2119, 'hyispeedtestnet.totbroadband.com', 8080, 'TH', 'TH', 'TOT', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2120, 'mon-hdy.psu.ac.th', 8080, 'TH', 'TH', 'Prince of Songkla Univ', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2121, 'speedtest-sni1.ais-idc.net', 8080, 'TH', 'TH', 'AIS', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2122, 'speedtest14.dtacnetwork.co.th', 8080, 'TH', 'TH', 'DTAC', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2123, 'spd-srt.scms-tech.co.th', 8080, 'TH', 'TH', 'SCM Technologies', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2124, 'speedtest-sni1.3bb.co.th', 8080, 'TH', 'TH', '3BB', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2125, 'ppnspeedtestnet.totbroadband.com', 8080, 'TH', 'TH', 'TOT', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2126, 'mon-trg.psu.ac.th', 8080, 'TH', 'TH', 'Computer Center, Prince of Songkla University', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2127, 'speedtest-sv.kpnhospital.com', 8080, 'TH', 'TH', 'Krongpinang Hospital', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2128, 'speedtest-sg-sgp1-01.vpnproxymaster.com', 8080, 'US', 'US', 'Lemon Clove Pte.Limited', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2129, 'vhcm.vietpn.com', 8080, 'VN', 'VN', 'VIETPN CO, LTD', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2130, 'speedtest1.dts.com.vn', 8080, 'VN', 'VN', 'DTS Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2131, 'speedtest.sptcantho.com', 8080, 'VN', 'VN', 'SPT4', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2132, 'speedtestkv3dp1.viettel.vn', 8080, 'VN', 'VN', 'Viettel Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2133, 'sp1.mobifone.vn', 8080, 'VN', 'VN', 'Mobifone', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2134, 'speedtest.vienthongact.vn', 8080, 'VN', 'VN', 'ACT Telecomunication Joint Stock Company', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2135, 'hcmspeedtest.cmctelecom.vn', 8080, 'VN', 'VN', 'CMC Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2136, 'sysops.cf', 8080, 'VN', 'VN', 'Vnetwok', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2137, 'speedtest.tpcoms.vn', 8080, 'VN', 'VN', 'TPCOMS', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2138, 'speedtestkv3b.viettel.vn', 8080, 'VN', 'VN', 'Viettel Network', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2139, 'speedtest.vnpt.vn.sharedata.xyz', 8080, 'VN', 'VN', 'Kobiton', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2140, 'hcm-speedtest01.sctv.com.vn', 8080, 'VN', 'VN', 'SCTV Co.Ltd', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2141, 'speedtest4.hcmc.netnam.vn', 8080, 'VN', 'VN', 'NetNam', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2142, 'sp2.dcnet.vn', 8080, 'VN', 'VN', 'DCNET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2143, 'sp1.supernet.vn', 8080, 'VN', 'VN', 'Supernet', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2144, 'speedtest.fpt.vn', 8080, 'VN', 'VN', 'FPT Telecom', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2145, 'speedtest3.vtn.com.vn', 8080, 'VN', 'VN', 'VNPT-NET', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2146, '118.107.96.3', 8080, 'VN', 'VN', 'VTC DIGICOM', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2147, 'vnmhcmspt1.vietnamobile.com.vn', 8080, 'VN', 'VN', 'Vietnamobile', NULL, 1, '2025-04-01 13:57:54', '2025-04-01 13:57:54'),
(2148, 'ping.online.net', 5200, 'FR', 'FR', 'Scaleway Vitry DC3', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2149, 'ping6.online.net', 5200, 'FR', 'FR', 'Scaleway Vitry DC3', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2150, 'ping-90ms.online.net', 5200, 'FR', 'FR', 'Scaleway Vitry DC3', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2151, 'ping6-90ms.online.net', 5200, 'FR', 'FR', 'Scaleway Vitry DC3', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2152, 'iperf3.moji.fr', 5200, 'FR', 'FR', 'DC Moji', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2153, 'speedtest.milkywan.fr', 9200, 'FR', 'FR', 'CBO Croissy-Beaubourg', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2154, 'iperf.par2.as49434.net', 9200, 'FR', 'FR', 'DC Harmony Hosting', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2155, 'paris.bbr.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2156, 'paris.cubic.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2157, 'mrs.bbr.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2158, 'mrs.cubic.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2159, 'lyo.bbr.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2160, 'lyo.cubic.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2161, 'tls.bbr.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2162, 'tls.cubic.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2163, 'str.bbr.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2164, 'str.cubic.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2165, 'poi.bbr.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2166, 'poi.cubic.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2167, 'ren.bbr.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2168, 'ren.cubic.iperf.bytel.fr', 9200, 'FR', 'FR', 'Bouygues Telecom', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2169, 'speedtest.serverius.net', 5002, 'NL', 'NL', 'Serverius data center', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2170, 'nl.iperf.014.fr', 10415, 'NL', 'NL', 'NextGenWebs data center', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2171, 'ch.iperf.014.fr', 15315, 'CH', 'CH', 'HostHatch data center', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2172, 'iperf.eenet.ee', 5201, 'EE', 'EE', 'EENet Tartu', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2173, 'iperf.astra.in.ua', 5201, 'UA', 'UA', 'Astra Lviv', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2174, 'iperf.volia.net', 5201, 'UA', 'UA', 'Volia Kiev', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2175, 'iperf.angolacables.co.ao', 9200, 'AO', 'AO', 'AngoNAP Luanda', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2176, 'speedtest.uztelecom.uz', 5200, 'UZ', 'UZ', 'Infosystems', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2177, 'iperf.biznetnetworks.com', 5201, 'ID', 'ID', 'Biznet - Midplaza Cimanggis', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2178, 'speedtest-iperf-akl.vetta.online', 5200, 'NZ', 'NZ', 'Vetta Online Auckland', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35'),
(2179, 'iperf.he.net', 5201, 'US', 'US', 'Hurricane Fremont 1', NULL, 1, '2025-04-01 10:57:35', '2025-04-01 10:57:35');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_articles`
--

CREATE TABLE `knowledge_articles` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `view_count` int NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_categories`
--

CREATE TABLE `knowledge_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_ratings`
--

CREATE TABLE `knowledge_ratings` (
  `id` bigint UNSIGNED NOT NULL,
  `article_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `is_helpful` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `labels`
--

CREATE TABLE `labels` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `label_ticket`
--

CREATE TABLE `label_ticket` (
  `label_id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mail_templates`
--

CREATE TABLE `mail_templates` (
  `id` int UNSIGNED NOT NULL,
  `mailable` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci,
  `html_template` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_template` longtext COLLATE utf8mb4_unicode_ci,
  `variables` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mail_templates`
--

INSERT INTO `mail_templates` (`id`, `mailable`, `subject`, `html_template`, `text_template`, `variables`, `created_at`, `updated_at`) VALUES
(18, 'App\\Mail\\Auth\\VerifyEmailMail', 'Verify Your Email Address', '\r\n                <h1>Verify Your Email</h1>\r\n                <p>Hi {{name}},</p>\r\n                <p>Please click the button below to verify your email address:</p>\r\n                <p>\r\n                    <a href=\"{{verification_url}}\" \r\n                       style=\"background: #3490dc; color: white; padding: 12px 25px; text-decoration: none; border-radius: 4px;\">\r\n                        Verify Email Address\r\n                    </a>\r\n                </p>\r\n                <p>If you did not create an account, no further action is required.</p>\r\n            ', NULL, 'name,verification_url', '2025-02-07 16:13:24', '2025-02-07 16:13:24'),
(19, 'App\\Mail\\Auth\\ResetPasswordMail', 'Reset Password', '<table id=\"u_body\" style=\"border-collapse: collapse; table-layout: fixed; border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; vertical-align: top; min-width: 320px; margin: 0 auto; background-color: #ecf0f1; width: 100%;\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr style=\"vertical-align: top;\">\r\n<td style=\"word-break: break-word; border-collapse: collapse !important; vertical-align: top;\">\r\n<div class=\"u-row-container\" style=\"padding: 0px; background-color: transparent;\">\r\n<div class=\"u-row\" style=\"margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;\">\r\n<div style=\"border-collapse: collapse; display: table; width: 100%; height: 100%; background-color: transparent;\">\r\n<div id=\"u_column_2\" class=\"u-col u-col-100\" style=\"max-width: 320px; min-width: 600px; display: table-cell; vertical-align: top;\">\r\n<div style=\"background-color: #ffffff; height: 100%; width: 100% !important; border-radius: 0px; -webkit-border-radius: 0px; -moz-border-radius: 0px;\">\r\n<div class=\"v-col-border\" style=\"box-sizing: border-box; height: 100%; padding: 0px; border-radius: 0px; -webkit-border-radius: 0px; -moz-border-radius: 0px; border: 15px solid #8d95ff;\"><!-- Logo Section -->\r\n<table id=\"u_content_heading_1\" style=\"font-family: \'Raleway\',sans-serif;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td class=\"v-container-padding-padding\" style=\"overflow-wrap: break-word; word-break: break-word; padding: 20px 0px; font-family: \'Raleway\',sans-serif;\" align=\"left\">\r\n<h1 class=\"v-text-align\" style=\"margin: 0px; color: #000000; line-height: 140%; text-align: center; word-wrap: break-word; font-size: 22px; font-weight: 400;\">bixa</h1>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style=\"font-family: \'Raleway\',sans-serif;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td class=\"v-container-padding-padding\" style=\"overflow-wrap: break-word; word-break: break-word; padding: 10px 0px; font-family: \'Raleway\',sans-serif;\" align=\"left\">\r\n<table style=\"border-collapse: collapse; table-layout: fixed; border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; vertical-align: top; border-top: 1px solid #BBBBBB; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n<tbody>\r\n<tr style=\"vertical-align: top;\">\r\n<td style=\"word-break: break-word; border-collapse: collapse !important; vertical-align: top; font-size: 0px; line-height: 0px; mso-line-height-rule: exactly; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;\">&nbsp;</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- Main Content -->\r\n<table id=\"u_content_text_2\" style=\"font-family: \'Raleway\',sans-serif;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td class=\"v-container-padding-padding\" style=\"overflow-wrap: break-word; word-break: break-word; padding: 20px 40px 10px; font-family: \'Raleway\',sans-serif;\" align=\"left\">\r\n<div class=\"v-text-align\" style=\"font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;\">\r\n<p style=\"line-height: 140%;\">Dear {{name}},</p>\r\n<br>\r\n<p style=\"line-height: 140%;\">We received a request to reset your password for your {site_name} account. Please click the button below to create a new password.</p>\r\n<br><strong>Reference:</strong> #bixa-{{name}}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- Button Section -->\r\n<table id=\"u_content_button_1\" style=\"font-family: \'Raleway\',sans-serif;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td class=\"v-container-padding-padding\" style=\"overflow-wrap: break-word; word-break: break-word; padding: 10px 10px 10px 40px; font-family: \'Raleway\',sans-serif;\" align=\"left\">\r\n<div class=\"v-text-align\" align=\"left\"><a class=\"v-button v-size-width\" style=\"box-sizing: border-box; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; text-align: center; color: #000000; background-color: #ffc25e; border-radius: 0px; -webkit-border-radius: 0px; -moz-border-radius: 0px; width: 42%; max-width: 100%; overflow-wrap: break-word; word-break: break-word; word-wrap: break-word; mso-border-alt: none; font-size: 14px;\" href=\"{{reset_url}}\" target=\"_blank\" rel=\"noopener\"> <span style=\"display: block; padding: 10px 20px; line-height: 120%;\">Reset Password</span> </a></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- Info Text -->\r\n<table id=\"u_content_text_1\" style=\"font-family: \'Raleway\',sans-serif;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td class=\"v-container-padding-padding\" style=\"overflow-wrap: break-word; word-break: break-word; padding: 20px 40px 30px; font-family: \'Raleway\',sans-serif;\" align=\"left\">\r\n<div class=\"v-text-align\" style=\"font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;\"><em><strong>This password reset link will expire in 24 hours</strong></em> <br><br>If you did not request this password reset, please ignore this email or contact support if you have concerns about your account security.</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- Divider Image -->\r\n<table style=\"font-family: \'Raleway\',sans-serif;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td class=\"v-container-padding-padding\" style=\"overflow-wrap: break-word; word-break: break-word; padding: 0px; font-family: \'Raleway\',sans-serif;\" align=\"left\">\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td class=\"v-text-align\" style=\"padding-right: 0px; padding-left: 0px;\" align=\"center\"><img style=\"outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; display: inline-block !important; border: none; height: auto; float: none; width: 90%; max-width: 540px;\" title=\"Divider\" src=\"https://i.ibb.co/GFy4JM6/image-1.png\" alt=\"Divider\" width=\"540\" align=\"center\" border=\"0\"></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n<!-- Footer Section -->\r\n<div class=\"u-row-container\" style=\"padding: 0px; background-color: transparent;\">\r\n<div class=\"u-row\" style=\"margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;\">\r\n<div style=\"border-collapse: collapse; display: table; width: 100%; height: 100%; background-color: transparent;\">\r\n<div class=\"u-col u-col-100\" style=\"max-width: 320px; min-width: 600px; display: table-cell; vertical-align: top;\">\r\n<div style=\"background-color: #8d95ff; height: 100%; width: 100% !important; border-radius: 0px; -webkit-border-radius: 0px; -moz-border-radius: 0px;\">\r\n<div style=\"box-sizing: border-box; height: 100%; padding: 0px;\"><!-- Social Media Icons --><!-- Social Media Icons -->\r\n<table id=\"u_content_social_1\" style=\"font-family: \'Raleway\',sans-serif;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td class=\"v-container-padding-padding\" style=\"overflow-wrap: break-word; word-break: break-word; padding: 50px 10px 10px; font-family: \'Raleway\',sans-serif;\" align=\"left\">\r\n<div style=\"direction: ltr;\" align=\"center\">\r\n<div style=\"display: table; max-width: 167px;\"><!-- Social Icons Container -->\r\n<table style=\"width: 32px !important; height: 32px !important; display: inline-block; border-collapse: collapse; table-layout: fixed; border-spacing: 0; vertical-align: top; margin-right: 10px;\" border=\"0\" width=\"32\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\">\r\n<tbody>\r\n<tr style=\"vertical-align: top;\">\r\n<td style=\"word-break: break-word; border-collapse: collapse !important; vertical-align: top;\" align=\"left\" valign=\"middle\"><a title=\"Facebook\" href=\"https://facebook.com/\" target=\"_blank\" rel=\"noopener\"> <img style=\"outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; display: block !important; border: none; height: auto; float: none; max-width: 32px !important;\" title=\"Facebook\" src=\"https://i.ibb.co/cD3kS6F/image-2.png\" alt=\"Facebook\" width=\"32\"> </a></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style=\"width: 32px !important; height: 32px !important; display: inline-block; border-collapse: collapse; table-layout: fixed; border-spacing: 0; vertical-align: top; margin-right: 10px;\" border=\"0\" width=\"32\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\">\r\n<tbody>\r\n<tr style=\"vertical-align: top;\">\r\n<td style=\"word-break: break-word; border-collapse: collapse !important; vertical-align: top;\" align=\"left\" valign=\"middle\"><a title=\"LinkedIn\" href=\"https://linkedin.com/\" target=\"_blank\" rel=\"noopener\"> <img style=\"outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; display: block !important; border: none; height: auto; float: none; max-width: 32px !important;\" title=\"LinkedIn\" src=\"https://i.ibb.co/xhqvPc8/image-3.png\" alt=\"LinkedIn\" width=\"32\"> </a></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style=\"width: 32px !important; height: 32px !important; display: inline-block; border-collapse: collapse; table-layout: fixed; border-spacing: 0; vertical-align: top; margin-right: 10px;\" border=\"0\" width=\"32\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\">\r\n<tbody>\r\n<tr style=\"vertical-align: top;\">\r\n<td style=\"word-break: break-word; border-collapse: collapse !important; vertical-align: top;\" align=\"left\" valign=\"middle\"><a title=\"Instagram\" href=\"https://instagram.com/\" target=\"_blank\" rel=\"noopener\"> <img style=\"outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; display: block !important; border: none; height: auto; float: none; max-width: 32px !important;\" title=\"Instagram\" src=\"https://i.ibb.co/q0374vh/image-4.png\" alt=\"Instagram\" width=\"32\"> </a></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style=\"width: 32px !important; height: 32px !important; display: inline-block; border-collapse: collapse; table-layout: fixed; border-spacing: 0; vertical-align: top; margin-right: 0px;\" border=\"0\" width=\"32\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\">\r\n<tbody>\r\n<tr style=\"vertical-align: top;\">\r\n<td style=\"word-break: break-word; border-collapse: collapse !important; vertical-align: top;\" align=\"left\" valign=\"middle\"><a title=\"X\" href=\"https://twitter.com/\" target=\"_blank\" rel=\"noopener\"> <img style=\"outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; display: block !important; border: none; height: auto; float: none; max-width: 32px !important;\" title=\"X\" src=\"https://i.ibb.co/5KYnXP0/image-5.png\" alt=\"X\" width=\"32\"> </a></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- Footer Text -->\r\n<table id=\"u_content_text_3\" style=\"font-family: \'Raleway\',sans-serif;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td class=\"v-container-padding-padding\" style=\"overflow-wrap: break-word; word-break: break-word; padding: 10px 100px 30px; font-family: \'Raleway\',sans-serif;\" align=\"left\">\r\n<div class=\"v-text-align\" style=\"font-size: 14px; color: #ffffff; line-height: 170%; text-align: center; word-wrap: break-word;\">\r\n<p style=\"font-size: 14px; line-height: 170%;\">PRIVACY POLICY | TERMS OF SERVICE | SUPPORT</p>\r\n<p style=\"font-size: 14px; line-height: 170%;\">&nbsp;</p>\r\n<p style=\"font-size: 14px; line-height: 170%;\">If you have any questions about your account security, please contact {site_name} support team immediately.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>', NULL, 'name,reset_url', '2025-02-07 16:13:24', '2025-02-08 14:25:46'),
(20, 'App\\Mail\\Hosting\\AccountCreatedMail', 'Your Hosting Account for {{domain}} is Ready', '\r\n                <h1>Welcome to Your New Hosting Account!</h1>\r\n                <p>Your hosting account has been created successfully.</p>\r\n                <p><strong>Account Details:</strong></p>\r\n                <ul>\r\n                    <li>Domain: {{domain}}</li>\r\n                    <li>Username: {{username}}</li>\r\n                    <li>Password: {{password}}</li>\r\n                    <li>Control Panel: {{cpanel_url}}</li>\r\n                    <li>Label: {{label}}</li>\r\n                </ul>\r\n            ', NULL, 'username,password,domain,cpanel_url,label', '2025-02-07 16:13:24', '2025-02-07 16:13:24'),
(21, 'App\\Mail\\Hosting\\AccountDeactivatedMail', 'Hosting Account Deactivated - {{domain}}', '\r\n                <h1>Hosting Account Deactivated</h1>\r\n                <p>Your hosting account has been deactivated:</p>\r\n                <ul>\r\n                    <li>Domain: {{domain}}</li>\r\n                    <li>Username: {{username}}</li>\r\n                    <li>Label: {{label}}</li>\r\n                </ul>\r\n                <p><strong>Reason:</strong> {{reason}}</p>\r\n            ', NULL, 'username,domain,label,reason', '2025-02-07 16:13:24', '2025-02-07 16:13:24'),
(22, 'App\\Mail\\Hosting\\AccountReactivatedMail', 'Hosting Account Reactivated - {{domain}}', '\r\n                <h1>Hosting Account Reactivated</h1>\r\n                <p>Your hosting account has been reactivated:</p>\r\n                <ul>\r\n                    <li>Domain: {{domain}}</li>\r\n                    <li>Username: {{username}}</li>\r\n                    <li>cPanel URL: {{cpanel_url}}</li>\r\n                </ul>\r\n            ', NULL, 'username,domain,cpanel_url', '2025-02-07 16:13:24', '2025-02-07 16:13:24'),
(23, 'App\\Mail\\Ticket\\NewTicketMail', '[Ticket #{{ticket_id}}] {{title}}', '\r\n                <h1>New Support Ticket</h1>\r\n                <p><strong>Ticket ID:</strong> #{{ticket_id}}</p>\r\n                <p><strong>Subject:</strong> {{title}}</p>\r\n                <p><strong>Category:</strong> {{category}}</p>\r\n                <p><strong>Priority:</strong> {{priority}}</p>\r\n                <p><strong>Service:</strong> {{service_info}}</p>\r\n                <p><strong>Message:</strong><br>{{message}}</p>\r\n            ', NULL, 'ticket_id,title,message,category,priority,service_type,service_info', '2025-02-07 16:13:24', '2025-02-07 16:13:24'),
(24, 'App\\Mail\\Ticket\\TicketReplyMail', 'Re: [Ticket #{{ticket_id}}] {{title}}', '\r\n                <h1>New Reply to Your Ticket</h1>\r\n                <p><strong>Ticket ID:</strong> #{{ticket_id}}</p>\r\n                <p><strong>Subject:</strong> {{title}}</p>\r\n                <p><strong>Reply from {{replier}}:</strong><br>{{reply}}</p>\r\n            ', NULL, 'ticket_id,title,reply,replier', '2025-02-07 16:13:24', '2025-02-07 16:13:24'),
(25, 'App\\Mail\\Ticket\\TicketStatusChangedMail', 'Status Changed: [Ticket #{{ticket_id}}] {{title}}', '\r\n                <h1>Ticket Status Updated</h1>\r\n                <p><strong>Ticket ID:</strong> #{{ticket_id}}</p>\r\n                <p><strong>Subject:</strong> {{title}}</p>\r\n                <p><strong>Status changed from:</strong> {{old_status}} to {{new_status}}</p>\r\n            ', NULL, 'ticket_id,title,old_status,new_status', '2025-02-07 16:13:24', '2025-02-07 16:13:24'),
(26, 'App\\Mail\\Auth\\NewDeviceLoginMail', 'New Login Detected on Your Account', '<div class=\"container\">\r\n<div class=\"header\">\r\n<h1>Security Alert</h1>\r\n</div>\r\n<div class=\"content\">\r\n<p>Hello {{ name }},</p>\r\n<div class=\"alert-box\"><strong>We detected a new login to your account from a device that you haven\'t used before.</strong></div>\r\n<p>Here are the details of the login:</p>\r\n<table>\r\n<tbody>\r\n<tr>\r\n<th>Time</th>\r\n<td>{{time}}</td>\r\n</tr>\r\n<tr>\r\n<th>IP Address</th>\r\n<td>{{ip_address}}</td>\r\n</tr>\r\n<tr>\r\n<th>Device</th>\r\n<td>{{device}}</td>\r\n</tr>\r\n<tr>\r\n<th>Browser</th>\r\n<td>{{browser}}</td>\r\n</tr>\r\n<tr>\r\n<th>Location</th>\r\n<td>{{location}}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>If this was you, you can ignore this email. If you don\'t recognize this login, please change your password immediately and contact support.</p>\r\n<p>Best regards,<br>Support Team</p>\r\n</div>\r\n<div class=\"footer\">\r\n<p>This is an automated message, please do not reply directly to this email.</p>\r\n</div>\r\n</div>', NULL, NULL, '2025-03-06 09:25:32', '2025-03-06 11:39:02'),
(27, 'App\\Mail\\Auth\\FailedLoginMail', 'Failed Login Attempt on Your Account', '<div class=\"container\">\r\n<div class=\"header\">\r\n<h1>Security Alert</h1>\r\n</div>\r\n<div class=\"content\">\r\n<p>Hello {{ $name }},</p>\r\n<div class=\"alert-box\"><strong>We detected a failed login attempt to your account. This might be a sign that someone is trying to gain unauthorized access.</strong></div>\r\n<p>Here are the details of the login attempt:</p>\r\n<table>\r\n<tbody>\r\n<tr>\r\n<th>Time</th>\r\n<td>{{time}}</td>\r\n</tr>\r\n<tr>\r\n<th>IP Address</th>\r\n<td>{{ip_address}}</td>\r\n</tr>\r\n<tr>\r\n<th>Device</th>\r\n<td>{{device}}</td>\r\n</tr>\r\n<tr>\r\n<th>Browser</th>\r\n<td>{{browser}}</td>\r\n</tr>\r\n<tr>\r\n<th>Location</th>\r\n<td>{{location}}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>If this was you, make sure you\'re using the correct password. If you\'re concerned about the security of your account, we recommend changing your password immediately.</p>\r\n<p>Best regards,<br>Support Team</p>\r\n</div>\r\n<div class=\"footer\">\r\n<p>This is an automated message, please do not reply directly to this email.</p>\r\n</div>\r\n</div>', NULL, NULL, '2025-03-06 09:25:32', '2025-03-06 11:41:32'),
(28, 'App\\Mail\\Admin\\BulkNotification', '{{ config(\"app.name\") }} - Notification', '<!DOCTYPE html>\r\n<html>\r\n\r\n<head>\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n  <title>{{ config(\'app.name\') }} - Notification</title>\r\n  <style>\r\n    @media only screen and (max-width: 620px) {\r\n        table.body h1 {\r\n            font-size: 28px !important;\r\n            margin-bottom: 10px !important;\r\n        }\r\n    \r\n        table.body p,\r\n        table.body ul,\r\n        table.body ol,\r\n        table.body td,\r\n        table.body span,\r\n        table.body a {\r\n            font-size: 16px !important;\r\n        }\r\n    \r\n        table.body .wrapper,\r\n        table.body .article {\r\n            padding: 10px !important;\r\n        }\r\n    \r\n        table.body .content {\r\n            padding: 0 !important;\r\n        }\r\n    \r\n        table.body .container {\r\n            padding: 0 !important;\r\n            width: 100% !important;\r\n        }\r\n    \r\n        table.body .main {\r\n            border-left-width: 0 !important;\r\n            border-radius: 0 !important;\r\n            border-right-width: 0 !important;\r\n        }\r\n    \r\n        table.body .btn table {\r\n            width: 100% !important;\r\n        }\r\n    \r\n        table.body .btn a {\r\n            width: 100% !important;\r\n        }\r\n    \r\n        table.body .img-responsive {\r\n            height: auto !important;\r\n            max-width: 100% !important;\r\n            width: auto !important;\r\n        }\r\n    }\r\n    \r\n    @media all {\r\n        .ExternalClass {\r\n            width: 100%;\r\n        }\r\n    \r\n        .ExternalClass,\r\n        .ExternalClass p,\r\n        .ExternalClass span,\r\n        .ExternalClass font,\r\n        .ExternalClass td,\r\n        .ExternalClass div {\r\n            line-height: 100%;\r\n        }\r\n    \r\n        .apple-link a {\r\n            color: inherit !important;\r\n            font-family: inherit !important;\r\n            font-size: inherit !important;\r\n            font-weight: inherit !important;\r\n            line-height: inherit !important;\r\n            text-decoration: none !important;\r\n        }\r\n    \r\n        #MessageViewBody a {\r\n            color: inherit;\r\n            text-decoration: none;\r\n            font-size: inherit;\r\n            font-family: inherit;\r\n            font-weight: inherit;\r\n            line-height: inherit;\r\n        }\r\n    \r\n        .btn-primary table td:hover {\r\n            background-color: #34495e !important;\r\n        }\r\n    \r\n        .btn-primary a:hover {\r\n            background-color: #34495e !important;\r\n            border-color: #34495e !important;\r\n        }\r\n    }\r\n  </style>\r\n</head>\r\n\r\n<body\r\n  style=\"background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;\">\r\n  <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"\r\n    class=\"body\"\r\n    style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;\"\r\n    width=\"100%\" bgcolor=\"#f6f6f6\">\r\n    <tr>\r\n      <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\"\r\n        valign=\"top\">&nbsp;</td>\r\n      <td class=\"container\"\r\n        style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;\"\r\n        width=\"580\" valign=\"top\">\r\n        <div class=\"content\"\r\n          style=\"box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;\">\r\n\r\n          <!-- START CENTERED WHITE CONTAINER -->\r\n          <table role=\"presentation\" class=\"main\"\r\n            style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #ffffff; border-radius: 3px; width: 100%;\"\r\n            width=\"100%\">\r\n            <!-- START MAIN CONTENT AREA -->\r\n            <tr>\r\n              <td class=\"wrapper\"\r\n                style=\"font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;\"\r\n                valign=\"top\">\r\n                <table role=\"presentation\" border=\"0\" cellpadding=\"0\"\r\n                  cellspacing=\"0\"\r\n                  style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;\"\r\n                  width=\"100%\">\r\n                  <tr>\r\n                    <td\r\n                      style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\"\r\n                      valign=\"top\">\r\n                      <!-- Company logo -->\r\n                      <div style=\"text-align: center; margin-bottom: 20px;\">\r\n                        <img src=\"{{ asset(\'images/logo.png\') }}\"\r\n                          alt=\"{{ config(\'app.name\') }}\"\r\n                          style=\"max-width: 120px; height: auto;\">\r\n                      </div>\r\n\r\n                      <!-- Email content -->\r\n                      <div style=\"margin-top: 20px;\">\r\n{{{ emailContent }}}\r\n                      </div>\r\n\r\n                      <!-- Optional footer with links -->\r\n                      <div\r\n                        style=\"margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #777; font-size: 13px;\">\r\n                        <p>\r\n                          If you have any questions, please visit our <a\r\n                            href=\"{{ route(\'knowledge.index\') }}\"\r\n                            style=\"color: #3490dc; text-decoration: underline;\">Knowledge\r\n                            Base</a>\r\n                          or contact our <a\r\n                            href=\"{{ route(\'user.tickets.create\') }}\"\r\n                            style=\"color: #3490dc; text-decoration: underline;\">Support\r\n                            team</a>.\r\n                        </p>\r\n                      </div>\r\n                    </td>\r\n                  </tr>\r\n                </table>\r\n              </td>\r\n            </tr>\r\n          </table>\r\n\r\n          <!-- START FOOTER -->\r\n          <div class=\"footer\"\r\n            style=\"clear: both; margin-top: 10px; text-align: center; width: 100%;\">\r\n            <table role=\"presentation\" border=\"0\" cellpadding=\"0\"\r\n              cellspacing=\"0\"\r\n              style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;\"\r\n              width=\"100%\">\r\n              <tr>\r\n                <td class=\"content-block\"\r\n                  style=\"font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #999999; font-size: 12px; text-align: center;\"\r\n                  valign=\"top\" align=\"center\">\r\n                  <span class=\"apple-link\"\r\n                    style=\"color: #999999; font-size: 12px; text-align: center;\">{{ config(\'app.name\') }},\r\n                    {{ date(\'Y\') }}</span>\r\n                  <br> Don\'t like these emails? aaaa<a\r\n                    href=\"{{ route(\'profile\') }}\"\r\n                    style=\"text-decoration: underline; color: #999999; font-size: 12px; text-align: center;\">Update\r\n                    your notification preferences</a>.\r\n                </td>\r\n              </tr>\r\n            </table>\r\n          </div>\r\n          <!-- END FOOTER -->\r\n\r\n        </div>\r\n      </td>\r\n      <td style=\"font-family: sans-serif; font-size: 14px; vertical-align: top;\"\r\n        valign=\"top\">&nbsp;</td>\r\n    </tr>\r\n  </table>\r\n</body>\r\n\r\n</html>', NULL, NULL, '2025-03-26 06:40:54', '2025-03-26 11:14:25'),
(30, 'App\\Mail\\Admin\\MigrationPasswordResetMail', 'New Account Password - {{ $site_name }}', '<!DOCTYPE html>\n<html>\n<head>\n    <meta charset=\"utf-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>New Account Password</title>\n    <style>\n        body {\n            font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;\n            line-height: 1.6;\n            color: #333;\n            margin: 0;\n            padding: 0;\n            background-color: #f5f5f5;\n        }\n        .container {\n            max-width: 600px;\n            margin: 0 auto;\n            padding: 20px;\n            background-color: #ffffff;\n            border-radius: 8px;\n            box-shadow: 0 2px 5px rgba(0,0,0,0.1);\n        }\n        .header {\n            text-align: center;\n            padding: 20px 0;\n            border-bottom: 2px solid #f0f0f0;\n        }\n        .header h1 {\n            color: #3f51b5;\n            margin: 0;\n            font-size: 24px;\n        }\n        .content {\n            padding: 20px 0;\n        }\n        .credentials {\n            background-color: #f1f5f9;\n            border-radius: 5px;\n            padding: 20px;\n            margin: 20px 0;\n        }\n        .credentials p {\n            margin: 10px 0;\n        }\n        .credentials strong {\n            display: inline-block;\n            width: 100px;\n        }\n        .note {\n            background-color: #fffde7;\n            border-left: 4px solid #ffc107;\n            padding: 10px 15px;\n            margin-top: 20px;\n        }\n        .button {\n            display: inline-block;\n            background-color: #3f51b5;\n            color: white;\n            text-decoration: none;\n            padding: 12px 25px;\n            border-radius: 4px;\n            margin: 20px 0;\n            font-weight: bold;\n        }\n        .footer {\n            text-align: center;\n            padding-top: 20px;\n            border-top: 1px solid #f0f0f0;\n            color: #666;\n            font-size: 12px;\n        }\n    </style>\n</head>\n<body>\n    <div class=\"container\">\n        <div class=\"header\">\n            <h1>{{ $site_name }}</h1>\n        </div>\n        \n        <div class=\"content\">\n            <p>Hello {{ $name }},</p>\n            \n            <p>Your account has been successfully migrated to our new system. As part of this process, your password has been reset for security reasons.</p>\n            \n            <div class=\"credentials\">\n                <p><strong>Username:</strong> {{ $email }}</p>\n                <p><strong>Password:</strong> {{ $password }}</p>\n                <p><strong>Account Type:</strong> {{ ucfirst($role) }}</p>\n            </div>\n            \n            <p>You can now log in using the new credentials above:</p>\n            \n            <div style=\"text-align: center;\">\n                <a href=\"{{ $login_url }}\" class=\"button\">Login to Your Account</a>\n            </div>\n            \n            <div class=\"note\">\n                <p><strong>Important:</strong> For security purposes, we recommend that you change your password immediately after logging in.</p>\n            </div>\n            \n            <p>If you did not expect this email or have any questions, please contact our support team.</p>\n            \n            <p>Thank you,<br>{{ $site_name }} Team</p>\n        </div>\n        \n        <div class=\"footer\">\n            <p>This is an automated message, please do not reply directly to this email.</p>\n            <p>&copy; {{ date(\'Y\') }} {{ $site_name }}. All rights reserved.</p>\n        </div>\n    </div>\n</body>\n</html>', NULL, NULL, '2025-03-26 06:40:54', '2025-03-26 06:40:54');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mofh_api_settings`
--

CREATE TABLE `mofh_api_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `api_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpanel_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `content_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_text_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_settings`
--

CREATE TABLE `oauth_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_secret` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_token` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `popup_notifications`
--

CREATE TABLE `popup_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `allow_dismiss` tinyint(1) NOT NULL DEFAULT '1',
  `show_once` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `popup_notification_users`
--

CREATE TABLE `popup_notification_users` (
  `id` bigint UNSIGNED NOT NULL,
  `popup_notification_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `dismissed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `site_pro_settings`
--

CREATE TABLE `site_pro_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `hostname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SMTP',
  `hostname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int NOT NULL,
  `encryption` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tls',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_ratings`
--

CREATE TABLE `staff_ratings` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `message_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `admin_id` bigint UNSIGNED NOT NULL,
  `rating` int NOT NULL COMMENT 'Đánh giá từ 1-5 sao',
  `comment` text COLLATE utf8mb4_unicode_ci COMMENT 'Nhận xét của người dùng',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'low',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `is_resolved` tinyint(1) NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `service_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `social_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `google2fa_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale_auto_detected` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `social_id`, `social_type`, `avatar`, `role`, `email_verified_at`, `password`, `remember_token`, `locale`, `signature`, `created_at`, `updated_at`, `google2fa_secret`, `locale_auto_detected`) VALUES
(1, 'Bixa Cloud', 'demo@bixa.app', NULL, NULL, NULL, 'admin', '2025-03-26 05:35:10', '$2y$10$y2EC4jqDVDYc2hPvKRUNMuOb6BOPYQaggWgm7dPTvQALqM/ZgobPy', 'badPH6WfaiILSVsyctNhD8bvabjoih1rX0USPn3BpFtHvGQp8Dzc712S73Q0', 'en', NULL, '2025-03-26 05:34:26', '2025-05-21 03:18:51', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `web_ftp_settings`
--

CREATE TABLE `web_ftp_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `use_external_service` tinyint(1) NOT NULL DEFAULT '0',
  `editor_theme` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monokai',
  `code_beautify` tinyint(1) NOT NULL DEFAULT '1',
  `code_suggestion` tinyint(1) NOT NULL DEFAULT '1',
  `auto_complete` tinyint(1) NOT NULL DEFAULT '1',
  `max_upload_size` int NOT NULL DEFAULT '10',
  `allow_zip_operations` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ad_slots`
--
ALTER TABLE `ad_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ad_slots_code_unique` (`code`);

--
-- Indexes for table `allowed_domains`
--
ALTER TABLE `allowed_domains`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `allowed_domains_domain_name_unique` (`domain_name`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `authentication_log`
--
ALTER TABLE `authentication_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `authentication_log_authenticatable_type_authenticatable_id_index` (`authenticatable_type`,`authenticatable_id`);

--
-- Indexes for table `auth_log_settings`
--
ALTER TABLE `auth_log_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `certificates_user_id_foreign` (`user_id`);

--
-- Indexes for table `cloudflare_configs`
--
ALTER TABLE `cloudflare_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hosting_accounts`
--
ALTER TABLE `hosting_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hosting_accounts_username_unique` (`username`),
  ADD UNIQUE KEY `hosting_accounts_key_unique` (`key`),
  ADD KEY `hosting_accounts_user_id_foreign` (`user_id`);

--
-- Indexes for table `icon_captcha_settings`
--
ALTER TABLE `icon_captcha_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `icon_captcha_settings_key_unique` (`key`);

--
-- Indexes for table `iperf_servers`
--
ALTER TABLE `iperf_servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `iperf_servers_country_code_index` (`country_code`),
  ADD KEY `iperf_servers_is_active_index` (`is_active`);

--
-- Indexes for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `knowledge_articles_slug_unique` (`slug`),
  ADD KEY `knowledge_articles_category_id_foreign` (`category_id`),
  ADD KEY `knowledge_articles_user_id_foreign` (`user_id`);

--
-- Indexes for table `knowledge_categories`
--
ALTER TABLE `knowledge_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `knowledge_categories_slug_unique` (`slug`);

--
-- Indexes for table `knowledge_ratings`
--
ALTER TABLE `knowledge_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `knowledge_ratings_article_id_user_id_unique` (`article_id`,`user_id`),
  ADD KEY `knowledge_ratings_user_id_foreign` (`user_id`);

--
-- Indexes for table `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mail_templates`
--
ALTER TABLE `mail_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mofh_api_settings`
--
ALTER TABLE `mofh_api_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_is_read_created_at_index` (`user_id`,`is_read`,`created_at`),
  ADD KEY `notifications_type_user_id_index` (`type`,`user_id`),
  ADD KEY `notifications_created_at_index` (`created_at`);

--
-- Indexes for table `oauth_settings`
--
ALTER TABLE `oauth_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`),
  ADD UNIQUE KEY `short_token` (`short_token`),
  ADD UNIQUE KEY `short_token_2` (`short_token`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `popup_notifications`
--
ALTER TABLE `popup_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `popup_notification_users`
--
ALTER TABLE `popup_notification_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `popup_notification_users_popup_notification_id_user_id_unique` (`popup_notification_id`,`user_id`),
  ADD KEY `popup_notification_users_user_id_foreign` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `site_pro_settings`
--
ALTER TABLE `site_pro_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_ratings`
--
ALTER TABLE `staff_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_ratings_message_id_user_id_unique` (`message_id`,`user_id`),
  ADD KEY `staff_ratings_ticket_id_foreign` (`ticket_id`),
  ADD KEY `staff_ratings_user_id_foreign` (`user_id`),
  ADD KEY `staff_ratings_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tickets_category_id_foreign` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `web_ftp_settings`
--
ALTER TABLE `web_ftp_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertisements`
--
ALTER TABLE `advertisements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ad_slots`
--
ALTER TABLE `ad_slots`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allowed_domains`
--
ALTER TABLE `allowed_domains`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `authentication_log`
--
ALTER TABLE `authentication_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_log_settings`
--
ALTER TABLE `auth_log_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cloudflare_configs`
--
ALTER TABLE `cloudflare_configs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hosting_accounts`
--
ALTER TABLE `hosting_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `icon_captcha_settings`
--
ALTER TABLE `icon_captcha_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iperf_servers`
--
ALTER TABLE `iperf_servers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2180;

--
-- AUTO_INCREMENT for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_categories`
--
ALTER TABLE `knowledge_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_ratings`
--
ALTER TABLE `knowledge_ratings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `labels`
--
ALTER TABLE `labels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mail_templates`
--
ALTER TABLE `mail_templates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mofh_api_settings`
--
ALTER TABLE `mofh_api_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_settings`
--
ALTER TABLE `oauth_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `popup_notifications`
--
ALTER TABLE `popup_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `popup_notification_users`
--
ALTER TABLE `popup_notification_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_pro_settings`
--
ALTER TABLE `site_pro_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_ratings`
--
ALTER TABLE `staff_ratings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `web_ftp_settings`
--
ALTER TABLE `web_ftp_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hosting_accounts`
--
ALTER TABLE `hosting_accounts`
  ADD CONSTRAINT `hosting_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  ADD CONSTRAINT `knowledge_articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `knowledge_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_ratings`
--
ALTER TABLE `knowledge_ratings`
  ADD CONSTRAINT `knowledge_ratings_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `popup_notification_users`
--
ALTER TABLE `popup_notification_users`
  ADD CONSTRAINT `popup_notification_users_popup_notification_id_foreign` FOREIGN KEY (`popup_notification_id`) REFERENCES `popup_notifications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `popup_notification_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_ratings`
--
ALTER TABLE `staff_ratings`
  ADD CONSTRAINT `staff_ratings_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_ratings_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
