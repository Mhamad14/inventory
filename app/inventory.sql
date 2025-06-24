-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 03:30 AM
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
-- Database: `inventory`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetOrderDetails` (IN `order_id` INT)   BEGIN
                SELECT 
                    o.*,
                    ((o.final_total - o.returns_total) - o.amount_paid) AS debt,
                    
                    (SELECT u.first_name 
                     FROM customers c 
                     JOIN users u ON u.id = c.user_id 
                     WHERE c.id = o.customer_id 
                     LIMIT 1) AS customer_name,

                    (SELECT first_name 
                     FROM users 
                     WHERE users.id = o.created_by 
                     LIMIT 1) AS creator_name,

                    (SELECT g.name 
                     FROM users_groups ug 
                     JOIN groups g ON g.id = ug.group_id 
                     WHERE ug.user_id = o.created_by 
                     LIMIT 1) AS creator_role

                FROM orders o
                WHERE o.id = order_id;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_PartialCustomerPayment` (IN `in_payment_amount` DECIMAL(10,2), IN `in_customer_id` INT, IN `in_business_id` INT)   BEGIN
                DECLARE done INT DEFAULT 0;
                DECLARE v_order_id INT;
                DECLARE v_final_total DECIMAL(10,2);
                DECLARE v_amount_paid DECIMAL(10,2);
                DECLARE v_remaining DECIMAL(10,2);

                DECLARE cur CURSOR FOR 
                    SELECT id, final_total, amount_paid
                    FROM orders
                    WHERE customer_id = in_customer_id
                      AND business_id = in_business_id
                      AND payment_status IN ('unpaid', 'partially_paid')
                    ORDER BY created_at ASC;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                OPEN cur;

                read_loop: LOOP
                    FETCH cur INTO v_order_id, v_final_total, v_amount_paid;
                    IF done THEN
                        LEAVE read_loop;
                    END IF;

                    SET v_remaining = v_final_total - v_amount_paid;

                    IF in_payment_amount >= v_remaining THEN
                        UPDATE orders
                        SET amount_paid = final_total,
                            payment_status = 'fully_paid'
                        WHERE id = v_order_id;

                        SET in_payment_amount = in_payment_amount - v_remaining;
                    ELSE
                        UPDATE orders
                        SET amount_paid = amount_paid + in_payment_amount,
                            payment_status = 'partially_paid'
                        WHERE id = v_order_id;

                        SET in_payment_amount = 0;
                        LEAVE read_loop;
                    END IF;
                END LOOP;

                CLOSE cur;
            END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) UNSIGNED NOT NULL,
  `business_id` int(11) UNSIGNED NOT NULL,
  `vendor_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `business_id`, `vendor_id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 1, 'مریشک', 'asdas', '2025-04-01 02:58:55', '2025-04-01 02:58:55', NULL),
(2, 5, 1, 'Nokia', 'for sale', '2025-05-26 18:35:39', '2025-05-26 18:35:39', NULL),
(3, 5, 1, 'DELL Latitude', 'for sale', '2025-05-26 18:35:58', '2025-05-26 18:35:58', NULL),
(4, 5, 1, 'ASUS AIO', 'all in one pcs', '2025-05-26 18:36:31', '2025-05-26 18:36:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `address` varchar(2048) NOT NULL,
  `contact` varchar(64) NOT NULL,
  `tax_name` varchar(256) NOT NULL,
  `tax_value` varchar(256) NOT NULL,
  `bank_details` varchar(2048) NOT NULL,
  `default_business` tinyint(2) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `email` varchar(64) NOT NULL,
  `website` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`id`, `user_id`, `name`, `icon`, `description`, `address`, `contact`, `tax_name`, `tax_value`, `bank_details`, `default_business`, `status`, `email`, `website`, `created_at`, `updated_at`) VALUES
(5, 1, 'Mhamad', 'public/uploads/business/0f2c91febe39793fac9fccd3837b9119.jpg', 'o kary dasty', 'slemani', '7741527601', 'slaw', '500', 'asdsad', 1, 1, 'commandermhamad@yahoo.com', 'nimana', '2025-03-31 21:09:45', '2025-04-14 15:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `status` int(2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `vendor_id`, `business_id`, `name`, `status`, `updated_at`, `created_at`) VALUES
(1, 0, 0, 0, 'general', 1, '2022-01-13 05:55:49', '2022-01-13 05:55:49'),
(13, 0, 1, 5, 'دانە', 1, '2025-03-31 21:26:18', '2025-03-31 21:26:18'),
(14, 13, 1, 5, 'پاکەت', 1, '2025-03-31 21:26:31', '2025-03-31 21:26:31'),
(15, 14, 1, 5, 'SLAW', 1, '2025-05-26 13:04:54', '2025-03-31 21:26:45'),
(16, 0, 1, 5, 'asd', 1, '2025-03-31 21:27:28', '2025-03-31 21:27:28'),
(17, 0, 1, 5, 'Mobile', 1, '2025-05-26 13:03:26', '2025-05-26 13:03:26'),
(18, 0, 1, 5, 'Computer', 1, '2025-05-26 13:03:40', '2025-05-26 13:03:40'),
(19, 18, 1, 5, 'PC Parts', 1, '2025-05-26 13:04:22', '2025-05-26 13:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(3) NOT NULL COMMENT 'ISO 4217 code (IQD, USD, etc)',
  `name` varchar(50) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `symbol_position` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = before, 1 = after',
  `decimal_places` tinyint(4) NOT NULL DEFAULT 2,
  `is_base` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 = base currency (IQD)',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `business_id`, `code`, `name`, `symbol`, `symbol_position`, `decimal_places`, `is_base`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 'IQD', 'دینار', 'د.ع', 0, 3, 1, 1, '2025-06-15 14:43:09', '2025-06-17 05:09:07', NULL),
(2, 5, 'USD', 'Dollar', '$', 0, 0, 0, 1, '2025-06-15 14:43:31', '2025-06-15 14:43:31', NULL),
(3, 5, 'EUR', 'Euro', '€', 0, 0, 0, 1, '2025-06-15 20:10:39', '2025-06-15 20:10:39', NULL),
(4, 5, 'RLE', 'Rial', 'R', 0, 3, 0, 1, '2025-06-17 04:52:54', '2025-06-17 04:52:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `balance` double NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `business_id`, `vendor_id`, `balance`, `created_by`, `status`, `created_at`, `updated_at`) VALUES
(2, 106, 5, 1, 0, NULL, 1, '2025-03-31 21:23:33', '2025-05-03 13:47:23'),
(3, 107, 5, 1, 0, 1, 1, '2025-03-31 21:42:56', '2025-03-31 21:42:56'),
(4, 111, 5, 1, 0, NULL, 1, '2025-04-29 21:38:39', '2025-04-29 21:38:39'),
(5, 112, 5, 1, 0, NULL, 1, '2025-04-29 21:39:11', '2025-04-29 21:39:11'),
(6, 113, 5, 1, 0, NULL, 1, '2025-04-29 21:40:11', '2025-04-29 21:40:11');

-- --------------------------------------------------------

--
-- Table structure for table `customers_transactions`
--

CREATE TABLE `customers_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'References ID of the user',
  `business_id` int(11) NOT NULL COMMENT 'References ID of the business',
  `vendor_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL COMMENT 'For purchase orders to store supplier info',
  `order_id` int(11) NOT NULL,
  `created_by` varchar(64) NOT NULL,
  `payment_for` int(11) DEFAULT NULL COMMENT '0 = sales, 1 = purchases, 2 = wallet',
  `payment_type` varchar(64) NOT NULL,
  `transaction_type` varchar(256) NOT NULL COMMENT 'credit | debit - for payment against customer wallet or purchase orders - blank for sales orders	',
  `order_type` varchar(256) NOT NULL,
  `amount` double NOT NULL,
  `opening_balance` double NOT NULL,
  `closing_balance` double NOT NULL,
  `transaction_id` varchar(148) NOT NULL,
  `message` varchar(264) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers_transactions`
--

INSERT INTO `customers_transactions` (`id`, `user_id`, `business_id`, `vendor_id`, `customer_id`, `supplier_id`, `order_id`, `created_by`, `payment_for`, `payment_type`, `transaction_type`, `order_type`, `amount`, `opening_balance`, `closing_balance`, `transaction_id`, `message`, `created_at`, `updated_at`) VALUES
(4, 106, 5, 1, 2, 0, 0, '1', 2, 'cash', 'credit', '', 100, 0, 100, '', 'dwfsd', '2025-03-31 21:24:31', '2025-03-31 21:24:31'),
(5, 106, 5, 1, 2, 0, 0, '1', 2, 'cash', 'debit', '', 50, 100, 50, '', 'dsfs', '2025-03-31 21:25:24', '2025-03-31 21:25:24'),
(6, 0, 0, 1, 2, 0, 41, '1', NULL, 'cash', '', '', 200, 0, 0, '', '', '2025-03-31 22:00:15', '2025-03-31 22:00:15'),
(7, 106, 5, 1, 2, 0, 41, '1', 0, 'cash', 'credit', '', 300, 0, 300, '', '', '2025-03-31 22:03:13', '2025-03-31 22:03:13'),
(8, 0, 0, 1, 2, 0, 43, '1', NULL, 'cash', '', '', 17000, 0, 0, '', '', '2025-03-31 22:06:53', '2025-03-31 22:06:53'),
(9, 106, 5, 1, 2, 0, 43, '1', 0, 'cash', 'credit', '', 163000, 0, 163000, '', 'asdsad', '2025-03-31 22:08:40', '2025-03-31 22:08:40'),
(10, 0, 0, 1, 2, 0, 44, '1', NULL, 'cash', '', '', 400, 0, 0, '', '', '2025-04-02 21:22:51', '2025-04-02 21:22:51'),
(11, 106, 5, 1, 2, 0, 44, '1', 0, 'cash', 'credit', '', 56, 0, 56, '', 'sdfsdf', '2025-04-02 21:23:41', '2025-04-02 21:23:41'),
(12, 0, 0, 1, 2, 0, 45, '1', NULL, 'cash', '', '', 300, 0, 0, '', '', '2025-04-02 21:24:34', '2025-04-02 21:24:34'),
(13, 106, 5, 1, 2, 0, 45, '1', 0, 'cash', 'credit', '', 200, 0, 200, '', 'sdfsdf', '2025-04-02 21:25:13', '2025-04-02 21:25:13'),
(14, 0, 0, 1, 2, 0, 46, '1', NULL, 'wallet', '', '', 5, 0, 0, '657876', '', '2025-04-02 22:06:05', '2025-04-02 22:06:05'),
(15, 0, 0, 1, 2, 0, 47, '1', NULL, 'cash', '', '', 100, 0, 0, '', '', '2025-04-03 14:12:33', '2025-04-03 14:12:33'),
(16, 106, 5, 1, 2, 0, 47, '1', 0, 'cash', 'debit', '', 45, 0, 45, '', '', '2025-04-03 14:13:08', '2025-04-03 14:13:08'),
(17, 106, 5, 1, 2, 0, 47, '1', 0, 'cash', 'credit', '', 300, 0, 300, '', '', '2025-04-03 14:13:35', '2025-04-03 14:13:35'),
(18, 106, 5, 1, 2, 0, 47, '1', 0, 'cash', 'credit', '', 5, 0, 5, '', '', '2025-04-03 14:13:53', '2025-04-03 14:13:53'),
(19, 0, 0, 1, 2, 0, 48, '1', NULL, 'cash', '', '', 300, 0, 0, '', '', '2025-04-04 23:55:21', '2025-04-04 23:55:21'),
(20, 106, 5, 1, 2, 0, 48, '1', 0, 'cash', 'credit', '', 100, 0, 100, '', '100 yawa', '2025-04-04 23:56:16', '2025-04-04 23:56:16'),
(21, 0, 0, 1, 2, 0, 49, '1', NULL, 'cash', '', '', 10, 0, 0, '', '', '2025-04-06 01:34:06', '2025-04-06 01:34:06'),
(22, 106, 5, 1, 2, 0, 50, '1', 0, 'cash', 'credit', '', 50, 0, 50, '', 'sdf', '2025-04-07 11:39:32', '2025-04-07 11:39:32'),
(23, 0, 0, 1, 3, 0, 58, '1', NULL, 'cash', '', '', 20, 0, 0, '', '', '2025-04-10 18:02:11', '2025-04-10 18:02:11'),
(24, 107, 5, 1, 3, 0, 58, '1', 0, 'cash', 'credit', '', 30, 0, 30, '', 'dfsdfdsf', '2025-04-10 18:03:35', '2025-04-10 18:03:35'),
(25, 107, 5, 1, 3, 0, 58, '1', 0, 'cash', 'credit', '', 877, 0, 877, '', 'sdf', '2025-04-14 12:38:19', '2025-04-14 12:38:19'),
(26, 3, 5, 1, 2, 0, 63, '1', NULL, 'cash', '', '', 100, 0, 0, '', '', '2025-04-21 17:46:04', '2025-04-22 23:22:40'),
(27, 2, 0, 1, 2, 0, 64, '1', NULL, 'cash', '', '', 100, 0, 0, '', '', '2025-04-21 17:46:17', '2025-04-22 23:22:07'),
(28, 0, 0, 1, 3, 0, 65, '1', NULL, 'cash', '', '', 49, 0, 0, '', '', '2025-04-22 23:50:58', '2025-04-22 23:50:58'),
(29, 107, 5, 1, 3, 0, 65, '1', 0, 'cash', 'credit', '', 50, 0, 50, '', 'dsfdfds', '2025-04-22 23:51:23', '2025-04-22 23:51:23'),
(30, 0, 0, 1, 2, 0, 69, '1', NULL, 'cash', '', '', 10, 0, 0, '', '', '2025-05-03 23:02:13', '2025-05-03 23:02:13');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_boys`
--

CREATE TABLE `delivery_boys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `business_id` varchar(30) NOT NULL,
  `permissions` varchar(512) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_boys`
--

INSERT INTO `delivery_boys` (`id`, `user_id`, `vendor_id`, `business_id`, `permissions`, `status`, `created_at`, `updated_at`) VALUES
(2, 109, 1, '5', '{\"customer_permission\":\"1\",\"transaction_permission\":\"1\",\"orders_permission\":\"1\"}', 1, '2025-03-31 22:10:37', '2025-03-31 22:10:37'),
(3, 105, 1, '5', '{\"customer_permission\":\"0\",\"transaction_permission\":\"0\",\"orders_permission\":\"1\"}', 1, '2025-04-16 22:04:10', '2025-04-16 22:04:10');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `position_id` int(11) NOT NULL,
  `address` varchar(150) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `salary` int(11) NOT NULL,
  `busniess_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `rate` double NOT NULL,
  `effective_date` datetime NOT NULL COMMENT 'Exact datetime when rate became effective',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exchange_rates`
--

INSERT INTO `exchange_rates` (`id`, `currency_id`, `rate`, `effective_date`, `created_at`) VALUES
(4, 2, 1420, '2025-06-15 16:38:41', '2025-06-15 17:38:41'),
(5, 3, 1510, '2025-06-15 16:40:59', '2025-06-15 17:41:14'),
(10, 2, 1450, '2025-06-15 15:11:26', '2025-06-15 20:41:26'),
(25, 2, 1460, '2025-06-15 15:31:56', '2025-06-15 21:01:56'),
(26, 2, 1470, '2025-06-15 15:38:15', '2025-06-15 21:08:15'),
(27, 3, 1520, '2025-06-15 15:38:26', '2025-06-15 21:08:26'),
(28, 2, 1480, '2025-06-15 15:38:41', '2025-06-15 21:08:41'),
(29, 3, 1530, '2025-06-15 15:38:41', '2025-06-15 21:08:41'),
(30, 3, 1550, '2025-06-15 15:39:00', '2025-06-15 21:09:00'),
(31, 3, 1520, '2025-06-15 15:39:07', '2025-06-15 21:09:07'),
(32, 2, 1490, '2025-06-15 23:17:56', '2025-06-16 04:47:56'),
(33, 2, 1470, '2025-06-15 23:21:47', '2025-06-16 04:51:47'),
(34, 3, 1525, '2025-06-15 23:21:54', '2025-06-16 04:51:54'),
(35, 2, 1480, '2025-06-15 23:28:01', '2025-06-16 04:58:01'),
(36, 2, 1490, '2025-06-15 23:35:45', '2025-06-16 05:05:45'),
(37, 2, 1470.222, '2025-06-15 23:42:15', '2025-06-16 05:12:15'),
(38, 2, 1478.21, '2025-06-15 23:54:24', '2025-06-16 05:24:24'),
(39, 2, 1480, '2025-06-16 00:46:22', '2025-06-16 06:16:22'),
(40, 2, 1234.2, '2025-06-16 11:55:07', '2025-06-16 17:25:07'),
(41, 3, 1233.22, '2025-06-16 11:55:07', '2025-06-16 17:25:07'),
(42, 2, 1, '2025-06-16 12:00:55', '2025-06-16 17:30:55'),
(43, 3, 1, '2025-06-16 12:05:43', '2025-06-16 17:35:43'),
(44, 2, 1430, '2025-06-16 12:22:42', '2025-06-16 17:52:42'),
(45, 3, 1551, '2025-06-16 12:22:42', '2025-06-16 17:52:42'),
(46, 3, 1555, '2025-06-16 12:22:53', '2025-06-16 17:52:53'),
(47, 2, 1433, '2025-06-16 12:22:59', '2025-06-16 17:52:59'),
(48, 2, 1433.26, '2025-06-16 12:24:26', '2025-06-16 17:54:26'),
(49, 3, 1555.33, '2025-06-16 12:24:26', '2025-06-16 17:54:26'),
(50, 2, 1433.22, '2025-06-16 12:39:48', '2025-06-16 18:09:48'),
(51, 2, 1433.66, '2025-06-16 12:39:53', '2025-06-16 18:09:53'),
(52, 3, 1555.123, '2025-06-16 12:41:56', '2025-06-16 18:11:56'),
(53, 2, 1433.77, '2025-06-16 12:42:02', '2025-06-16 18:12:02'),
(54, 2, 1440.22, '2025-06-16 23:18:47', '2025-06-17 04:48:47'),
(55, 2, 1440, '2025-06-16 23:18:57', '2025-06-17 04:48:57'),
(56, 4, 0.031, '2025-06-16 23:23:12', '2025-06-17 04:53:12'),
(57, 2, 1434.5, '2025-06-16 23:27:10', '2025-06-17 04:57:10'),
(58, 2, 143450, '2025-06-16 23:32:16', '2025-06-17 05:02:17'),
(59, 2, 100000003883, '2025-06-16 23:32:49', '2025-06-17 05:02:49'),
(60, 2, 143450, '2025-06-16 23:32:55', '2025-06-17 05:02:55'),
(61, 3, 15553939, '2025-06-16 23:33:01', '2025-06-17 05:03:01'),
(62, 3, 155000, '2025-06-17 00:35:26', '2025-06-17 06:05:26'),
(63, 1, 1430, '2025-06-17 01:22:06', '2025-06-17 06:52:06');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `expenses_id` varchar(512) NOT NULL,
  `note` varchar(512) NOT NULL,
  `amount` varchar(512) NOT NULL,
  `expenses_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `business_id`, `vendor_id`, `expenses_id`, `note`, `amount`, `expenses_date`, `created_at`, `updated_at`) VALUES
(1, 5, 1, '1', 'کاک وەلید وو', '50', '2025-04-01', '2025-03-31 21:20:46', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `expenses_type`
--

CREATE TABLE `expenses_type` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `title` varchar(512) NOT NULL,
  `description` varchar(512) NOT NULL,
  `expenses_type_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `expenses_type`
--

INSERT INTO `expenses_type` (`id`, `vendor_id`, `title`, `description`, `expenses_type_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'کارەبای مۆلیدە', 'وەسڵی مۆلیدە', '2025-04-01', '2025-03-31 21:18:51', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator'),
(2, 'vendors', 'Vendors'),
(3, 'delivery_boys', 'Delivery boys'),
(4, 'customers', 'Customers'),
(5, 'suppliers', 'Suppliers'),
(6, 'team_members', 'Team Members');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `is_rtl` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `language`, `code`, `is_rtl`, `created_at`) VALUES
(1, 'english', 'en', 0, '2022-04-26 11:23:14'),
(5, 'کوردی', 'ku', 0, '2025-03-31 21:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) DEFAULT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2023_05_29_113905', 'App\\Database\\Migrations\\Address_Field', 'default', 'App', 1695452760, 1),
(2, '2023-09-19-101401', 'App\\Database\\Migrations\\User_permissions', 'default', 'App', 1695452811, 2),
(3, '2024-08-22-092910', 'App\\Database\\Migrations\\Add_Order_No_To_OrdersTable', 'default', 'App', 1724838698, 3),
(4, '2024-08-22-124010', 'App\\Database\\Migrations\\Add_Order_No_To_OrdersTable', 'default', 'App', 1724838796, 4),
(5, '2024-09-06-063500', 'App\\Database\\Migrations\\CreateTeamMembersTable', 'default', 'App', 1727937481, 5),
(6, '2024-09-30-045119', 'App\\Database\\Migrations\\ModifyProductsTableTax', 'default', 'App', 1727937481, 5),
(7, '2024-10-01-094305', 'App\\Database\\Migrations\\AddTaxDetailsToOrderTable', 'default', 'App', 1727937481, 5),
(8, '2024-10-02-035805', 'App\\Database\\Migrations\\ModifyTaxIdInPurchaseTable', 'default', 'App', 1727937481, 5),
(9, '2024-10-04-044813', 'App\\Database\\Migrations\\ChangeTaxIdToTaxIdsToServiceTable', 'default', 'App', 1728017386, 6),
(10, '2024-10-07-051345', 'App\\Database\\Migrations\\AddTaxDetailsToOrderServiceTable', 'default', 'App', 1728278101, 7),
(11, '2024-10-11-060741', 'App\\Database\\Migrations\\AddWarehouseTable', 'default', 'App', 1729594787, 8),
(12, '2024-10-11-061727', 'App\\Database\\Migrations\\AddWarehouseProductStockTable', 'default', 'App', 1729594787, 8),
(13, '2024-10-22-040400', 'App\\Database\\Migrations\\AddWarehouseIdToOrderTable', 'default', 'App', 1729594787, 8),
(14, '2024-10-22-040414', 'App\\Database\\Migrations\\AddWarehouseIdToPurchasesTable', 'default', 'App', 1729594787, 8),
(15, '2024-11-22-115056', 'App\\Database\\Migrations\\AddUserIdToCustomersTransactions', 'default', 'App', 1738991529, 9),
(16, '2025-01-22-033558', 'App\\Database\\Migrations\\ModifyPurchasesTable', 'default', 'App', 1738991529, 9),
(17, '2025-01-22-064048', 'App\\Database\\Migrations\\ModifyOrdersTable', 'default', 'App', 1738991529, 9),
(18, '2025-01-30-060358', 'App\\Database\\Migrations\\AddBarcodeToProductVariantsTable', 'default', 'App', 1738991529, 9),
(19, '2025-01-30-065314', 'App\\Database\\Migrations\\ModifyWharehouseProductStockTable', 'default', 'App', 1738991529, 9),
(20, '2025-02-04-093722', 'App\\Database\\Migrations\\AddIsPosOrderOrdersTable', 'default', 'App', 1738991629, 10),
(21, '2025-02-07-094001', 'App\\Database\\Migrations\\CreateBrandsTable', 'default', 'App', 1738991629, 10),
(22, '2025-02-08-032228', 'App\\Database\\Migrations\\AddBrandToProductsTable', 'default', 'App', 1738991629, 10),
(23, '2025-02-08-034420', 'App\\Database\\Migrations\\RemoveUnsignFromBrandID', 'default', 'App', 1738991630, 10),
(24, '2025-04-14-201318', 'App\\Database\\Migrations\\CreateDraftOrdersTable', 'default', 'App', 1744661623, 11),
(25, '2025-04-14-205615', 'App\\Database\\Migrations\\AddDraftsTable', 'default', 'App', 1744664229, 12),
(26, '2025-04-27-183238', 'App\\Database\\Migrations\\CreateEmployeesTable', 'default', 'App', 1748263950, 13),
(27, '2025-04-27-191532', 'App\\Database\\Migrations\\CreatePositionsTable', 'default', 'App', 1748263950, 13),
(28, '2025-05-09-162127', 'App\\Database\\Migrations\\CreateSpPartialCustomerPayment', 'default', 'App', 1748263950, 13),
(29, '2025-05-18-155323', 'App\\Database\\Migrations\\AlterOrdersAmountPaidToFloat', 'default', 'App', 1748263950, 13),
(30, '2025-05-18-192002', 'App\\Database\\Migrations\\CreateOrdersItemsView', 'default', 'App', 1748263950, 13),
(31, '2025-05-20-145812', 'App\\Database\\Migrations\\CreateOrderDetailsView', 'default', 'App', 1748263950, 13),
(32, '2025-05-21-142011', 'App\\Database\\Migrations\\CreateWarehouseBatchesTable', 'default', 'App', 1748263950, 13),
(33, '2025-05-23-011908', 'App\\Database\\Migrations\\RemovePriceColumnsFromProductsVariants', 'default', 'App', 1748263950, 13),
(34, '2025-05-24-074746', 'App\\Database\\Migrations\\RemoveTypeFromProducts', 'default', 'App', 1748263950, 13),
(35, '2025-05-24-130555', 'App\\Database\\Migrations\\CreateViewProducts', 'default', 'App', 1748263950, 13),
(36, '2025-05-25-030029', 'App\\Database\\Migrations\\UpdateStockDefaultInProductsAndVariants', 'default', 'App', 1748263950, 13),
(37, '2025-05-25-030802', 'App\\Database\\Migrations\\TriggerStockForProductsAndVariants', 'default', 'App', 1748263950, 13),
(38, '2025-05-25-160000', 'App\\Database\\Migrations\\AddSellPriceToWarehouseBatches', 'default', 'App', 1748263950, 13),
(39, '2025-05-19-000001', 'App\\Database\\Migrations\\AddBusinessIdToPositions', 'default', 'App', 1749977740, 14),
(40, '2025-05-30-045314', 'App\\Database\\Migrations\\CreateWarehouseBatchesReturnsTable', 'default', 'App', 1749977740, 14),
(41, '2025-06-11-232350', 'App\\Database\\Migrations\\CreateCurrenciesTable', 'default', 'App', 1749977740, 14),
(42, '2025-06-11-232405', 'App\\Database\\Migrations\\CreateExchangeRatesTable', 'default', 'App', 1749977740, 14),
(43, '2025-06-11-232410', 'App\\Database\\Migrations\\CreatePaymentsTable', 'default', 'App', 1749977740, 14),
(44, '2025-06-12-073043', 'App\\Database\\Migrations\\CreateWarehouseBatchesTriggersStock', 'default', 'App', 1749977740, 14),
(46, '2025-06-15-132820', 'App\\Database\\Migrations\\ImproveExchangeRate', 'default', 'App', 1749998265, 15);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `warehouse_id` int(11) UNSIGNED DEFAULT NULL,
  `order_no` varchar(255) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `total` float NOT NULL,
  `delivery_charges` float NOT NULL,
  `discount` float NOT NULL,
  `final_total` float NOT NULL,
  `returns_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(128) NOT NULL,
  `amount_paid` float NOT NULL DEFAULT 0,
  `order_type` varchar(512) DEFAULT NULL,
  `message` varchar(64) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_pos_order` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `vendor_id`, `customer_id`, `warehouse_id`, `order_no`, `business_id`, `created_by`, `total`, `delivery_charges`, `discount`, `final_total`, `returns_total`, `payment_status`, `amount_paid`, `order_type`, `message`, `payment_method`, `created_at`, `updated_at`, `is_pos_order`) VALUES
(52, 1, 3, NULL, NULL, 5, 1, 100, 0, 0, 86, 14.00, 'unpaid', 100, 'product', 'asdasdasd', 'cash', '2025-04-08 22:37:09', '2025-05-03 23:00:10', 1),
(53, 1, 3, NULL, NULL, 5, 1, 500, 0, 0, 500, 0.00, 'fully_paid', 500, 'product', 'asdad', 'cash', '2025-04-08 22:43:20', '2025-04-08 22:43:20', 1),
(54, 1, 3, NULL, NULL, 5, 1, 24000, 0, 0, 24000, 0.00, 'fully_paid', 24000, 'product', 'asdasdasdasd', 'cash', '2025-04-09 13:46:29', '2025-04-09 13:46:29', 1),
(55, 1, 3, NULL, NULL, 5, 1, 100, 0, 0, 100, 0.00, 'fully_paid', 100, 'product', 'asd', 'cash', '2025-04-09 13:47:10', '2025-04-09 13:47:10', 1),
(56, 1, 3, NULL, NULL, 5, 1, 3000, 0, 0, 3000, 0.00, 'fully_paid', 3000, 'product', 'asdasdasd', 'cash', '2025-04-09 17:29:13', '2025-04-09 17:29:13', 1),
(57, 1, 3, NULL, NULL, 5, 1, 400, 0, 0, 400, 0.00, 'fully_paid', 400, 'product', 'asdasdas', 'cash', '2025-04-10 02:08:29', '2025-04-10 02:08:29', 1),
(58, 1, 3, NULL, NULL, 5, 1, 939, 0, 0, 927, 12.00, 'fully_paid', 927, 'product', 'fafasdassdf', 'cash', '2025-04-10 18:02:11', '2025-04-14 12:38:19', 1),
(62, 1, 106, NULL, NULL, 5, 105, 903, 0, 0, 903, 0.00, 'fully_paid', 903, 'product', '', 'cash', '2025-04-16 22:05:52', '2025-04-16 22:05:52', 0),
(65, 1, 3, NULL, NULL, 5, 1, 402, 0, 0, 402, 0.00, 'partially_paid', 99, 'product', 'asdasd', 'cash', '2025-04-22 23:50:58', '2025-04-22 23:51:23', 1),
(69, 1, 2, NULL, NULL, 5, 1, 27, 3, 5, 25, 0.00, 'partially_paid', 10, 'product', 'mr abas is our friend of all time', 'cash', '2025-05-03 23:02:13', '2025-05-04 00:44:30', 1),
(71, 1, 2, NULL, NULL, 5, 1, 2, 1, 0, 3, 0.00, 'partially_paid', 0, 'product', '', NULL, '2025-05-04 00:45:37', '2025-05-04 00:47:30', 1),
(72, 1, 2, NULL, NULL, 5, 1, 6, 2, 0, 8, 0.00, 'fully_paid', 0, 'product', '', 'cash', '2025-05-04 12:16:46', '2025-05-04 12:16:46', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders_items`
--

CREATE TABLE `orders_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` float NOT NULL,
  `returned_quantity` int(11) DEFAULT 0,
  `price` float NOT NULL,
  `tax_name` varchar(128) NOT NULL,
  `tax_percentage` double NOT NULL,
  `is_tax_included` tinyint(2) NOT NULL,
  `tax_details` varchar(255) DEFAULT NULL,
  `sub_total` float NOT NULL,
  `status` varchar(512) NOT NULL,
  `delivery_boy` int(11) DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_items`
--

INSERT INTO `orders_items` (`id`, `order_id`, `product_id`, `product_variant_id`, `product_name`, `quantity`, `returned_quantity`, `price`, `tax_name`, `tax_percentage`, `is_tax_included`, `tax_details`, `sub_total`, `status`, `delivery_boy`, `warehouse_id`, `created_at`, `updated_at`) VALUES
(7, 39, 17, 2, 'هێلکە', 300, 0, 500, '', 0, 1, '[]', 150000, '5', NULL, NULL, '2025-03-31 21:45:41', '2025-03-31 21:45:41'),
(8, 40, 17, 3, 'هێلکەی سور', 3, 0, 450, '', 0, 1, '[]', 1350, '5', NULL, NULL, '2025-03-31 21:51:02', '2025-03-31 21:51:02'),
(9, 41, 17, 2, 'هێلکەی سپی', 1, 0, 500, '', 0, 1, '[]', 500, '7', NULL, NULL, '2025-03-31 22:00:15', '2025-03-31 22:00:15'),
(10, 42, 17, 2, 'هێلکەی سپی', 1, 0, 500, '', 0, 1, '[]', 500, '8', NULL, NULL, '2025-03-31 22:02:20', '2025-03-31 22:02:20'),
(11, 43, 17, 3, 'هێلکەی سور', 400, 0, 450, '', 0, 1, '[]', 180000, '8', NULL, NULL, '2025-03-31 22:06:53', '2025-03-31 22:06:53'),
(12, 44, 18, 5, 'مریشک سپی', 1, 0, 6, '', 0, 1, '[]', 6, '8', NULL, NULL, '2025-04-02 21:22:51', '2025-04-02 21:22:51'),
(13, 44, 17, 3, 'هێلکەی سور', 1, 0, 450, '', 0, 1, '[]', 450, '8', NULL, NULL, '2025-04-02 21:22:51', '2025-04-02 21:22:51'),
(14, 45, 18, 4, 'مریشک سوور', 1, 1, 5, '', 0, 1, '[]', 5, '8', NULL, NULL, '2025-04-02 21:24:34', '2025-04-09 13:38:32'),
(15, 45, 17, 2, 'هێلکەی سپی', 1, 0, 500, '', 0, 1, '[]', 500, '8', NULL, NULL, '2025-04-02 21:24:34', '2025-04-02 21:24:34'),
(16, 47, 18, 4, 'مریشک سوور', 1, 1, 5, '', 0, 1, '[]', 5, '8', 1, NULL, '2025-04-03 14:12:33', '2025-04-09 13:37:45'),
(17, 47, 17, 2, 'هێلکەی سپی', 1, 0, 500, '', 0, 1, '[]', 500, '8', 1, NULL, '2025-04-03 14:12:34', '2025-04-03 14:14:36'),
(18, 48, 17, 2, 'هێلکەی سپی', 1, 0, 500, '', 0, 1, '[]', 500, '8', NULL, NULL, '2025-04-04 23:55:21', '2025-04-04 23:55:21'),
(19, 48, 18, 4, 'مریشک سوور', 1, 0, 5, '', 0, 1, '[]', 5, '8', NULL, NULL, '2025-04-04 23:55:21', '2025-04-04 23:55:21'),
(20, 49, 18, 4, 'مریشک سوور', 4, 0, 5, '', 0, 1, '[]', 20, '5', NULL, NULL, '2025-04-06 01:34:06', '2025-04-06 01:34:06'),
(21, 50, 18, 4, 'مریشک سوور', 290, 290, 5, '', 0, 1, '[]', 1450, '5', NULL, NULL, '2025-04-06 01:51:49', '2025-04-08 22:25:13'),
(22, 51, 17, 2, 'هێلکەی سپی', 50, 10, 500, '', 0, 1, '[]', 25000, '5', NULL, NULL, '2025-04-08 22:26:05', '2025-04-08 22:42:22'),
(23, 52, 19, 6, 'سلاو', 50, 7, 2, '', 0, 1, '[]', 100, '5', NULL, NULL, '2025-04-08 22:37:09', '2025-04-09 13:36:28'),
(24, 53, 17, 2, 'هێلکەی سپی', 1, 0, 500, '', 0, 1, '[]', 500, '5', NULL, NULL, '2025-04-08 22:43:20', '2025-04-08 22:43:20'),
(25, 54, 20, 7, 'asdasd', 60, 0, 400, '', 0, 1, '[]', 24000, '5', NULL, NULL, '2025-04-09 13:46:29', '2025-04-09 13:46:29'),
(26, 55, 18, 4, 'مریشک سوور', 20, 0, 5, '', 0, 1, '[]', 100, '5', NULL, NULL, '2025-04-09 13:47:10', '2025-04-09 13:47:10'),
(27, 56, 18, 4, 'مریشک سوور', 600, 0, 5, '', 0, 1, '[]', 3000, '5', NULL, NULL, '2025-04-09 17:29:13', '2025-04-09 17:29:13'),
(28, 57, 20, 7, 'asdasd', 1, 0, 400, '', 0, 1, '[]', 400, '5', NULL, NULL, '2025-04-10 02:08:30', '2025-04-10 02:08:30'),
(29, 58, 18, 5, 'مریشک سپی', 5, 2, 6, '', 0, 1, '[]', 30, '5', NULL, NULL, '2025-04-10 18:02:11', '2025-04-10 18:04:24'),
(30, 58, 20, 7, 'asdasd', 1, 0, 400, '', 0, 1, '[]', 400, '5', NULL, NULL, '2025-04-10 18:02:12', '2025-04-10 18:02:12'),
(31, 58, 19, 6, 'سلاو', 1, 0, 2, '', 0, 1, '[]', 2, '5', NULL, NULL, '2025-04-10 18:02:12', '2025-04-10 18:02:12'),
(32, 58, 17, 2, 'هێلکەی سپی', 1, 0, 500, '', 0, 1, '[]', 500, '5', NULL, NULL, '2025-04-10 18:02:12', '2025-04-10 18:02:12'),
(33, 58, 21, 9, 'test2', 1, 0, 2, '', 0, 1, '[]', 2, '5', NULL, NULL, '2025-04-10 18:02:12', '2025-04-10 18:02:12'),
(34, 58, 18, 4, 'مریشک سوور', 1, 0, 5, '', 0, 1, '[]', 5, '5', NULL, NULL, '2025-04-10 18:02:12', '2025-04-10 18:02:12'),
(35, 59, 17, 2, 'هێلکەی سپی', 40, 0, 500, '', 0, 1, '[]', 20000, '5', NULL, NULL, '2025-04-10 21:38:01', '2025-04-10 21:38:01'),
(36, 60, 17, 2, 'هێلکەی سپی', 1, 1, 500, '', 0, 1, '[]', 500, '5', NULL, NULL, '2025-04-13 19:18:19', '2025-04-16 20:56:40'),
(37, 60, 19, 6, 'سلاو', 1, 0, 2, '', 0, 1, '[]', 2, '5', NULL, NULL, '2025-04-13 19:18:19', '2025-04-13 19:18:19'),
(38, 61, 19, 6, 'سلاو', 1, 0, 2, '', 0, 1, '[]', 2, '5', NULL, NULL, '2025-04-16 20:58:19', '2025-04-16 20:58:19'),
(39, 61, 20, 7, 'asdasd', 1, 0, 400, '', 0, 1, '[]', 400, '5', NULL, NULL, '2025-04-16 20:58:19', '2025-04-16 20:58:19'),
(40, 61, 17, 2, 'هێلکەی سپی', 1, 0, 500, '', 0, 1, '[]', 500, '5', NULL, NULL, '2025-04-16 20:58:19', '2025-04-16 20:58:19'),
(41, 62, 19, 6, 'سلاو', 1, 0, 2, '', 0, 1, '[]', 2, '5', NULL, NULL, '2025-04-16 22:05:52', '2025-04-16 22:05:52'),
(42, 62, 20, 7, 'asdasd', 1, 0, 400, '', 0, 1, '[]', 400, '5', NULL, NULL, '2025-04-16 22:05:52', '2025-04-16 22:05:52'),
(43, 62, 21, 8, 'test1', 1, 0, 1, '', 0, 1, '[]', 1, '5', NULL, NULL, '2025-04-16 22:05:52', '2025-04-16 22:05:52'),
(44, 62, 17, 2, 'هێلکەی سپی', 1, 0, 500, '', 0, 1, '[]', 500, '5', NULL, NULL, '2025-04-16 22:05:52', '2025-04-16 22:05:52'),
(45, 63, 21, 8, 'test1', 1, 1, 1, '', 0, 1, '[]', 1, '5', NULL, NULL, '2025-04-21 17:46:04', '2025-04-23 20:33:59'),
(46, 63, 20, 7, 'asdasd', 1, 1, 400, '', 0, 1, '[]', 400, '5', NULL, NULL, '2025-04-21 17:46:04', '2025-04-23 20:33:59'),
(47, 64, 20, 7, 'asdasd', 1, 0, 400, '', 0, 1, '[]', 400, '5', NULL, NULL, '2025-04-21 17:46:17', '2025-04-21 17:46:17'),
(48, 65, 20, 7, 'asdasd', 1, 0, 400, '', 0, 1, '[]', 400, '5', NULL, NULL, '2025-04-22 23:50:58', '2025-04-22 23:50:58'),
(49, 65, 19, 6, 'سلاو', 1, 0, 2, '', 0, 1, '[]', 2, '5', NULL, NULL, '2025-04-22 23:50:58', '2025-04-22 23:50:58'),
(50, 66, 21, 9, 'test2', 1, 0, 2, '', 0, 1, '[]', 2, '5', NULL, NULL, '2025-04-24 12:11:46', '2025-04-24 12:11:46'),
(51, 67, 20, 7, 'asdasd', 1, 0, 400, '', 0, 1, '[]', 400, '5', NULL, NULL, '2025-04-24 12:28:34', '2025-04-24 12:28:34'),
(52, 67, 19, 6, 'سلاو', 1, 0, 2, '', 0, 1, '[]', 2, '5', NULL, NULL, '2025-04-24 12:28:34', '2025-04-24 12:28:34'),
(53, 68, 20, 7, 'asdasd', 1, 0, 400, '', 0, 1, '[]', 400, '5', NULL, NULL, '2025-04-27 19:57:24', '2025-04-27 19:57:24'),
(54, 69, 21, 9, 'test2', 3, 0, 2, '', 0, 1, '[]', 6, '7', NULL, NULL, '2025-05-03 23:02:13', '2025-05-03 23:02:13'),
(55, 69, 18, 5, 'مریشک سپی', 3, 0, 6, '', 0, 1, '[]', 18, '7', NULL, NULL, '2025-05-03 23:02:13', '2025-05-03 23:02:13'),
(56, 69, 21, 8, 'test1', 3, 0, 1, '', 0, 1, '[]', 3, '7', NULL, NULL, '2025-05-03 23:02:13', '2025-05-03 23:02:13'),
(57, 70, 21, 8, 'test1', 5, 0, 1, '', 0, 1, '[]', 5, '5', NULL, NULL, '2025-05-03 23:36:30', '2025-05-03 23:36:30'),
(58, 71, 21, 8, 'test1', 2, 0, 1, '', 0, 1, '[]', 2, '7', NULL, NULL, '2025-05-04 00:45:37', '2025-05-04 00:45:37'),
(59, 72, 21, 9, 'test2', 3, 0, 2, '', 0, 1, '[]', 6, '5', NULL, NULL, '2025-05-04 12:16:46', '2025-05-04 12:16:46');

-- --------------------------------------------------------

--
-- Table structure for table `orders_services`
--

CREATE TABLE `orders_services` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_name` text NOT NULL,
  `price` double NOT NULL,
  `quantity` double NOT NULL,
  `unit_name` varchar(128) NOT NULL,
  `unit_id` tinyint(11) NOT NULL,
  `sub_total` double NOT NULL,
  `tax_name` varchar(128) NOT NULL,
  `tax_percentage` double NOT NULL,
  `is_tax_included` tinyint(2) NOT NULL,
  `tax_details` varchar(255) DEFAULT NULL,
  `is_recursive` tinyint(2) NOT NULL,
  `recurring_days` int(128) NOT NULL,
  `starts_on` datetime NOT NULL,
  `ends_on` datetime NOT NULL,
  `delivery_boy` int(11) DEFAULT NULL,
  `status` varchar(512) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_services`
--

INSERT INTO `orders_services` (`id`, `order_id`, `service_id`, `service_name`, `price`, `quantity`, `unit_name`, `unit_id`, `sub_total`, `tax_name`, `tax_percentage`, `is_tax_included`, `tax_details`, `is_recursive`, `recurring_days`, `starts_on`, `ends_on`, `delivery_boy`, `status`, `created_at`, `updated_at`) VALUES
(4, 46, 4, 'ئیشتیراکی مانگانە', 5, 1, 'gram', 2, 5, '', 0, 1, '[]', 1, -30, '2025-05-03 00:00:00', '2025-06-02 00:00:00', NULL, '8', '2025-04-02 22:06:05', '2025-04-02 22:06:05');

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_items_view`
-- (See below for the actual view)
--
CREATE TABLE `order_items_view` (
`orders_items_id` int(11)
,`category` varchar(64)
,`brand` varchar(1024)
,`image` varchar(128)
,`warehouse_id` int(11) unsigned
,`warehouse_name` varchar(255)
,`product_name` varchar(255)
,`quantity` float
,`price` float
,`returned_quantity` int(11)
,`order_id` int(11)
,`product_id` int(11)
,`product_variant_id` int(11)
,`status` varchar(256)
,`status_id` varchar(512)
);

-- --------------------------------------------------------

--
-- Table structure for table `order_returns`
--

CREATE TABLE `order_returns` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `return_date` datetime NOT NULL DEFAULT current_timestamp(),
  `return_reason` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `processed_by` int(11) DEFAULT NULL,
  `processed_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `order_returns`
--

INSERT INTO `order_returns` (`id`, `order_id`, `item_id`, `quantity`, `price`, `total`, `return_date`, `return_reason`, `status`, `processed_by`, `processed_date`) VALUES
(1, 50, 21, 2, 5.00, 10.00, '2025-04-07 23:58:53', 'fgh', '1', NULL, '2025-04-07 23:58:53'),
(2, 50, 21, 3, 5.00, 15.00, '2025-04-07 23:59:00', 'dfg', '1', NULL, '2025-04-07 23:59:00'),
(3, 50, 21, 2, 5.00, 10.00, '2025-04-08 00:02:37', 'asd', '1', NULL, '2025-04-08 00:02:37'),
(4, 50, 21, 3, 5.00, 15.00, '2025-04-08 00:03:39', 'asd', '1', NULL, '2025-04-08 00:03:39'),
(5, 50, 21, 5, 5.00, 25.00, '2025-04-08 01:12:16', 'sdfsdfsdfsdfsd', 'processed', 1, NULL),
(6, 50, 21, 5, 5.00, 25.00, '2025-04-08 01:12:42', ',m ,mjlkl;jjpjpi', 'processed', 1, NULL),
(7, 50, 21, 6, 5.00, 30.00, '2025-04-08 01:13:29', 'jkhgiulgigu867', 'processed', 1, NULL),
(8, 50, 21, 16, 5.00, 80.00, '2025-04-08 01:15:06', 'asdasdsdasdasd', 'processed', 1, NULL),
(9, 50, 21, 1, 5.00, 5.00, '2025-04-08 01:16:44', 'sdfsdfsddfsdfsd', 'processed', 1, NULL),
(10, 48, 18, 1, 500.00, 500.00, '2025-04-09 03:07:38', 'dasasdasddasd', 'processed', 1, NULL),
(11, 49, 20, 2, 5.00, 10.00, '2025-04-09 03:08:26', 'sdfdsfdsfdsf', 'processed', 1, NULL),
(12, 50, 21, 6, 5.00, 30.00, '2025-04-09 03:16:54', 'asdsadsadasdsa', 'processed', 1, NULL),
(13, 50, 21, 5, 5.00, 25.00, '2025-04-09 03:19:32', 'asdsadasdasdasds', 'processed', 1, NULL),
(14, 50, 21, 6, 5.00, 30.00, '2025-04-09 03:53:26', 'asdsadasdasdasdas', 'processed', 1, NULL),
(15, 50, 21, 5, 5.00, 25.00, '2025-04-09 03:54:09', 'asdsadasdasdsad', 'processed', 1, NULL),
(16, 50, 21, 225, 5.00, 1125.00, '2025-04-09 03:55:13', 'asdasdasdasdasdasdasd', 'processed', 1, NULL),
(17, 51, 22, 4, 500.00, 2000.00, '2025-04-09 03:56:31', 'asdasdasdasdads', 'processed', 1, NULL),
(18, 51, 22, 4, 500.00, 2000.00, '2025-04-09 04:00:14', 'zxczxczxczxczxc', 'processed', 1, NULL),
(19, 52, 23, 5, 2.00, 10.00, '2025-04-09 04:07:39', 'erwrwerwerwer', 'processed', 1, NULL),
(20, 51, 22, 1, 500.00, 500.00, '2025-04-09 04:11:12', 'sdfsdfsdfsdfsfd', 'processed', 1, NULL),
(21, 51, 22, 1, 500.00, 500.00, '2025-04-09 04:12:22', 'asdasdsadasdsadasd', 'processed', 1, NULL),
(22, 52, 23, 1, 2.00, 2.00, '2025-04-09 19:06:05', 'asdasdsadasdasda', 'processed', 1, NULL),
(23, 52, 23, 1, 2.00, 2.00, '2025-04-09 19:06:28', 'dsfsfdsfsdfsd', 'processed', 1, NULL),
(24, 47, 16, 1, 5.00, 5.00, '2025-04-09 19:07:45', 'asdasdasdsadasd', 'processed', 1, NULL),
(25, 45, 14, 1, 5.00, 5.00, '2025-04-09 19:08:32', 'asdasdsadasds', 'processed', 1, NULL),
(26, 58, 29, 2, 6.00, 12.00, '2025-04-10 23:34:24', 'dfsdfdsffsdfsdfs', 'processed', 1, NULL),
(27, 60, 36, 1, 500.00, 500.00, '2025-04-17 02:26:40', 'fvfsdxcvxcvxc', 'processed', 1, NULL),
(28, 63, 45, 1, 1.00, 1.00, '2025-04-24 02:03:59', 'etrgsdfgszfsdf', 'processed', 1, NULL),
(29, 63, 46, 1, 400.00, 400.00, '2025-04-24 02:03:59', 'etrgsdfgszfsdf', 'processed', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `no_of_businesses` int(11) NOT NULL,
  `no_of_delivery_boys` int(11) NOT NULL,
  `no_of_products` int(11) NOT NULL,
  `no_of_customers` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages_tenures`
--

CREATE TABLE `packages_tenures` (
  `id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `tenure` varchar(64) NOT NULL,
  `months` varchar(32) NOT NULL,
  `price` double DEFAULT NULL,
  `discounted_price` double DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `converted_iqd` double NOT NULL,
  `rate_at_payment` double NOT NULL,
  `payment_type` varchar(64) NOT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` bigint(20) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `tax_ids` varchar(255) DEFAULT NULL,
  `name` varchar(1024) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(128) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `business_id`, `vendor_id`, `tax_ids`, `name`, `description`, `image`, `stock`, `status`, `created_at`, `updated_at`) VALUES
(17, 1, NULL, 5, 1, '[]', 'هێلکە', 'اسداسداسد', 'public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg', 487, 1, '2025-03-31 21:38:42', '2025-04-16 22:05:52'),
(18, 1, 1, 5, 1, '[]', 'مریشک', 'mrishk', 'public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg', 20, 1, '2025-04-02 21:21:54', '2025-04-10 02:57:25'),
(19, 1, NULL, 5, 1, '[]', 'سلاو', 'اسداسداسداسد', 'public/uploads/products/a07f39855f8968114740148b235eb15b.jpg', 846, 1, '2025-04-08 22:35:25', '2025-04-24 12:28:34'),
(20, 1, NULL, 5, 1, '[]', 'شیر', 'اسداسداسداس', 'public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg', 834, 1, '2025-04-09 13:45:50', '2025-04-27 19:57:24'),
(21, 1, NULL, 5, 1, '[]', 'test', 'adsadas', 'public/uploads/products/457624148_899136962237390_8858762611595115315_n.jpg', 0, 1, '2025-04-10 03:00:00', '2025-04-10 03:00:28'),
(22, 17, 2, 5, 1, NULL, 'Nokia 33 Series', 'a good one back then', 'public/uploads/products/n3310.jpg', 0, 1, '2025-05-26 13:10:44', '2025-05-26 13:10:44'),
(23, 17, 2, 5, 1, NULL, 'Nokia 33 Series', 'a good one back then', 'public/uploads/products/n3310_1.jpg', 0, 1, '2025-05-26 13:10:44', '2025-05-26 13:10:44');

-- --------------------------------------------------------

--
-- Table structure for table `products_variants`
--

CREATE TABLE `products_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_name` varchar(512) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `qty_alert` varchar(256) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `barcode` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products_variants`
--

INSERT INTO `products_variants` (`id`, `product_id`, `variant_name`, `stock`, `qty_alert`, `unit_id`, `status`, `created_at`, `updated_at`, `barcode`) VALUES
(2, 17, 'هێلکەی سپی', 10, '10', 0, 1, '2025-03-31 21:38:42', '2025-04-08 22:42:44', '123123'),
(3, 17, 'هێلکەی سور', 5, '10', 0, 1, '2025-03-31 21:38:42', '2025-04-10 02:56:33', '1231234'),
(4, 18, 'مریشک سوور', 12, '50', 5, 1, '2025-04-02 21:21:54', '2025-06-16 14:40:58', '6435632'),
(5, 18, 'مریشک سپی', 33, '50', 5, 1, '2025-04-02 21:21:54', '2025-05-03 23:02:13', '535625'),
(6, 19, 'سلاو', 5, '0', 0, 1, '2025-04-08 22:35:25', '2025-04-08 22:37:39', '3123123'),
(7, 20, 'asdasd', 0, '0', 0, 1, '2025-04-09 13:45:50', '2025-04-09 13:45:50', '1232213123'),
(8, 21, 'test1', 29, '10', 1, 1, '2025-04-10 03:00:00', '2025-05-04 00:45:37', '12354673'),
(9, 21, 'test2', 52, '10', 13, 1, '2025-04-10 03:00:00', '2025-05-04 12:16:46', '6756353'),
(10, 22, 'nokia 3310', 0, '2', 35, 1, '2025-05-26 13:10:44', '2025-05-26 13:10:44', '234234'),
(11, 22, 'nokia 3314', 0, '2', 35, 1, '2025-05-26 13:10:44', '2025-05-26 13:10:44', '3454355'),
(12, 23, 'nokia 3310', 2, '2', 35, 1, '2025-05-26 13:10:44', '2025-06-16 23:46:54', '234234'),
(13, 23, 'nokia 3314', 0, '2', 35, 1, '2025-05-26 13:10:44', '2025-05-26 13:10:44', '3454355');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `warehouse_id` int(11) UNSIGNED DEFAULT NULL,
  `order_no` varchar(64) NOT NULL,
  `purchase_date` date NOT NULL,
  `tax_ids` varchar(255) DEFAULT NULL,
  `status` int(12) NOT NULL,
  `delivery_charges` float NOT NULL,
  `order_type` varchar(256) NOT NULL,
  `total` float NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` varchar(128) NOT NULL,
  `amount_paid` double NOT NULL,
  `message` varchar(1024) NOT NULL,
  `discount` float NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `business_id`, `vendor_id`, `supplier_id`, `warehouse_id`, `order_no`, `purchase_date`, `tax_ids`, `status`, `delivery_charges`, `order_type`, `total`, `payment_method`, `payment_status`, `amount_paid`, `message`, `discount`, `created_at`, `updated_at`) VALUES
(3, 5, 1, 108, 2, '1', '2025-04-01', '[]', 5, 0, 'order', 40000, 'cash', 'fully_paid', 40000, 'sadds', 0, '2025-04-01 03:18:30', '2025-03-31 21:48:30'),
(4, 5, 1, 108, 2, '', '2025-04-01', '[]', 5, 0, 'order', 30000, 'cash', 'fully_paid', 30000, '', 0, '2025-04-01 03:39:46', '2025-03-31 22:09:46'),
(5, 5, 1, 108, 2, '56', '2025-04-03', '[]', 8, 0, 'order', 200, 'cash', 'partially_paid', 100, 'fsdfs', 0, '2025-04-03 02:57:47', '2025-04-02 21:27:47'),
(6, 5, 1, 108, 2, '', '2025-04-06', '[]', 5, 0, 'order', 600, 'cash', 'fully_paid', 600, '', 0, '2025-04-06 07:22:23', '2025-04-06 01:52:23'),
(7, 5, 1, 108, 2, '', '2025-04-09', '[]', 5, 0, 'order', 500, 'cash', 'fully_paid', 500, 'asdsadasdasd', 0, '2025-04-09 04:06:05', '2025-04-08 22:36:05'),
(8, 5, 1, 108, 2, '', '2025-04-09', '[]', 5, 0, 'order', 150000, 'cash', 'fully_paid', 150000, 'asdasdad', 0, '2025-04-09 19:18:58', '2025-04-09 13:48:58'),
(9, 5, 1, 108, 2, '', '2025-04-10', '[]', 5, 0, 'order', 15000, 'cash', 'fully_paid', 15000, 'asdasd', 0, '2025-04-10 08:24:59', '2025-04-10 02:54:59'),
(10, 5, 1, 108, 2, '', '2025-05-26', NULL, 7, 0, 'order', 63, NULL, 'fully_paid', 63, 'nia', 0, '2025-05-26 19:00:40', '2025-05-26 13:30:40'),
(11, 5, 1, 108, 3, '', '2025-05-26', NULL, 7, 5, 'order', 24, NULL, 'fully_paid', 24, 'hast', 44, '2025-05-26 19:01:30', '2025-05-26 13:31:30'),
(12, 5, 1, 108, 3, '', '2025-05-26', NULL, 7, 5, 'order', 24, NULL, 'partially_paid', 20, 'hast', 44, '2025-05-26 19:02:22', '2025-05-26 13:32:22'),
(13, 5, 1, 108, 3, '', '2025-05-26', NULL, 7, 5, 'order', -39, NULL, 'partially_paid', 20, 'hast', 44, '2025-05-26 19:08:29', '2025-05-26 13:38:29'),
(14, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 0, 'order', -2, NULL, 'fully_paid', -2, 'ddd', 2, '2025-05-26 19:09:04', '2025-05-26 13:39:04'),
(15, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 0, 'order', -2, NULL, 'fully_paid', -2, 'ddd', 2, '2025-05-26 19:12:01', '2025-05-26 13:42:01'),
(16, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 0, 'order', -20, NULL, 'fully_paid', -20, 'asdasd', 22, '2025-05-26 19:21:03', '2025-05-26 13:51:03'),
(17, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 1, 'order', 1, NULL, 'fully_paid', 1, 'ddd', 0, '2025-05-26 19:25:57', '2025-05-26 13:55:57'),
(18, 5, 1, 108, 2, '', '2025-05-26', NULL, 6, 0, 'order', -1, NULL, 'fully_paid', -1, 'fff', 1, '2025-05-26 19:29:48', '2025-05-26 13:59:48'),
(19, 5, 1, 108, 2, '', '2025-05-26', NULL, 6, 0, 'order', 0, NULL, 'fully_paid', 0, '', 0, '2025-05-26 20:00:05', '2025-05-26 14:30:05'),
(20, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 0, 'order', 0, NULL, 'fully_paid', 0, 'asas', 0, '2025-05-26 20:01:24', '2025-05-26 14:31:24'),
(21, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 0, 'order', 0, NULL, 'fully_paid', 0, 'asas', 0, '2025-05-26 20:02:49', '2025-05-26 14:32:49'),
(22, 5, 1, 108, 3, '', '2025-05-26', NULL, 7, 0, 'order', 6, NULL, 'fully_paid', 6, 'asas', 0, '2025-05-26 20:04:03', '2025-05-26 14:34:03'),
(23, 5, 1, 108, 2, '', '2025-05-26', NULL, 7, 0, 'order', 0, NULL, 'fully_paid', 0, 'asas', 0, '2025-05-26 20:09:19', '2025-05-26 14:39:19'),
(24, 5, 1, 108, 2, '', '2025-05-26', NULL, 7, 0, 'order', 0, NULL, 'fully_paid', 0, 'asas', 0, '2025-05-26 20:09:21', '2025-05-26 14:39:21'),
(25, 5, 1, 108, 3, '', '2025-05-26', NULL, 5, 0, 'order', 0, NULL, 'fully_paid', 0, 'nia', 0, '2025-05-26 20:09:38', '2025-05-26 14:39:38'),
(26, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 0, 'order', 0, NULL, 'fully_paid', 0, '12', 0, '2025-05-26 20:12:58', '2025-05-26 14:42:58'),
(27, 5, 1, 108, 2, '', '2025-05-26', NULL, 6, 0, 'order', 0, NULL, 'fully_paid', 0, '', 0, '2025-05-26 20:16:36', '2025-05-26 14:46:36'),
(28, 5, 1, 108, 2, '', '2025-05-26', NULL, 6, 0, 'order', 0, NULL, 'fully_paid', 0, '', 0, '2025-05-26 20:18:20', '2025-05-26 14:48:20'),
(29, 5, 1, 108, 2, '', '2025-05-26', NULL, 6, 0, 'order', 2, NULL, 'fully_paid', 2, '', 0, '2025-05-26 20:22:58', '2025-05-26 14:52:58'),
(30, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 0, 'order', 4, NULL, 'fully_paid', 4, '', 0, '2025-05-26 20:24:36', '2025-05-26 14:54:36'),
(31, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 0, 'order', 4, NULL, 'fully_paid', 4, '', 0, '2025-05-26 20:24:54', '2025-05-26 14:54:54'),
(32, 5, 1, 108, 2, '', '2025-05-26', NULL, 5, 0, 'order', 4, NULL, 'fully_paid', 4, 'nia', 0, '2025-05-26 20:24:57', '2025-05-26 14:54:57'),
(33, 5, 1, 108, 3, '', '2025-05-26', NULL, 5, 0, 'order', 3, NULL, 'fully_paid', 3, '', 0, '2025-05-26 20:26:17', '2025-05-26 14:56:17'),
(34, 5, 1, 108, 2, '', '2025-05-26', NULL, 6, 0, 'order', 50, NULL, 'fully_paid', 50, 'boxoshi', 0, '2025-05-26 20:28:22', '2025-05-26 14:58:22'),
(35, 5, 1, 108, 3, '', '2025-05-26', NULL, 7, 44, 'order', 518, NULL, 'partially_paid', 200, 'boxoshi hichman nia bowtn', 3, '2025-05-26 20:30:42', '2025-06-16 23:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `purchases_items`
--

CREATE TABLE `purchases_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  `discount` float NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `purchases_items`
--

INSERT INTO `purchases_items` (`id`, `purchase_id`, `product_variant_id`, `quantity`, `price`, `discount`, `status`, `created_at`, `updated_at`) VALUES
(3, 3, 2, 100, 400, 0, 5, '2025-04-01 03:18:30', '2025-03-31 21:48:30'),
(4, 4, 3, 300, 100, 0, 5, '2025-04-01 03:39:46', '2025-03-31 22:09:46'),
(5, 5, 5, 50, 4, 0, 8, '2025-04-03 02:57:47', '2025-04-02 21:27:47'),
(6, 6, 4, 200, 3, 0, 5, '2025-04-06 07:22:23', '2025-04-06 01:52:23'),
(7, 7, 6, 500, 0, 0, 5, '2025-04-09 04:06:05', '2025-04-08 22:36:05'),
(8, 8, 7, 500, 300, 0, 5, '2025-04-09 19:18:58', '2025-04-09 13:48:58'),
(9, 9, 7, 50, 300, 0, 5, '2025-04-10 08:24:59', '2025-04-10 02:54:59'),
(10, 0, 13, 1, 4, 1, 6, '2025-05-26 20:28:22', '2025-05-26 14:58:22'),
(11, 0, 5, 10, 5, 3, 6, '2025-05-26 20:28:22', '2025-05-26 14:58:22'),
(12, 35, 13, 22, 2, 2, 7, '2025-05-26 20:30:42', '2025-05-26 15:00:42'),
(13, 35, 9, 44, 4, 5, 7, '2025-05-26 20:30:42', '2025-05-26 15:00:42'),
(14, 35, 4, 12, 22, 0, 5, '2025-06-16 20:10:58', '2025-06-16 14:40:58'),
(15, 35, 12, 2, 33, 0, 6, '2025-06-17 05:16:54', '2025-06-16 23:46:54');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `tax_ids` varchar(255) DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` varchar(2048) NOT NULL,
  `image` varchar(512) NOT NULL,
  `price` double NOT NULL,
  `cost_price` double NOT NULL,
  `is_tax_included` tinyint(2) NOT NULL,
  `is_recursive` tinyint(2) NOT NULL,
  `recurring_days` int(128) NOT NULL,
  `recurring_price` double NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `vendor_id`, `business_id`, `tax_ids`, `unit_id`, `name`, `description`, `image`, `price`, `cost_price`, `is_tax_included`, `is_recursive`, `recurring_days`, `recurring_price`, `status`, `created_at`, `updated_at`) VALUES
(4, 1, 5, '[]', 2, 'ئیشتیراکی مانگانە', 'بۆ ئەو کەسانە حەز ئەکەن مانگانە پارە بەن', 'public/uploads/services/fc68c7853d0b5ecd72df224a7fe57071.jpg', 5, 0, 1, 1, 30, 30, 1, '2025-04-02 22:02:43', '2025-04-02 22:02:43');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `variable` varchar(128) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `variable`, `value`) VALUES
(2, 'general', '{\"title\":\"Legends\",\"support_email\":\"admin@admin.com\",\"currency_symbol\":\"$\",\"currency_locate\":\"left\",\"date_format\":\"m\\/d\\/y H:i A\",\"time_format\":null,\"decimal_points\":\"\",\"mysql_timezone\":\"\",\"select_time_zone\":\"Select Timezone\",\"phone\":\"9089789098\",\"primary_color\":\"#000000\",\"secondary_color\":\"#000000\",\"primary_shadow\":\"#fcf6f5\",\"address\":\"Address\",\"short_description\":\"Short description\",\"support_hours\":\"Support hours\",\"logo\":\".\\/public\\/uploads\\/logo_1.png\",\"half_logo\":\".\\/public\\/uploads\\/favicon-128.png\",\"favicon\":\"\\/public\\/uploads\\/favicon-128_1.png\",\"copyright_details\":\"<span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\">\\u00a9 Copyright 2025 Legends<\\/span><span style=\\\"font-weight: bolder; color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\">.<\\/span><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"> All Rights Reserved<\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span>\"}'),
(36, 'about_us', '{\"about_us\":\"<h4><span style=\\\"color: #000000;\\\">UPBiz - A platform for transforming your conventional business into digital.<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Inventory, Accounting, Invoicing Software. <\\/span><span style=\\\"color: #000000;\\\">UpBiz provides features for strong order management, it helps you manage everything from product management to order management to track every transaction. Upbiz offers a system of multiple roles for users. <\\/span><span style=\\\"color: #000000;\\\">With UpBiz businessmen can easily manage inventory and subscriptions with the help of its prominent features.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Grow your business rapidly as it will reduce your paperwork. <\\/span><span style=\\\"color: #000000;\\\">Straightforward solution for companies that include subscription services as well as products, and stock management, now easily managed at this platform.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Inventory :&nbsp;<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Track Complete inventory with Upbiz such as product type, stock, units, variants, and all other details by listing them. it makes sure that all the items being ordered in the store remain available when required.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Accounting:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">With Upbiz record all the transactions of orders whether its fully paid or partially paid. Further maximizing sales its suitable for growing businesses that need to keep their accounting in check. By recording the expenses, Moreover, it will help save time creating business reports.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Invoicing Software:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\"><strong>UpBiz<\\/strong> is an excellent addition to your business as it helps you automate your billing requirements, including GST return filing, inventory management, invoicing, and billing.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">We are talking about improving the life of a segment that is the largest in our nation, i.e &lsquo;Small Business Sector&rsquo; the heartbeat of our economy. One major aspect holding down the small and medium enterprise (SME) sector is that they hardly have any access to proper technology. Easing this situation will go a long way in nurturing and sustaining SMEs. To let India emerge as one of the brightest economic spots in the coming years, businesses should focus on ways to make cash rather than getting stuck up in counting cash.It basically helps them do business accounting easier with the modern digital way!<\\/span><\\/p>\\r\\n<p>&nbsp;<\\/p>\"}'),
(37, 'refund_policy', '{\"refund_policy\":\"<h4>The following terms are applicable for any products that You purchased with Us.<\\/h4>\\r\\n<p>First of all, we thank you and appreciate your service or product purchase with us on our Website upbiz.taskhub.company. Please read this policy carefully as it will give you important information and guidelines about your rights and obligations as our customer, with respect to any purchase or service we provide to you.<\\/p>\\r\\n<p>At upBiz, we take pride in the services delivered by us and guarantee your satisfaction with our services and support. We constantly improve and strive to deliver the best accounting, financial or secretarial services through the internet. We make every effort to provide the service to you as per the specifications and timelines mentioned against each service or product purchased by you from upBiz, however, if, due for any reason, we are unable to provide to you the service or product you purchased from us, please contact us immediately and we will correct the situation, provide a refund or offer credit that can be used for future upBiz orders.<\\/p>\\r\\n<h4>You shall be entitled to a refund which shall be subject to the following situations:<\\/h4>\\r\\n<p>The Refund shall be only considered in the event there is a clear, visible deficiency with the service or product purchased from upBiz. No refund shall be issued if upBiz processed the registration\\/application as per the government guidelines and registration is pending on part of a government department or officials. If any government fee, duty, challan, or any other sum is paid in the course of processing your registration application. We will refund the full payment less the government fee paid. (Don&rsquo;t worry no government fee shall be deducted until Government challan or any other payment proof is provided to you)<\\/p>\\r\\n<p>In the event a customer has paid for a service and then requests for a refund only because there was a change of mind, the refund shall not be considered as there is no fault, defect, or onus on upBiz. Refund requests shall not be entertained after the work has been shared with you in the event of a change of mind. However, we shall give you the option of using the amount paid for by you, for an alternative service in upBiz amounting to the same value and the said amount could be applied in part or whole towards the said new service; and If the request for a refund has been raised 30 (thirty) days after the purchase of a service or product has been completed and the same has been intimated and indicated via email or through any form of communication stating that the work has been completed, then, such refund request shall be deemed invalid and shall not be considered.<\\/p>\\r\\n<p>If the request for the refund has been approved by upBiz, the same shall be processed and intimated to you via email. This refund process could take a minimum of 15 (fifteen) business days to process and shall be credited to your bank account accordingly. We shall handle the refund process with care and ensure that the money spent by you is returned to you at the earliest.<\\/p>\\r\\n<h4>Fees for Services<\\/h4>\\r\\n<p>When the payment of fee is made to upBiz, the fees paid in advance is retained by upBiz in a client account. upBiz will earn the fees upon working on a client&rsquo;s matter. During an engagement, upBiz earns fee at different rates and different times depending on the completion of various milestones (e.g. providing client portal access, assigning relationship manager, obtaining DIN, Filing of forms, etc.,). Refund cannot be provided for the earned fee because resources and man-hours spent on delivering the service are non-returnable in nature. Further, we can&rsquo;t refund or credit any money paid to government entities, such as filing fees or taxes, or to other third parties with a role in processing your order. Under any circumstance, upBiz shall be liable to refund only up to the fee paid by the client.<\\/p>\\r\\n<h4>Change of Service<\\/h4>\\r\\n<p>If you want to change the service you ordered for a different one, you must request this change of service within 30 days of purchase. The purchase price of the original service, less any earned fee and money paid to government entities, such as filing fees or taxes, or to other third parties with a role in processing your order, will be credited to your upBiz account. You can use the balance credit for any other upBiz service.<\\/p>\\r\\n<h4>Standard Pricing<\\/h4>\\r\\n<p>upBiz has a standard pricing policy wherein no additional service fee is requested under any circumstance. However, the standard pricing policy is not applicable for an increase in the total fee paid by the client to upBiz due to an increase in the government fee or fee incurred by the client for completion of legal documentation or re-filing of forms with the government due to rejection or resubmission. upBiz is not responsible or liable for any other cost incurred by the client related to the completion of the service.<\\/p>\\r\\n<h4>Factors outside our Control<\\/h4>\\r\\n<p>We cannot guarantee the results or outcome of your particular procedure. For instance, the government may reject a trademark application for legal reasons beyond the scope of upBiz service. In some cases, a government backlog or problems with the government platforms (e.g. MCA website, Income Tax website, FSSAI website) can lead to long delays before your process is complete. Similarly, upBiz does not guarantee the results or outcomes of the services rendered by our Associates on a Nearest Expert platform, who are not employed by upBiz. Problems like these are beyond our control and are not covered by this guarantee or eligible for a refund. Hence, the delay in processing your file by the Government cannot be a reason for the refund.<\\/p>\\r\\n<h4>Force Majeure<\\/h4>\\r\\n<p>upBiz shall not be considered in breach of its Satisfaction Guarantee policy or default under any terms of service, and shall not be liable to the Client for any cessation, interruption, or delay in the performance of its obligations by reason of earthquake, flood, fire, storm, lightning, drought, landslide, hurricane, cyclone, typhoon, tornado, natural disaster, act of God or the public enemy, epidemic, famine or plague, action of a court or public authority, change in law, explosion, war, terrorism, armed conflict, labor strike, lockout, boycott or similar event beyond our reasonable control, whether foreseen or unforeseen (each a &ldquo;Force Majeure Event&rdquo;).<\\/p>\\r\\n<h4>Cancellation Fee<\\/h4>\\r\\n<p>Since we&rsquo;re incurring costs and dedicating time, manpower, technology resources, and effort to your service or document preparation, our guarantee only covers satisfaction issues caused by upBiz &ndash; not changes to your situation or your state of mind. In case you require us to hold the processing of service, we will hold the fee paid on your account until you are ready to commence the service.<\\/p>\\r\\n<p>Before processing any refund, we reserve the right to make the best effort to complete the service. In case, you are not satisfied with the service, a cancellation fee of 20% + earned fee + fee paid to the government would be applicable. In case of a change of service, the cancellation fee would not be applicable.<\\/p>\"}'),
(42, 'payment_gateway', '{\"razorpay_payment_mode\":\"Test\",\"razorpay_secret_key\":\"Y0mPvWDwSEVqGo7WhOqDuRrF\",\"razorpay_api_key\":\"rzp_test_yUGY97WyLX7BwZ\",\"razorpay_status\":\"1\",\"stripe_payment_mode\":\"Test\",\"stripe_currency_symbol\":\"INR\",\"stripe_publishable_key\":\"sk_test_51JgnbISHhf5LKO0I0wNtrjf4Hc3pbHjUDJFFQvKgi7ga1I3jgbhJ53bTc3fPMb6qOleEWw66a7XYPo0fevZKGHR900RZc6mkEM\",\"stripe_secret_key\":\"pk_test_51JgnbISHhf5LKO0IQdzXM2b4iZAizrgwaNFfLiQYkq9XdfYQLOw5HQGbOxT4MJAtSjDdOVgYzQ1djB3LEdSSt6AA001sjpQvvN\",\"stripe_webhook_secret_key\":\"Stripe Webhook Secret Key\",\"stripe_status\":\"1\",\"flutterwave_payment_mode\":\"Test\",\"flutterwave_currency_symbol\":\"NGN\",\"flutterwave_public_key\":\"FLWPUBK_TEST-1ffbaed6ee3788cd2bcbb898d3b90c59-X\",\"flutterwave_secret_key\":\"FLWSECK_TEST-c659ffd76304fff90fc4b67ae735b126-X\",\"flutterwave_encryption_key\":\"FLWSECK_TEST25c36edcfcaa\",\"flutterwave_status\":\"1\"}'),
(43, 'email', '{\"email\":\"your_smtp@email.com\",\"password\":\"your password\",\"smtp_host\":\"your host\",\"smtp_port\":\"465\",\"mail_content_type\":\"html\",\"smtp_encryption\":\"ssl\"}'),
(44, 'terms_and_conditions', '{\"terms_and_conditions\":\"<p><span style=\\\"color: #000000;\\\">Welcome to <strong>Upbiz!<\\/strong><\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">These terms and conditions outline the rules and regulations for the use of upbiz\'s Website, located at https:\\/\\/upbiz.taskhub.company\\/.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By accessing this website we assume you accept these terms and conditions. Do not continue to use Upbiz if you do not agree to take all of the terms and conditions stated on this page.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: \\\"Client\\\", \\\"You\\\" and \\\"Your\\\" refers to you, the person log on this website and compliant to the Company&rsquo;s terms and conditions. \\\"The Company\\\", \\\"Ourselves\\\", \\\"We\\\", \\\"Our\\\" and \\\"Us\\\", refers to our Company. \\\"Party\\\", \\\"Parties\\\", or \\\"Us\\\", refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client&rsquo;s needs in respect of provision of the Company&rsquo;s stated services, in accordance with and subject to, prevailing law of India. Any use of the above terminology or other words in the singular, plural, capitalization and\\/or he\\/she or they, are taken as interchangeable and therefore as referring to same.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Cookies<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We employ the use of cookies. By accessing Upbiz , you agreed to use cookies in agreement with the upbiz\'s Privacy Policy.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Most interactive websites use cookies to let us retrieve the user&rsquo;s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate\\/advertising partners may also use cookies.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">License<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Unless otherwise stated, upbiz and\\/or its licensors own the intellectual property rights for all material on Upbiz . All intellectual property rights are reserved. You may access this from Upbiz for your own personal use subjected to restrictions set in these terms and conditions.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">You must not:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Republish material from Upbiz<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Sell, rent or sub-license material from Upbiz<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Reproduce, duplicate or copy material from Upbiz<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Redistribute content from Upbiz<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">This Agreement shall begin on the date hereof.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. upbiz does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of upbiz,its agents and\\/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, upbiz shall not be liable for the Comments or for any liability, damages or expenses caused and\\/or suffered as a result of any use of and\\/or posting of and\\/or appearance of the Comments on this website.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">upbiz reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">You warrant and represent that:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">You hereby grant upbiz a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Hyperlinking to our Content<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">The following organizations may link to our Website without prior written approval:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Government agencies;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Search engines;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">News organizations;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and\\/or services; and (c) fits within the context of the linking party&rsquo;s site.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">We may consider and approve other link requests from the following types of organizations:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">commonly-known consumer and\\/or business information sources;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">dot.com community sites;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">associations or other groups representing charities;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">online directory distributors;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">internet portals;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">accounting, law and consulting firms; and<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">educational institutions and trade associations.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of upbiz; and (d) the link is in the context of general resource information.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party&rsquo;s site.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to upbiz.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Approved organizations may hyperlink to our Website as follows:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By use of our corporate name; or<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By use of the uniform resource locator being linked to; or<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party&rsquo;s site.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">No use of upbiz\'s logo or other artwork will be allowed for linking absent a trademark license agreement.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">iFrames<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Content Liability<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Your Privacy<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Please read Privacy Policy<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Reservation of Rights<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it&rsquo;s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Removal of links from our website<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Disclaimer<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">limit or exclude our or your liability for death or personal injury;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">limit or exclude our or your liability for fraud or fraudulent misrepresentation;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">limit any of our or your liabilities in any way that is not permitted under applicable law; or<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">exclude any of our or your liabilities that may not be excluded under applicable law.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.<\\/span><\\/p>\"}'),
(45, 'privacy_policy', '{\"privacy_policy\":\"<p><span style=\\\"color: #000000;\\\">At Upbiz , accessible from http:\\/\\/upbiz.taskhub.company\\/, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Upbiz and how we use it. <\\/span><span style=\\\"color: #000000;\\\">If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">This Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and\\/or collect in Upbiz . This policy is not applicable to any information collected offline or via channels other than this website.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Consent<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">By using our website, you hereby consent to our Privacy Policy and agree to its terms.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Information we collect<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">The personal information that you are asked to provide, and the reasons why you are asked to provide it, will be made clear to you at the point we ask you to provide your personal information. <\\/span><span style=\\\"color: #000000;\\\">If you contact us directly, we may receive additional information about you such as your name, email address, phone number, the contents of the message and\\/or attachments you may send us, and any other information you may choose to provide. <\\/span><span style=\\\"color: #000000;\\\">When you register for an Account, we may ask for your contact information, including items such as name, company name, address, email address, and telephone number.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">How we use your information<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We use the information we collect in various ways, including to:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">1. Provide, operate, and maintain our website<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">2. Improve, personalize, and expand our website<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">3. Understand and analyze how you use our website<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">4. Develop new products, services, features, and functionality<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">5. Communicate with you, either directly or through one of our partners, including for customer service, to provide you with updates and other information relating to the website, and for marketing and promotional purposes<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">6. Send you emails<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">7. Find and prevent fraud<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Log Files<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Upbiz follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services\' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring\\/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users\' movement on the website, and gathering demographic information.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Advertising Partners Privacy Policies<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">You may consult this list to find the Privacy Policy for each of the advertising partners of Upbiz .<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on Upbiz , which are sent directly to users\' browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and\\/or to personalize the advertising content that you see on websites that you visit.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Note that Upbiz has no access to or control over these cookies that are used by third-party advertisers.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Third Party Privacy Policies<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Upbiz \'s Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options. <\\/span><span style=\\\"color: #000000;\\\">You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers\' respective websites.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Request that a business that collects a consumer\'s personal data disclose the categories and specific pieces of personal data that a business has collected about consumers. <\\/span><span style=\\\"color: #000000;\\\">Request that a business delete any personal data about the consumer that a business has collected. <\\/span><span style=\\\"color: #000000;\\\">Request that a business that sells a consumer\'s personal data, not sell the consumer\'s personal data.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">GDPR Data Protection Rights -&nbsp;<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We would like to make sure you are fully aware of all of your data protection rights. Every user is entitled to the following:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">1. The right to access &ndash; You have the right to request copies of your personal data. We may charge you a small fee for this service.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">2. The right to rectification &ndash; You have the right to request that we correct any information you believe is inaccurate. You also have the right to request that we complete the information you believe is incomplete.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">3. The right to erasure &ndash; You have the right to request that we erase your personal data, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">4. The right to restrict processing &ndash; You have the right to request that we restrict the processing of your personal data, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">5. The right to object to processing &ndash; You have the right to object to our processing of your personal data, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">6. The right to data portability &ndash; You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">7. If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.<\\/span><\\/p>\"}');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `status` varchar(256) NOT NULL,
  `operation` tinyint(4) NOT NULL COMMENT '0-do nothing | 1-credit | 2-debit\r\n',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `vendor_id`, `business_id`, `status`, `operation`, `created_at`, `updated_at`) VALUES
(5, 1, 5, 'تەواو ووە', 0, '2025-03-31 21:44:12', '2025-03-31 21:44:12'),
(6, 1, 5, 'تەواو ووە', 0, '2025-03-31 21:45:03', '2025-03-31 21:45:03'),
(7, 1, 5, 'قەرز', 2, '2025-03-31 21:59:43', '2025-03-31 21:59:43'),
(8, 1, 5, 'زیا بوون', 1, '2025-03-31 22:02:01', '2025-03-31 22:02:01'),
(9, 1, 5, 'گەڕانەوە', 2, '2025-04-05 00:00:48', '2025-04-05 00:00:48');

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL COMMENT 'RENEWABLE SERVICES',
  `customer_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription`
--

INSERT INTO `subscription` (`id`, `service_id`, `customer_id`, `vendor_id`, `created_by`, `business_id`, `created_at`, `updated_at`) VALUES
(2, 4, 2, 1, NULL, 5, '2025-04-02 22:06:05', '2025-04-02 22:06:05');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `balance` float NOT NULL,
  `billing_address` varchar(264) NOT NULL,
  `shipping_address` varchar(264) NOT NULL,
  `credit_period` int(11) NOT NULL,
  `credit_limit` float NOT NULL,
  `tax_name` varchar(64) NOT NULL,
  `tax_num` varchar(64) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `user_id`, `vendor_id`, `balance`, `billing_address`, `shipping_address`, `credit_period`, `credit_limit`, `tax_name`, `tax_num`, `status`, `created_at`, `updated_at`) VALUES
(2, 108, 1, 500, 'asadasd', 'asdasdsad', 400, 800, 'asd', 'asd', 1, '2025-04-01 03:17:45', '2025-03-31 21:47:45');

-- --------------------------------------------------------

--
-- Table structure for table `tax`
--

CREATE TABLE `tax` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `percentage` float NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `vendor_id` int(11) UNSIGNED NOT NULL,
  `business_ids` text DEFAULT NULL,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`id`, `user_id`, `vendor_id`, `business_ids`, `permissions`) VALUES
(2, 104, 1, '[\"5\"]', '{\"\'pos\'\":[\"\'can_create\'\"],\"\'products\'\":[\"\'can_create\'\"]}'),
(3, 110, 1, '[\"5\"]', '{\"\'business\'\":[\"\'can_read\'\"],\"\'categories\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'customers\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'expenses\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'expenses_type\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'generate_barcode\'\":[\"\'can_create\'\"],\"\'manage_stock\'\":[\"\'can_create\'\",\"\'can_read\'\"],\"\'orders\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'orders_return\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'pos\'\":[\"\'can_create\'\"],\"\'products\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'purchase_return\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'update\'\"],\"\'purchases\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'services\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'subscription\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'suppliers\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'transactions\'\":[\"\'can_create\'\",\"\'can_read\'\"],\"\'units\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\"],\"\'brand\'\":[\"\'can_create\'\",\"\'can_read\'\",\"\'can_update\'\",\"\'can_delete\'\"]}');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(12) NOT NULL,
  `user_id` int(12) NOT NULL,
  `amount` double NOT NULL,
  `txn_id` varchar(128) NOT NULL,
  `payment_method` varchar(64) NOT NULL,
  `status` varchar(256) NOT NULL,
  `message` varchar(264) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `symbol` varchar(64) NOT NULL,
  `conversion` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `vendor_id`, `parent_id`, `name`, `symbol`, `conversion`, `created_at`, `updated_at`) VALUES
(1, 0, 0, 'kilogram', 'kg', 0, '2022-01-03 07:43:05', '2022-01-12 07:38:11'),
(2, 0, 1, 'gram', 'g', 1000, '2022-01-03 07:44:03', '2022-01-12 05:14:13'),
(3, 0, 0, 'liter', 'l', 0, '2022-01-03 08:05:44', '2022-01-03 08:09:05'),
(4, 0, 3, 'milliliter', 'ml', 1000, '2022-01-03 08:09:44', '2022-01-03 08:09:44'),
(5, 0, 0, 'pack', 'pk', 0, '2022-01-03 08:20:47', '2022-01-03 08:28:03'),
(6, 0, 0, 'piece', 'pc', 0, '2022-01-03 08:28:26', '2022-01-03 08:28:26'),
(7, 15, 0, 'meter', 'mm', 0, '2022-01-03 08:29:35', '2022-01-13 04:57:47'),
(8, 15, 7, 'millimeter', 'mm', 0, '2022-01-03 08:31:01', '2022-01-13 09:51:32'),
(9, 14, 0, 'foot', 'ft', 0, '2022-01-03 11:35:49', '2022-01-12 07:48:38'),
(10, 0, 9, 'inch', 'in', 12, '2022-01-03 11:36:19', '2022-01-03 11:37:06'),
(11, 0, 0, 'square foot', 'sqft', 0, '2022-01-05 10:55:15', '2022-01-05 10:55:15'),
(12, 0, 11, 'square meter', 'sqm', 0, '2022-01-05 10:58:11', '2022-01-05 10:58:11'),
(13, 0, 0, 'bundles', 'bdl', 0, '2022-01-06 09:45:48', '2022-01-06 09:45:48'),
(32, 0, 0, 'months', 'm', 0, '2022-01-13 10:16:35', '2022-03-28 11:39:15'),
(33, 15, 0, 'hours', 'h', 3600, '2022-01-28 06:36:17', '2022-01-28 06:36:17'),
(34, 15, 0, 'general', 'g', 0, '2022-03-30 05:58:26', '2022-03-30 05:58:26'),
(35, 1, 0, 'دانە', 'دانە', 1, '2025-03-31 21:30:25', '2025-05-26 13:08:59');

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE `updates` (
  `id` int(11) NOT NULL,
  `version` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `updates`
--

INSERT INTO `updates` (`id`, `version`) VALUES
(1, '1.0'),
(2, '1.1'),
(3, '1.2'),
(4, '1.3'),
(5, '1.4'),
(6, '1.5'),
(7, '1.6'),
(8, '1.7'),
(9, '1.8'),
(11, '1.9');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) NOT NULL,
  `mobile` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `mobile`, `password`, `email`, `address`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
(1, '127.0.0.1', 'mhamadaras10@gmail.com', '1234567890', '$2y$12$TSY7xOZEZaadKneUgikdEOd.ceM2X.4rl9tmXV1cpyCmX4H.iKbRi', 'mhamadaras10@gmail.com', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, 1268889823, 1750122589, 1, 'admin', 'admin', 'admin', ''),
(104, '::1', '123456789', '123456789', '$2y$10$v6TVuHv4u.N1ymvJvVT9aema93kf5ICwKF/rvjxAumbejWj3FWWGO', 'mhamadaras17@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1743455833, 1743458187, 1, 'zanko', 'ibrahim', NULL, '123456789'),
(105, '::1', '1234567899', '1234567899', '$2y$10$.RmCowdjcoEzPW0N6vhTWOknrn1Xh7xt65fTc/pjCbHCzWTWFb11S', 'mhamadaras13@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1743456144, 1744841060, 1, 'zanko', NULL, NULL, NULL),
(106, '::1', '2349456184', '2349456184', '$2y$10$TbC7UNnQfN1STS5cFR9Mt.wqsoZBdXwP7qZ.ScS64MCxoUp0I80wK', 'commandermhamad@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1743456213, NULL, 1, 'Abas Abdulla Mohammed', NULL, NULL, NULL),
(107, '::1', '12345678999', '12345678999', '$2y$10$TA04Gh5mInkT.d/ajNrjOOnUoJSgEgfhTh323gfM47xxHxtL5wt.y', 'mhamadaras@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1743457376, NULL, 1, 'mhamad', NULL, NULL, NULL),
(108, '::1', '12223456', '12223456', '$2y$10$lw7XUnHZhecO5c./csNLPeXzxo6KNaLVxbWpGpN6Y8Uq/hvkUHVCq', 'mhamad1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1743457665, NULL, 1, 'mhamadd', NULL, NULL, NULL),
(109, '::1', '11111111', '11111111', '$2y$10$zZnV0k/rli33i7xp051Kv.acnH5O6MbOsQBjCUWhJEuCAYmI.ANdC', 'commandermham1ad@yahoo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1743459036, 1743459045, 1, 'mhamadddd', NULL, NULL, NULL),
(110, '::1', '07511262122', '07511262122', '$2y$10$0lCo4bottd0/2KWjzkXnSOsrMhuRGempYbhUZR9/1Qz6CA5Ys6Deq', 'shahramadalat@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1745961754, 1746030404, 1, 'shahram', 'adalat', NULL, '07511262122'),
(111, '::1', '234231443', '234231443', '$2y$10$UaAkoya5lgoUG5xiOxFD.u77pdwGpEjA/LLRUwBs.sizC42KAG77W', 'ahmed122@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1745962719, NULL, 1, 'ahmed ali', NULL, NULL, NULL),
(112, '::1', '123231423143241', '123231423143241', '$2y$10$8lHTW1ZTmDcH/JqI0mNWd.MG7DnyC8aFwkAY8SvJy1uD2m4VZbVqi', 'ahmed1222@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1745962751, NULL, 1, 'ahmed abass', NULL, NULL, NULL),
(113, '::1', '123123434324', '123123434324', '$2y$10$wGyC/1Uhe/UcvlqEbGZyBu91TFtRhp/o5f4lyyDWCPiMRrA3ktr.y', 'ahmed122222@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1745962811, NULL, 1, 'none', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE `users_groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1),
(81, 104, 6),
(82, 105, 3),
(83, 106, 4),
(84, 107, 4),
(85, 108, 5),
(86, 109, 3),
(87, 110, 6),
(88, 111, 4),
(89, 112, 4),
(90, 113, 4);

-- --------------------------------------------------------

--
-- Table structure for table `users_packages`
--

CREATE TABLE `users_packages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `transaction_id` int(12) DEFAULT NULL,
  `package_name` varchar(64) NOT NULL,
  `no_of_businesses` int(11) NOT NULL,
  `no_of_delivery_boys` int(11) NOT NULL,
  `no_of_products` int(11) NOT NULL,
  `no_of_customers` int(11) NOT NULL,
  `tenure` varchar(64) NOT NULL,
  `price` double NOT NULL,
  `months` varchar(64) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_id` varchar(255) NOT NULL DEFAULT '1',
  `role` int(11) NOT NULL,
  `permissions` text NOT NULL,
  `created_by` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_product_details`
-- (See below for the actual view)
--
CREATE TABLE `view_product_details` (
`product_id` int(11)
,`product_name` varchar(1024)
,`description` text
,`image` varchar(128)
,`stock` int(11)
,`status` tinyint(2)
,`category_id` int(11)
,`business_id` int(11)
,`category_name` varchar(64)
,`brand_name` varchar(255)
,`creator` varchar(50)
,`brand_id` bigint(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(11) UNSIGNED NOT NULL,
  `vendor_id` int(11) UNSIGNED NOT NULL,
  `business_id` text DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `vendor_id`, `business_id`, `name`, `country`, `city`, `zip_code`, `address`) VALUES
(1, 1, '4', 'Default Warehouse', 'Default Country', 'Default City', '0000000', 'Default Warehouse Address'),
(2, 1, '5', 'مەخزەن', 'iraq', 'slemani', '00904', 'sdfsdf'),
(3, 1, '5', 'پێشانگا', 'Iraq', 'sulaymany', '00904', 'mamostayan street\r\nBsbs');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_batches`
--

CREATE TABLE `warehouse_batches` (
  `id` int(11) UNSIGNED NOT NULL,
  `purchase_item_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `warehouse_id` int(10) UNSIGNED NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost_price` float NOT NULL,
  `sell_price` float DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouse_batches`
--

INSERT INTO `warehouse_batches` (`id`, `purchase_item_id`, `business_id`, `product_variant_id`, `warehouse_id`, `batch_number`, `quantity`, `cost_price`, `sell_price`, `expiration_date`, `created_at`) VALUES
(1, 14, 5, 4, 3, 'ORDER-14-1750084858-2025-06-30', 12, 22, 25, '2025-06-30', '2025-06-16 20:10:58'),
(2, 15, 5, 12, 3, 'ORDER-15-1750117614-2025-06-24', 2, 33, 34, '2025-06-24', '2025-06-17 05:16:54');

--
-- Triggers `warehouse_batches`
--
DELIMITER $$
CREATE TRIGGER `update_product_variant_stock_after_batch_delete` AFTER DELETE ON `warehouse_batches` FOR EACH ROW BEGIN
    UPDATE products_variants
    SET stock = (
        SELECT IFNULL(SUM(quantity), 0)
        FROM warehouse_batches
        WHERE product_variant_id = OLD.product_variant_id
    )
    WHERE id = OLD.product_variant_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_product_variant_stock_after_batch_insert` AFTER INSERT ON `warehouse_batches` FOR EACH ROW BEGIN
    UPDATE products_variants
    SET stock = (
        SELECT IFNULL(SUM(quantity), 0)
        FROM warehouse_batches
        WHERE product_variant_id = NEW.product_variant_id
    )
    WHERE id = NEW.product_variant_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_product_variant_stock_after_update` AFTER UPDATE ON `warehouse_batches` FOR EACH ROW BEGIN
    UPDATE products_variants
    SET stock = (
        SELECT IFNULL(SUM(quantity), 0)
        FROM warehouse_batches
        WHERE product_variant_id = NEW.product_variant_id
    )
    WHERE id = NEW.product_variant_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_batches_returns`
--

CREATE TABLE `warehouse_batches_returns` (
  `id` int(11) NOT NULL,
  `purchase_item_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `warehouse_id` int(10) UNSIGNED NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost_price` float NOT NULL,
  `return_price` float DEFAULT NULL,
  `return_reason` text DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_product_stock`
--

CREATE TABLE `warehouse_product_stock` (
  `id` int(11) UNSIGNED NOT NULL,
  `warehouse_id` int(11) UNSIGNED NOT NULL,
  `product_variant_id` int(11) DEFAULT NULL,
  `stock` double DEFAULT 0,
  `qty_alert` double DEFAULT 0,
  `vendor_id` int(11) UNSIGNED NOT NULL,
  `business_id` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouse_product_stock`
--

INSERT INTO `warehouse_product_stock` (`id`, `warehouse_id`, `product_variant_id`, `stock`, `qty_alert`, `vendor_id`, `business_id`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 590, 100, 1, '5', '2025-03-31 21:38:42', '2025-04-16 22:05:52'),
(3, 2, 3, 346, 100, 1, '5', '2025-03-31 21:38:42', '2025-04-02 21:22:51'),
(4, 2, 4, 382, 50, 1, '5', '2025-04-02 21:21:54', '2025-04-10 18:02:12'),
(5, 2, 5, 341, 50, 1, '5', '2025-04-02 21:21:54', '2025-05-03 23:02:13'),
(6, 2, 6, 844, 100, 1, '5', '2025-04-08 22:35:25', '2025-04-24 12:28:34'),
(7, 2, 7, 833, 10, 1, '5', '2025-04-09 13:45:50', '2025-04-27 19:57:24'),
(8, 2, 8, 28, 10, 1, '5', '2025-04-10 03:00:00', '2025-05-04 00:45:37'),
(9, 2, 9, 32, 10, 1, '5', '2025-04-10 03:00:00', '2025-05-04 12:16:46'),
(10, 3, 9, 20, 0, 1, '5', '2025-04-24 12:11:11', '2025-04-24 12:11:11'),
(11, 3, 4, 12, -1, 1, '5', NULL, NULL),
(12, 3, 12, 2, -1, 1, '5', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure for view `order_items_view`
--
DROP TABLE IF EXISTS `order_items_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_items_view`  AS SELECT `oi`.`id` AS `orders_items_id`, `c`.`name` AS `category`, `p`.`name` AS `brand`, `p`.`image` AS `image`, `w`.`id` AS `warehouse_id`, `w`.`name` AS `warehouse_name`, `oi`.`product_name` AS `product_name`, `oi`.`quantity` AS `quantity`, `oi`.`price` AS `price`, `oi`.`returned_quantity` AS `returned_quantity`, `oi`.`order_id` AS `order_id`, `oi`.`product_id` AS `product_id`, `oi`.`product_variant_id` AS `product_variant_id`, `s`.`status` AS `status`, `oi`.`status` AS `status_id` FROM (((((`orders_items` `oi` left join `products` `p` on(`oi`.`product_id` = `p`.`id`)) left join `products_variants` `pv` on(`oi`.`product_variant_id` = `pv`.`id`)) left join `categories` `c` on(`p`.`category_id` = `c`.`id`)) left join `status` `s` on(`s`.`id` = `oi`.`status`)) left join `warehouses` `w` on(`w`.`id` = `oi`.`warehouse_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_product_details`
--
DROP TABLE IF EXISTS `view_product_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_product_details`  AS SELECT `p`.`id` AS `product_id`, `p`.`name` AS `product_name`, `p`.`description` AS `description`, `p`.`image` AS `image`, `p`.`stock` AS `stock`, `p`.`status` AS `status`, `p`.`category_id` AS `category_id`, `p`.`business_id` AS `business_id`, `categories`.`name` AS `category_name`, `brands`.`name` AS `brand_name`, `u`.`first_name` AS `creator`, `p`.`brand_id` AS `brand_id` FROM (((`products` `p` left join `brands` on(`brands`.`id` = `p`.`brand_id`)) left join `categories` on(`categories`.`id` = `p`.`category_id`)) left join `users` `u` on(`u`.`id` = `p`.`vendor_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_id_code` (`business_id`,`code`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers_transactions`
--
ALTER TABLE `customers_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `vendor_id_2` (`vendor_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `delivery_boys`
--
ALTER TABLE `delivery_boys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `business_id` (`business_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses_type`
--
ALTER TABLE `expenses_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `business_id` (`business_id`);

--
-- Indexes for table `orders_items`
--
ALTER TABLE `orders_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_variant_id` (`product_variant_id`);

--
-- Indexes for table `orders_services`
--
ALTER TABLE `orders_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `order_returns`
--
ALTER TABLE `order_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages_tenures`
--
ALTER TABLE `packages_tenures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `business_id` (`business_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `tax_id` (`tax_ids`);

--
-- Indexes for table `products_variants`
--
ALTER TABLE `products_variants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases_items`
--
ALTER TABLE `purchases_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `business_id` (`business_id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `business_id` (`business_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax`
--
ALTER TABLE `tax`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `updates`
--
ALTER TABLE `updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `activation_selector` (`activation_selector`),
  ADD UNIQUE KEY `forgotten_password_selector` (`forgotten_password_selector`),
  ADD UNIQUE KEY `remember_selector` (`remember_selector`);

--
-- Indexes for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_groups_user_id_foreign` (`user_id`),
  ADD KEY `users_groups_group_id_foreign` (`group_id`);

--
-- Indexes for table `users_packages`
--
ALTER TABLE `users_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warehouse_batches`
--
ALTER TABLE `warehouse_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_batch_number` (`batch_number`),
  ADD KEY `fk_business` (`business_id`),
  ADD KEY `fk_product_variant` (`product_variant_id`),
  ADD KEY `fk_purchase_items` (`purchase_item_id`),
  ADD KEY `fk_warehouse` (`warehouse_id`);

--
-- Indexes for table `warehouse_batches_returns`
--
ALTER TABLE `warehouse_batches_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_batches_returns_business_id_foreign` (`business_id`),
  ADD KEY `warehouse_batches_returns_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `warehouse_batches_returns_purchase_item_id_foreign` (`purchase_item_id`),
  ADD KEY `warehouse_batches_returns_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `warehouse_product_stock`
--
ALTER TABLE `warehouse_product_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_product_stock_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `warehouse_product_stock_product_variant_id_foreign` (`product_variant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers_transactions`
--
ALTER TABLE `customers_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `delivery_boys`
--
ALTER TABLE `delivery_boys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses_type`
--
ALTER TABLE `expenses_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `orders_items`
--
ALTER TABLE `orders_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `orders_services`
--
ALTER TABLE `orders_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_returns`
--
ALTER TABLE `order_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages_tenures`
--
ALTER TABLE `packages_tenures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `products_variants`
--
ALTER TABLE `products_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `purchases_items`
--
ALTER TABLE `purchases_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `subscription`
--
ALTER TABLE `subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tax`
--
ALTER TABLE `tax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `updates`
--
ALTER TABLE `updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `users_packages`
--
ALTER TABLE `users_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouse_batches`
--
ALTER TABLE `warehouse_batches`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `warehouse_batches_returns`
--
ALTER TABLE `warehouse_batches_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse_product_stock`
--
ALTER TABLE `warehouse_product_stock`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `users_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `users_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `warehouse_batches`
--
ALTER TABLE `warehouse_batches`
  ADD CONSTRAINT `fk_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  ADD CONSTRAINT `fk_product_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `products_variants` (`id`),
  ADD CONSTRAINT `fk_purchase_items` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchases_items` (`id`),
  ADD CONSTRAINT `fk_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `warehouse_batches_returns`
--
ALTER TABLE `warehouse_batches_returns`
  ADD CONSTRAINT `warehouse_batches_returns_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_batches_returns_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `products_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_batches_returns_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchases_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_batches_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_product_stock`
--
ALTER TABLE `warehouse_product_stock`
  ADD CONSTRAINT `warehouse_product_stock_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `products_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `warehouse_product_stock_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
