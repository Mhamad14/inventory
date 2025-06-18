DROP TABLE IF EXISTS brands;

CREATE TABLE `brands` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(11) unsigned NOT NULL,
  `vendor_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO brands VALUES("1","5","1","مریشک","asdas","2025-04-01 02:58:55","2025-04-01 02:58:55",NULL);



DROP TABLE IF EXISTS businesses;

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO businesses VALUES("5","1","Mhamad","public/uploads/business/0f2c91febe39793fac9fccd3837b9119.jpg","o kary dasty","slemani","7741527601","slaw","500","asdsad","1","1","commandermhamad@yahoo.com","nimana","2025-04-01 00:09:45","2025-04-14 18:35:00");



DROP TABLE IF EXISTS categories;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `status` int(2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO categories VALUES("1","0","0","0","general","1","2022-01-13 08:55:49","2022-01-13 08:55:49"),
("13","0","1","5","دانە","1","2025-04-01 00:26:18","2025-04-01 00:26:18"),
("14","13","1","5","پاکەت","1","2025-04-01 00:26:31","2025-04-01 00:26:31"),
("15","14","1","5","SLAW","0","2025-04-03 00:59:20","2025-04-01 00:26:45"),
("16","0","1","5","asd","1","2025-04-01 00:27:28","2025-04-01 00:27:28");



DROP TABLE IF EXISTS currencies;

CREATE TABLE `currencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(10) unsigned NOT NULL,
  `code` varchar(3) NOT NULL COMMENT 'ISO 4217 code (IQD, USD, etc)',
  `name` varchar(50) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `symbol_position` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = before, 1 = after',
  `decimal_places` tinyint(4) NOT NULL DEFAULT 2,
  `is_base` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 = base currency (IQD)',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `business_id_code` (`business_id`,`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO currencies VALUES("1","5","USD","US Dollar","$","0","2","0","1",NULL,NULL,NULL),
("2","5","IQD","Iraqi Dinar","د.ع","1","0","1","1","2025-06-12 20:12:52","2025-06-12 20:12:52",NULL),
("5","5","EUR","Euro","%^","0","2","0","1","2025-06-13 08:05:26","2025-06-13 08:05:26","2025-06-13 08:05:36"),
("6","5","YEN","Japanese yen","@","0","2","0","1","2025-06-14 05:17:27","2025-06-14 05:17:27",NULL);



DROP TABLE IF EXISTS customers;

CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `balance` double NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO customers VALUES("2","106","5","1","0",NULL,"1","2025-04-01 00:23:33","2025-04-03 17:13:08"),
("3","107","5","1","0","1","1","2025-04-01 00:42:56","2025-04-01 00:42:56"),
("4","110","5","1","0",NULL,"1","2025-05-20 17:53:32","2025-05-20 17:53:32");



DROP TABLE IF EXISTS customers_transactions;

CREATE TABLE `customers_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `vendor_id_2` (`vendor_id`),
  KEY `customer_id` (`customer_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO customers_transactions VALUES("4","106","5","1","2","0","0","1","2","cash","credit","","100","0","100","","dwfsd","2025-04-01 00:24:31","2025-04-01 00:24:31"),
("5","106","5","1","2","0","0","1","2","cash","debit","","50","100","50","","dsfs","2025-04-01 00:25:24","2025-04-01 00:25:24"),
("6","0","0","1","2","0","41","1",NULL,"cash","","","200","0","0","","","2025-04-01 01:00:15","2025-04-01 01:00:15"),
("7","106","5","1","2","0","41","1","0","cash","credit","","300","0","300","","","2025-04-01 01:03:13","2025-04-01 01:03:13"),
("8","0","0","1","2","0","43","1",NULL,"cash","","","17000","0","0","","","2025-04-01 01:06:53","2025-04-01 01:06:53"),
("9","106","5","1","2","0","43","1","0","cash","credit","","163000","0","163000","","asdsad","2025-04-01 01:08:40","2025-04-01 01:08:40"),
("10","0","0","1","2","0","44","1",NULL,"cash","","","400","0","0","","","2025-04-03 00:22:51","2025-04-03 00:22:51"),
("11","106","5","1","2","0","44","1","0","cash","credit","","56","0","56","","sdfsdf","2025-04-03 00:23:41","2025-04-03 00:23:41"),
("12","0","0","1","2","0","45","1",NULL,"cash","","","300","0","0","","","2025-04-03 00:24:34","2025-04-03 00:24:34"),
("13","106","5","1","2","0","45","1","0","cash","credit","","200","0","200","","sdfsdf","2025-04-03 00:25:13","2025-04-03 00:25:13"),
("14","0","0","1","2","0","46","1",NULL,"wallet","","","5","0","0","657876","","2025-04-03 01:06:05","2025-04-03 01:06:05"),
("15","0","0","1","2","0","47","1",NULL,"cash","","","100","0","0","","","2025-04-03 17:12:33","2025-04-03 17:12:33"),
("16","106","5","1","2","0","47","1","0","cash","debit","","45","0","45","","","2025-04-03 17:13:08","2025-04-03 17:13:08"),
("17","106","5","1","2","0","47","1","0","cash","credit","","300","0","300","","","2025-04-03 17:13:35","2025-04-03 17:13:35"),
("18","106","5","1","2","0","47","1","0","cash","credit","","5","0","5","","","2025-04-03 17:13:53","2025-04-03 17:13:53"),
("19","0","0","1","2","0","48","1",NULL,"cash","","","300","0","0","","","2025-04-05 02:55:21","2025-04-05 02:55:21"),
("20","106","5","1","2","0","48","1","0","cash","credit","","100","0","100","","100 yawa","2025-04-05 02:56:16","2025-04-05 02:56:16"),
("21","0","0","1","2","0","49","1",NULL,"cash","","","10","0","0","","","2025-04-06 04:34:06","2025-04-06 04:34:06"),
("22","106","5","1","2","0","50","1","0","cash","credit","","50","0","50","","sdf","2025-04-07 14:39:32","2025-04-07 14:39:32"),
("23","0","0","1","3","0","58","1",NULL,"cash","","","20","0","0","","","2025-04-10 21:02:11","2025-04-10 21:02:11"),
("24","107","5","1","3","0","58","1","0","cash","credit","","30","0","30","","dfsdfdsf","2025-04-10 21:03:35","2025-04-10 21:03:35"),
("25","107","5","1","3","0","58","1","0","cash","credit","","877","0","877","","sdf","2025-04-14 15:38:19","2025-04-14 15:38:19"),
("26","3","5","1","2","0","63","1",NULL,"cash","","","100","0","0","","","2025-04-21 20:46:04","2025-04-23 02:22:40"),
("27","2","0","1","2","0","64","1",NULL,"cash","","","100","0","0","","","2025-04-21 20:46:17","2025-04-23 02:22:07"),
("28","0","0","1","3","0","65","1",NULL,"cash","","","49","0","0","","","2025-04-23 02:50:58","2025-04-23 02:50:58"),
("29","107","5","1","3","0","65","1","0","cash","credit","","50","0","50","","dsfdfds","2025-04-23 02:51:23","2025-04-23 02:51:23"),
("30","0","0","1","3","0","70","1",NULL,"cash","","","300","0","0","","","2025-05-19 23:29:30","2025-05-19 23:29:30"),
("31","0","0","1","3","0","73","1",NULL,"cash","","","200","0","0","","","2025-05-21 19:33:26","2025-05-21 19:33:26"),
("32","0","0","1","3","0","76","1",NULL,"cash","","","100","0","0","","","2025-06-08 05:45:13","2025-06-08 05:45:13");



DROP TABLE IF EXISTS delivery_boys;

CREATE TABLE `delivery_boys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `business_id` varchar(30) NOT NULL,
  `permissions` varchar(512) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `business_id` (`business_id`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO delivery_boys VALUES("2","109","1","5","{\"customer_permission\":\"1\",\"transaction_permission\":\"1\",\"orders_permission\":\"1\"}","1","2025-04-01 01:10:37","2025-04-01 01:10:37"),
("3","105","1","5","{\"customer_permission\":\"0\",\"transaction_permission\":\"0\",\"orders_permission\":\"1\"}","1","2025-04-17 01:04:10","2025-04-17 01:04:10");



DROP TABLE IF EXISTS employees;

CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `position_id` int(11) NOT NULL,
  `address` varchar(150) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `salary` int(11) NOT NULL,
  `busniess_id` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO employees VALUES("1","asd","1","asd","234234","400","5",NULL,NULL);



DROP TABLE IF EXISTS exchange_rates;

CREATE TABLE `exchange_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL,
  `rate` double NOT NULL,
  `effective_date` datetime NOT NULL COMMENT 'Exact datetime when rate became effective',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO exchange_rates VALUES("1","1","2","2025-06-14 00:00:00","2025-06-14 02:58:30");



DROP TABLE IF EXISTS expenses;

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `expenses_id` varchar(512) NOT NULL,
  `note` varchar(512) NOT NULL,
  `amount` varchar(512) NOT NULL,
  `expenses_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO expenses VALUES("1","5","1","1","کاک وەلید وو","50","2025-04-01","2025-04-01 00:20:46","0000-00-00 00:00:00"),
("2","5","1","1","کاک وەلید وو","50","2025-05-19","2025-05-19 23:33:54","0000-00-00 00:00:00");



DROP TABLE IF EXISTS expenses_type;

CREATE TABLE `expenses_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `title` varchar(512) NOT NULL,
  `description` varchar(512) NOT NULL,
  `expenses_type_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO expenses_type VALUES("1","1","کارەبای مۆلیدە","وەسڵی مۆلیدە","2025-04-01","2025-04-01 00:18:51","0000-00-00 00:00:00");



DROP TABLE IF EXISTS getorderdetails;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `getorderdetails` AS select `o`.`id` AS `id`,`o`.`vendor_id` AS `vendor_id`,`o`.`customer_id` AS `customer_id`,`o`.`warehouse_id` AS `warehouse_id`,`o`.`order_no` AS `order_no`,`o`.`business_id` AS `business_id`,`o`.`created_by` AS `created_by`,`o`.`total` AS `total`,`o`.`delivery_charges` AS `delivery_charges`,`o`.`discount` AS `discount`,`o`.`final_total` AS `final_total`,`o`.`returns_total` AS `returns_total`,`o`.`payment_status` AS `payment_status`,`o`.`amount_paid` AS `amount_paid`,`o`.`order_type` AS `order_type`,`o`.`message` AS `message`,`o`.`payment_method` AS `payment_method`,`o`.`created_at` AS `created_at`,`o`.`updated_at` AS `updated_at`,`o`.`is_pos_order` AS `is_pos_order`,`o`.`final_total` - `o`.`returns_total` - `o`.`amount_paid` AS `debt`,(select `u`.`first_name` from (`customers` `c` join `users` `u` on(`u`.`id` = `c`.`user_id`)) where `c`.`id` = `o`.`customer_id` limit 1) AS `customer_name`,(select `users`.`first_name` from `users` where `users`.`id` = `o`.`created_by` limit 1) AS `creator_name`,(select `g`.`name` from (`users_groups` `ug` join `groups` `g` on(`g`.`id` = `ug`.`group_id`)) where `ug`.`user_id` = `o`.`created_by` limit 1) AS `creator_role` from `orders` `o`;

INSERT INTO getorderdetails VALUES("39","1","2","2","1","5","1","150000","0","0","150000","0.00","fully_paid","150000","product","aASSa","cash","2025-04-01 00:45:41","2025-06-09 21:07:44","0","0","عەباس","admin","admin"),
("40","1","2",NULL,NULL,"5","1","1350","0","0","1350","0.00","fully_paid","1350","product","","cash","2025-04-01 00:51:02","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("41","1","2",NULL,NULL,"5","1","500","0","0","500","0.00","fully_paid","500","product","","cash","2025-04-01 01:00:15","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("42","1","2",NULL,NULL,"5","1","500","0","0","500","0.00","fully_paid","500","product","asd","cash","2025-04-01 01:02:20","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("43","1","2",NULL,NULL,"5","1","180000","0","0","180000","0.00","fully_paid","180000","product","","cash","2025-04-01 01:06:53","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("44","1","2",NULL,NULL,"5","1","456","0","0","456","0.00","fully_paid","456","product","adasda","cash","2025-04-03 00:22:51","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("45","1","2",NULL,NULL,"5","1","505","0","0","500","5.00","fully_paid","500","product","xcvxvc","cash","2025-04-03 00:24:34","2025-06-09 21:07:44","1","-5","عەباس","admin","admin"),
("46","1","2",NULL,NULL,"5","1","5","0","0","5","0.00","fully_paid","5","service","","wallet","2025-04-03 01:06:05","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("47","1","2",NULL,NULL,"5","1","505","0","0","500","5.00","fully_paid","500","product","","cash","2025-04-03 17:12:33","2025-06-09 21:07:44","1","-5","عەباس","admin","admin"),
("48","1","2",NULL,NULL,"5","1","505","0","0","5","500.00","fully_paid","5","product","tawawa","cash","2025-04-05 02:55:21","2025-06-09 21:07:44","1","-500","عەباس","admin","admin"),
("49","1","2",NULL,NULL,"5","1","20","0","0","10","10.00","fully_paid","10","product","sadasd","cash","2025-04-06 04:34:06","2025-06-09 21:07:44","1","-10","عەباس","admin","admin"),
("50","1","2",NULL,NULL,"5","1","1450","0","0","50","1400.00","fully_paid","50","product","dsf",NULL,"2025-04-06 04:51:49","2025-06-09 21:07:44","1","-1400","عەباس","admin","admin"),
("51","1","2",NULL,NULL,"5","1","25000","0","0","20000","5000.00","fully_paid","20000","product","asdsadasdas","cash","2025-04-09 01:26:05","2025-06-09 21:07:44","1","-5000","عەباس","admin","admin"),
("52","1","3",NULL,NULL,"5","1","100","0","0","86","14.00","fully_paid","86","product","asdasdasd","cash","2025-04-09 01:37:09","2025-06-09 21:07:44","1","-14","mhamad","admin","admin"),
("53","1","3",NULL,NULL,"5","1","500","0","0","500","0.00","fully_paid","500","product","asdad","cash","2025-04-09 01:43:20","2025-06-09 21:07:44","1","0","mhamad","admin","admin"),
("54","1","3",NULL,NULL,"5","1","24000","0","0","24000","0.00","fully_paid","24000","product","asdasdasdasd","cash","2025-04-09 16:46:29","2025-06-09 21:07:44","1","0","mhamad","admin","admin"),
("55","1","3",NULL,NULL,"5","1","100","0","0","100","0.00","fully_paid","100","product","asd","cash","2025-04-09 16:47:10","2025-06-09 21:07:44","1","0","mhamad","admin","admin"),
("56","1","3",NULL,NULL,"5","1","3000","0","0","3000","0.00","fully_paid","3000","product","asdasdasd","cash","2025-04-09 20:29:13","2025-06-09 21:07:44","1","0","mhamad","admin","admin"),
("57","1","3",NULL,NULL,"5","1","400","0","0","400","0.00","fully_paid","400","product","asdasdas","cash","2025-04-10 05:08:29","2025-06-09 21:07:44","1","0","mhamad","admin","admin"),
("58","1","3",NULL,NULL,"5","1","939","0","0","927","12.00","fully_paid","927","product","fafasdassdf","cash","2025-04-10 21:02:11","2025-06-09 21:07:44","1","-12","mhamad","admin","admin"),
("59","1","2",NULL,NULL,"5","1","20000","0","0","20000","0.00","fully_paid","20000","product","ASDASDASD","cash","2025-04-11 00:38:01","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("60","1","2",NULL,NULL,"5","1","502","0","0","2","500.00","unpaid","502","product","sdf","cash","2025-04-13 22:18:19","2025-06-09 21:07:44","1","-1000","عەباس","admin","admin"),
("61","1","2",NULL,NULL,"5","1","902","0","0","902","0.00","fully_paid","902","product","asdasdasd","cash","2025-04-16 23:58:19","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("62","1","106",NULL,NULL,"5","105","903","0","0","903","0.00","fully_paid","903","product","","cash","2025-04-17 01:05:52","2025-06-09 21:07:44","0","0",NULL,"zanko","delivery_boys"),
("63","1","2",NULL,NULL,"5","1","401","0","0","0","401.00","unpaid","100","product","","cash","2025-04-21 20:46:04","2025-06-09 21:07:44","1","-501","عەباس","admin","admin"),
("64","1","2",NULL,NULL,"5","1","400","0","0","400","0.00","partially_paid","100","product","","cash","2025-04-21 20:46:17","2025-06-09 21:07:44","1","300","عەباس","admin","admin"),
("65","1","3",NULL,NULL,"5","1","402","0","0","402","0.00","partially_paid","317","product","asdasd","cash","2025-04-23 02:50:58","2025-06-09 21:07:44","1","85","mhamad","admin","admin"),
("66","1","2",NULL,NULL,"5","1","2","0","0","2","0.00","fully_paid","2","product","sdfsdf","cash","2025-04-24 15:11:46","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("67","1","2",NULL,NULL,"5","1","402","0","0","402","0.00","fully_paid","402","product","fszdfdas","cash","2025-04-24 15:28:34","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("68","1","2",NULL,NULL,"5","1","400","0","0","400","0.00","fully_paid","400","product","asdsad","cash","2025-04-27 22:57:24","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("69","1","2",NULL,NULL,"5","1","402","0","0","402","0.00","fully_paid","402","product","","cash","2025-05-03 17:17:44","2025-06-09 21:07:44","1","0","عەباس","admin","admin"),
("70","1","3",NULL,NULL,"5","1","2700","0","0","2700","0.00","partially_paid","300","product","","cash","2025-05-19 23:29:30","2025-06-09 21:07:44","1","2400","mhamad","admin","admin"),
("71","1","3",NULL,NULL,"5","1","402","0","0","402","0.00","partially_paid","0","product","","cash","2025-05-21 19:31:04","2025-06-09 21:07:44","1","402","mhamad","admin","admin"),
("72","1","3",NULL,NULL,"5","1","400","0","0","400","0.00","partially_paid","0","product","sdfsdf","cash","2025-05-21 19:31:37","2025-06-09 21:07:44","1","400","mhamad","admin","admin"),
("73","1","3",NULL,NULL,"5","1","400","0","0","400","0.00","partially_paid","200","product","sdfsdf","cash","2025-05-21 19:33:26","2025-06-09 21:07:44","1","200","mhamad","admin","admin"),
("74","1","2",NULL,NULL,"5","1","502","0","0","502","0.00","partially_paid","0","product","asd","cash","2025-06-08 05:41:40","2025-06-09 21:07:44","1","502","عەباس","admin","admin"),
("75","1","2",NULL,NULL,"5","1","505","0","0","505","0.00","partially_paid","0","product","sadasd","cash","2025-06-08 05:42:49","2025-06-09 21:07:44","1","505","عەباس","admin","admin"),
("76","1","3",NULL,NULL,"5","1","1","0","0","1","0.00","unpaid","100","product","asd","cash","2025-06-08 05:45:13","2025-06-09 21:07:44","1","-99","mhamad","admin","admin");



DROP TABLE IF EXISTS groups;

CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO groups VALUES("1","admin","Administrator"),
("2","vendors","Vendors"),
("3","delivery_boys","Delivery boys"),
("4","customers","Customers"),
("5","suppliers","Suppliers"),
("6","team_members","Team Members");



DROP TABLE IF EXISTS languages;

CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `is_rtl` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO languages VALUES("1","english","en","0","2022-04-26 14:23:14"),
("5","کوردی","ku","1","2025-04-01 00:12:37");



DROP TABLE IF EXISTS login_attempts;

CREATE TABLE `login_attempts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) DEFAULT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;




DROP TABLE IF EXISTS migrations;

CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO migrations VALUES("1","2023_05_29_113905","App\\Database\\Migrations\\Address_Field","default","App","1695452760","1"),
("2","2023-09-19-101401","App\\Database\\Migrations\\User_permissions","default","App","1695452811","2"),
("3","2024-08-22-092910","App\\Database\\Migrations\\Add_Order_No_To_OrdersTable","default","App","1724838698","3"),
("4","2024-08-22-124010","App\\Database\\Migrations\\Add_Order_No_To_OrdersTable","default","App","1724838796","4"),
("5","2024-09-06-063500","App\\Database\\Migrations\\CreateTeamMembersTable","default","App","1727937481","5"),
("6","2024-09-30-045119","App\\Database\\Migrations\\ModifyProductsTableTax","default","App","1727937481","5"),
("7","2024-10-01-094305","App\\Database\\Migrations\\AddTaxDetailsToOrderTable","default","App","1727937481","5"),
("8","2024-10-02-035805","App\\Database\\Migrations\\ModifyTaxIdInPurchaseTable","default","App","1727937481","5"),
("9","2024-10-04-044813","App\\Database\\Migrations\\ChangeTaxIdToTaxIdsToServiceTable","default","App","1728017386","6"),
("10","2024-10-07-051345","App\\Database\\Migrations\\AddTaxDetailsToOrderServiceTable","default","App","1728278101","7"),
("11","2024-10-11-060741","App\\Database\\Migrations\\AddWarehouseTable","default","App","1729594787","8"),
("12","2024-10-11-061727","App\\Database\\Migrations\\AddWarehouseProductStockTable","default","App","1729594787","8"),
("13","2024-10-22-040400","App\\Database\\Migrations\\AddWarehouseIdToOrderTable","default","App","1729594787","8"),
("14","2024-10-22-040414","App\\Database\\Migrations\\AddWarehouseIdToPurchasesTable","default","App","1729594787","8"),
("15","2024-11-22-115056","App\\Database\\Migrations\\AddUserIdToCustomersTransactions","default","App","1738991529","9"),
("16","2025-01-22-033558","App\\Database\\Migrations\\ModifyPurchasesTable","default","App","1738991529","9"),
("17","2025-01-22-064048","App\\Database\\Migrations\\ModifyOrdersTable","default","App","1738991529","9"),
("18","2025-01-30-060358","App\\Database\\Migrations\\AddBarcodeToProductVariantsTable","default","App","1738991529","9"),
("19","2025-01-30-065314","App\\Database\\Migrations\\ModifyWharehouseProductStockTable","default","App","1738991529","9"),
("20","2025-02-04-093722","App\\Database\\Migrations\\AddIsPosOrderOrdersTable","default","App","1738991629","10"),
("21","2025-02-07-094001","App\\Database\\Migrations\\CreateBrandsTable","default","App","1738991629","10"),
("22","2025-02-08-032228","App\\Database\\Migrations\\AddBrandToProductsTable","default","App","1738991629","10"),
("23","2025-02-08-034420","App\\Database\\Migrations\\RemoveUnsignFromBrandID","default","App","1738991630","10"),
("24","2025-04-14-201318","App\\Database\\Migrations\\CreateDraftOrdersTable","default","App","1744661623","11"),
("25","2025-04-14-205615","App\\Database\\Migrations\\AddDraftsTable","default","App","1744664229","12"),
("26","2025-04-27-183238","App\\Database\\Migrations\\CreateEmployeesTable","default","App","1747752202","13"),
("27","2025-04-27-191532","App\\Database\\Migrations\\CreatePositionsTable","default","App","1747752202","13"),
("28","2025-05-09-162127","App\\Database\\Migrations\\CreateSpPartialCustomerPayment","default","App","1747752202","13"),
("29","2025-05-18-155323","App\\Database\\Migrations\\AlterOrdersAmountPaidToFloat","default","App","1747752203","13"),
("30","2025-05-18-192002","App\\Database\\Migrations\\CreateOrdersItemsView","default","App","1747752203","13"),
("33","2025-05-19-000001","App\\Database\\Migrations\\AddBusinessIdToPositions","default","App","1747758360","14"),
("34","2024-03-21-000001","App\\Database\\Migrations\\CreateCurrenciesTable","default","App","1749348158","15"),
("35","2024-03-21-000002","App\\Database\\Migrations\\AddCurrencyToOrdersAndPurchases","default","App","1749348158","15"),
("36","2025-05-20-145812","App\\Database\\Migrations\\CreateOrderDetailsView","default","App","1749348259","16"),
("37","2025-05-21-142011","App\\Database\\Migrations\\CreateWarehouseBatchesTable","default","App","1749405052","17"),
("38","2025-05-23-011908","App\\Database\\Migrations\\RemovePriceColumnsFromProductsVariants","default","App","1749405052","17"),
("39","2025-05-24-074746","App\\Database\\Migrations\\RemoveTypeFromProducts","default","App","1749405052","17"),
("40","2025-05-24-130555","App\\Database\\Migrations\\CreateViewProducts","default","App","1749405052","17"),
("41","2025-05-25-030029","App\\Database\\Migrations\\UpdateStockDefaultInProductsAndVariants","default","App","1749405052","17"),
("42","2025-05-25-030802","App\\Database\\Migrations\\TriggerStockForProductsAndVariants","default","App","1749405052","17"),
("43","2025-05-25-160000","App\\Database\\Migrations\\AddSellPriceToWarehouseBatches","default","App","1749405052","17"),
("44","2025-05-30-045314","App\\Database\\Migrations\\CreateWarehouseBatchesReturnsTable","default","App","1749405052","17"),
("45","2024-03-19-000001","App\\Database\\Migrations\\CreateCurrenciesTable","default","App","1749413538","18"),
("46","2025-06-09-013147","App\\Database\\Migrations\\CreateCurrenciesTable","default","App","1749432802","19"),
("47","2025-05-31-000001","App\\Database\\Migrations\\CreateCurrenciesTable","default","App","1749433302","20"),
("48","2025-05-31-000002","App\\Database\\Migrations\\CreateExchangeRatesTable","default","App","1749433302","20"),
("49","2025-05-31-000003","App\\Database\\Migrations\\AddCurrencyToOrdersTable","default","App","1749433302","20"),
("50","2024-03-19-000002","App\\Database\\Migrations\\AddCurrencyToPurchases","default","App","1749435750","21"),
("51","2024-03-20-000001","App\\Database\\Migrations\\CreateCurrenciesTable","default","App","1749435981","22"),
("52","2024-03-19-000002","App\\Database\\Migrations\\CreateExchangeRatesTable","default","App","1749492463","23"),
("53","2024-03-19-000003","App\\Database\\Migrations\\CreateCurrencyTransactionsTable","default","App","1749492464","23"),
("54","2024-03-19-000004","App\\Database\\Migrations\\ModifyOrdersForMultiCurrency","default","App","1749492464","23"),
("55","2024-03-19-000005","App\\Database\\Migrations\\ModifyPurchasesForMultiCurrency","default","App","1749492464","23"),
("56","2024-03-19-000006","App\\Database\\Migrations\\ModifyPurchaseItemsForMultiCurrency","default","App","1749493003","24"),
("60","2025-06-11-230449","App\\Database\\Migrations\\CreateExchangeRatesTable","default","App","1749683566","25"),
("61","2025-06-11-230509","App\\Database\\Migrations\\CreatePaymentsTable","default","App","1749683566","25"),
("66","2025-06-11-232350","App\\Database\\Migrations\\CreateCurrenciesTable","default","App","1749684324","26"),
("67","2025-06-11-232405","App\\Database\\Migrations\\CreateExchangeRatesTable","default","App","1749684355","27"),
("68","2025-06-11-232410","App\\Database\\Migrations\\CreatePaymentsTable","default","App","1749684380","28"),
("69","2025-06-13-000001","App\\Database\\Migrations\\AddDeletedAtToCurrenciesTable","default","App","1749759529","29"),
("70","2025-06-12-073043","App\\Database\\Migrations\\CreateWarehouseBatchesTriggersStock","default","App","1749861684","30"),
("71","2024-03-21-000001","App\\Database\\Migrations\\AddExpiryAlertToProductsVariants","default","App","1749868722","31"),
("72","2025-06-15-132820","App\\Database\\Migrations\\ImproveExchangeRate","default","App","1750123476","32");



DROP TABLE IF EXISTS order_items_view;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_items_view` AS select `oi`.`id` AS `orders_items_id`,`c`.`name` AS `category`,`p`.`name` AS `brand`,`p`.`image` AS `image`,`w`.`id` AS `warehouse_id`,`w`.`name` AS `warehouse_name`,`oi`.`product_name` AS `product_name`,`oi`.`quantity` AS `quantity`,`oi`.`price` AS `price`,`oi`.`returned_quantity` AS `returned_quantity`,`oi`.`order_id` AS `order_id`,`oi`.`product_id` AS `product_id`,`oi`.`product_variant_id` AS `product_variant_id`,`s`.`status` AS `status`,`oi`.`status` AS `status_id` from (((((`orders_items` `oi` left join `products` `p` on(`oi`.`product_id` = `p`.`id`)) left join `products_variants` `pv` on(`oi`.`product_variant_id` = `pv`.`id`)) left join `categories` `c` on(`p`.`category_id` = `c`.`id`)) left join `status` `s` on(`s`.`id` = `oi`.`status`)) left join `warehouses` `w` on(`w`.`id` = `oi`.`warehouse_id`));

INSERT INTO order_items_view VALUES("7","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکە","300","500","0","39","17","2","تەواو ووە","5"),
("8","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سور","3","450","0","40","17","3","تەواو ووە","5"),
("9","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","41","17","2","قەرز","7"),
("10","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","42","17","2","زیا بوون","8"),
("11","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سور","400","450","0","43","17","3","زیا بوون","8"),
("12","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سپی","1","6","0","44","18","5","زیا بوون","8"),
("13","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سور","1","450","0","44","17","3","زیا بوون","8"),
("14","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سوور","1","5","1","45","18","4","زیا بوون","8"),
("15","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","45","17","2","زیا بوون","8"),
("16","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سوور","1","5","1","47","18","4","زیا بوون","8"),
("17","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","47","17","2","زیا بوون","8"),
("18","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","48","17","2","زیا بوون","8"),
("19","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سوور","1","5","0","48","18","4","زیا بوون","8"),
("20","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سوور","4","5","0","49","18","4","تەواو ووە","5"),
("21","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سوور","290","5","290","50","18","4","تەواو ووە","5"),
("22","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","50","500","10","51","17","2","تەواو ووە","5"),
("23","general","سلاو","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg",NULL,NULL,"سلاو","50","2","7","52","19","6","تەواو ووە","5"),
("24","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","53","17","2","تەواو ووە","5"),
("25","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","60","400","0","54","20","7","تەواو ووە","5"),
("26","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سوور","20","5","0","55","18","4","تەواو ووە","5"),
("27","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سوور","600","5","0","56","18","4","تەواو ووە","5"),
("28","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","57","20","7","تەواو ووە","5"),
("29","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سپی","5","6","2","58","18","5","تەواو ووە","5"),
("30","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","58","20","7","تەواو ووە","5"),
("31","general","سلاو","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg",NULL,NULL,"سلاو","1","2","0","58","19","6","تەواو ووە","5"),
("32","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","58","17","2","تەواو ووە","5"),
("33","general","test","public/uploads/products/457624148_899136962237390_8858762611595115315_n.jpg",NULL,NULL,"test2","1","2","0","58","21","9","تەواو ووە","5"),
("34","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سوور","1","5","0","58","18","4","تەواو ووە","5"),
("35","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","40","500","0","59","17","2","تەواو ووە","5"),
("36","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","1","60","17","2","تەواو ووە","5"),
("37","general","سلاو","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg",NULL,NULL,"سلاو","1","2","0","60","19","6","تەواو ووە","5"),
("38","general","سلاو","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg",NULL,NULL,"سلاو","1","2","0","61","19","6","تەواو ووە","5"),
("39","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","61","20","7","تەواو ووە","5"),
("40","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","61","17","2","تەواو ووە","5"),
("41","general","سلاو","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg",NULL,NULL,"سلاو","1","2","0","62","19","6","تەواو ووە","5"),
("42","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","62","20","7","تەواو ووە","5"),
("43","general","test","public/uploads/products/457624148_899136962237390_8858762611595115315_n.jpg",NULL,NULL,"test1","1","1","0","62","21","8","تەواو ووە","5"),
("44","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","62","17","2","تەواو ووە","5"),
("45","general","test","public/uploads/products/457624148_899136962237390_8858762611595115315_n.jpg",NULL,NULL,"test1","1","1","1","63","21","8","تەواو ووە","5"),
("46","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","1","63","20","7","تەواو ووە","5"),
("47","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","64","20","7","تەواو ووە","5"),
("48","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","65","20","7","تەواو ووە","5"),
("49","general","سلاو","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg",NULL,NULL,"سلاو","1","2","0","65","19","6","تەواو ووە","5"),
("50","general","test","public/uploads/products/457624148_899136962237390_8858762611595115315_n.jpg",NULL,NULL,"test2","1","2","0","66","21","9","تەواو ووە","5"),
("51","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","67","20","7","تەواو ووە","5"),
("52","general","سلاو","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg",NULL,NULL,"سلاو","1","2","0","67","19","6","تەواو ووە","5"),
("53","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","68","20","7","تەواو ووە","5"),
("54","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","69","20","7","تەواو ووە","5"),
("55","general","سلاو","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg",NULL,NULL,"سلاو","1","2","0","69","19","6","تەواو ووە","5"),
("56","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سور","6","450","0","70","17","3","تەواو ووە","5"),
("57","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","71","20","7","تەواو ووە","5"),
("58","general","سلاو","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg",NULL,NULL,"سلاو","1","2","0","71","19","6","تەواو ووە","5"),
("59","پاکەت","مریشکی گورزە","public/uploads/products/344751598_198947899627155_3579641464036871083_n.jpg",NULL,NULL,"مریشکی گورزە","1","400","0","72","22","10","تەواو ووە","5"),
("60","general","شیر","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg",NULL,NULL,"asdasd","1","400","0","73","20","7","تەواو ووە","5"),
("61","general","test","public/uploads/products/457624148_899136962237390_8858762611595115315_n.jpg",NULL,NULL,"test1","2","1","0","74","21","8","تەواو ووە","5"),
("62","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","74","17","2","تەواو ووە","5"),
("63","general","مریشک","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg",NULL,NULL,"مریشک سوور","1","5","0","75","18","4","تەواو ووە","5"),
("64","general","هێلکە","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg",NULL,NULL,"هێلکەی سپی","1","500","0","75","17","2","تەواو ووە","5"),
("65","general","test","public/uploads/products/457624148_899136962237390_8858762611595115315_n.jpg",NULL,NULL,"test1","1","1","0","76","21","8","تەواو ووە","5");



DROP TABLE IF EXISTS order_returns;

CREATE TABLE `order_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `return_date` datetime NOT NULL DEFAULT current_timestamp(),
  `return_reason` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `processed_by` int(11) DEFAULT NULL,
  `processed_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO order_returns VALUES("1","50","21","2","5.00","10.00","2025-04-07 23:58:53","fgh","1",NULL,"2025-04-07 23:58:53"),
("2","50","21","3","5.00","15.00","2025-04-07 23:59:00","dfg","1",NULL,"2025-04-07 23:59:00"),
("3","50","21","2","5.00","10.00","2025-04-08 00:02:37","asd","1",NULL,"2025-04-08 00:02:37"),
("4","50","21","3","5.00","15.00","2025-04-08 00:03:39","asd","1",NULL,"2025-04-08 00:03:39"),
("5","50","21","5","5.00","25.00","2025-04-08 01:12:16","sdfsdfsdfsdfsd","processed","1",NULL),
("6","50","21","5","5.00","25.00","2025-04-08 01:12:42",",m ,mjlkl;jjpjpi","processed","1",NULL),
("7","50","21","6","5.00","30.00","2025-04-08 01:13:29","jkhgiulgigu867","processed","1",NULL),
("8","50","21","16","5.00","80.00","2025-04-08 01:15:06","asdasdsdasdasd","processed","1",NULL),
("9","50","21","1","5.00","5.00","2025-04-08 01:16:44","sdfsdfsddfsdfsd","processed","1",NULL),
("10","48","18","1","500.00","500.00","2025-04-09 03:07:38","dasasdasddasd","processed","1",NULL),
("11","49","20","2","5.00","10.00","2025-04-09 03:08:26","sdfdsfdsfdsf","processed","1",NULL),
("12","50","21","6","5.00","30.00","2025-04-09 03:16:54","asdsadsadasdsa","processed","1",NULL),
("13","50","21","5","5.00","25.00","2025-04-09 03:19:32","asdsadasdasdasds","processed","1",NULL),
("14","50","21","6","5.00","30.00","2025-04-09 03:53:26","asdsadasdasdasdas","processed","1",NULL),
("15","50","21","5","5.00","25.00","2025-04-09 03:54:09","asdsadasdasdsad","processed","1",NULL),
("16","50","21","225","5.00","1125.00","2025-04-09 03:55:13","asdasdasdasdasdasdasd","processed","1",NULL),
("17","51","22","4","500.00","2000.00","2025-04-09 03:56:31","asdasdasdasdads","processed","1",NULL),
("18","51","22","4","500.00","2000.00","2025-04-09 04:00:14","zxczxczxczxczxc","processed","1",NULL),
("19","52","23","5","2.00","10.00","2025-04-09 04:07:39","erwrwerwerwer","processed","1",NULL),
("20","51","22","1","500.00","500.00","2025-04-09 04:11:12","sdfsdfsdfsdfsfd","processed","1",NULL),
("21","51","22","1","500.00","500.00","2025-04-09 04:12:22","asdasdsadasdsadasd","processed","1",NULL),
("22","52","23","1","2.00","2.00","2025-04-09 19:06:05","asdasdsadasdasda","processed","1",NULL),
("23","52","23","1","2.00","2.00","2025-04-09 19:06:28","dsfsfdsfsdfsd","processed","1",NULL),
("24","47","16","1","5.00","5.00","2025-04-09 19:07:45","asdasdasdsadasd","processed","1",NULL),
("25","45","14","1","5.00","5.00","2025-04-09 19:08:32","asdasdsadasds","processed","1",NULL),
("26","58","29","2","6.00","12.00","2025-04-10 23:34:24","dfsdfdsffsdfsdfs","processed","1",NULL),
("27","60","36","1","500.00","500.00","2025-04-17 02:26:40","fvfsdxcvxcvxc","processed","1",NULL),
("28","63","45","1","1.00","1.00","2025-04-24 02:03:59","etrgsdfgszfsdf","processed","1",NULL),
("29","63","46","1","400.00","400.00","2025-04-24 02:03:59","etrgsdfgszfsdf","processed","1",NULL);



DROP TABLE IF EXISTS orders;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `warehouse_id` int(11) unsigned DEFAULT NULL,
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
  `is_pos_order` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `customer_id` (`customer_id`),
  KEY `business_id` (`business_id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO orders VALUES("39","1","2","2","1","5","1","150000","0","0","150000","0.00","fully_paid","150000","product","aASSa","cash","2025-04-01 00:45:41","2025-06-09 21:07:44","0"),
("40","1","2",NULL,NULL,"5","1","1350","0","0","1350","0.00","fully_paid","1350","product","","cash","2025-04-01 00:51:02","2025-06-09 21:07:44","1"),
("41","1","2",NULL,NULL,"5","1","500","0","0","500","0.00","fully_paid","500","product","","cash","2025-04-01 01:00:15","2025-06-09 21:07:44","1"),
("42","1","2",NULL,NULL,"5","1","500","0","0","500","0.00","fully_paid","500","product","asd","cash","2025-04-01 01:02:20","2025-06-09 21:07:44","1"),
("43","1","2",NULL,NULL,"5","1","180000","0","0","180000","0.00","fully_paid","180000","product","","cash","2025-04-01 01:06:53","2025-06-09 21:07:44","1"),
("44","1","2",NULL,NULL,"5","1","456","0","0","456","0.00","fully_paid","456","product","adasda","cash","2025-04-03 00:22:51","2025-06-09 21:07:44","1"),
("45","1","2",NULL,NULL,"5","1","505","0","0","500","5.00","fully_paid","500","product","xcvxvc","cash","2025-04-03 00:24:34","2025-06-09 21:07:44","1"),
("46","1","2",NULL,NULL,"5","1","5","0","0","5","0.00","fully_paid","5","service","","wallet","2025-04-03 01:06:05","2025-06-09 21:07:44","1"),
("47","1","2",NULL,NULL,"5","1","505","0","0","500","5.00","fully_paid","500","product","","cash","2025-04-03 17:12:33","2025-06-09 21:07:44","1"),
("48","1","2",NULL,NULL,"5","1","505","0","0","5","500.00","fully_paid","5","product","tawawa","cash","2025-04-05 02:55:21","2025-06-09 21:07:44","1"),
("49","1","2",NULL,NULL,"5","1","20","0","0","10","10.00","fully_paid","10","product","sadasd","cash","2025-04-06 04:34:06","2025-06-09 21:07:44","1"),
("50","1","2",NULL,NULL,"5","1","1450","0","0","50","1400.00","fully_paid","50","product","dsf",NULL,"2025-04-06 04:51:49","2025-06-09 21:07:44","1"),
("51","1","2",NULL,NULL,"5","1","25000","0","0","20000","5000.00","fully_paid","20000","product","asdsadasdas","cash","2025-04-09 01:26:05","2025-06-09 21:07:44","1"),
("52","1","3",NULL,NULL,"5","1","100","0","0","86","14.00","fully_paid","86","product","asdasdasd","cash","2025-04-09 01:37:09","2025-06-09 21:07:44","1"),
("53","1","3",NULL,NULL,"5","1","500","0","0","500","0.00","fully_paid","500","product","asdad","cash","2025-04-09 01:43:20","2025-06-09 21:07:44","1"),
("54","1","3",NULL,NULL,"5","1","24000","0","0","24000","0.00","fully_paid","24000","product","asdasdasdasd","cash","2025-04-09 16:46:29","2025-06-09 21:07:44","1"),
("55","1","3",NULL,NULL,"5","1","100","0","0","100","0.00","fully_paid","100","product","asd","cash","2025-04-09 16:47:10","2025-06-09 21:07:44","1"),
("56","1","3",NULL,NULL,"5","1","3000","0","0","3000","0.00","fully_paid","3000","product","asdasdasd","cash","2025-04-09 20:29:13","2025-06-09 21:07:44","1"),
("57","1","3",NULL,NULL,"5","1","400","0","0","400","0.00","fully_paid","400","product","asdasdas","cash","2025-04-10 05:08:29","2025-06-09 21:07:44","1"),
("58","1","3",NULL,NULL,"5","1","939","0","0","927","12.00","fully_paid","927","product","fafasdassdf","cash","2025-04-10 21:02:11","2025-06-09 21:07:44","1"),
("59","1","2",NULL,NULL,"5","1","20000","0","0","20000","0.00","fully_paid","20000","product","ASDASDASD","cash","2025-04-11 00:38:01","2025-06-09 21:07:44","1"),
("60","1","2",NULL,NULL,"5","1","502","0","0","2","500.00","unpaid","502","product","sdf","cash","2025-04-13 22:18:19","2025-06-09 21:07:44","1"),
("61","1","2",NULL,NULL,"5","1","902","0","0","902","0.00","fully_paid","902","product","asdasdasd","cash","2025-04-16 23:58:19","2025-06-09 21:07:44","1"),
("62","1","106",NULL,NULL,"5","105","903","0","0","903","0.00","fully_paid","903","product","","cash","2025-04-17 01:05:52","2025-06-09 21:07:44","0"),
("63","1","2",NULL,NULL,"5","1","401","0","0","0","401.00","unpaid","100","product","","cash","2025-04-21 20:46:04","2025-06-09 21:07:44","1"),
("64","1","2",NULL,NULL,"5","1","400","0","0","400","0.00","partially_paid","100","product","","cash","2025-04-21 20:46:17","2025-06-09 21:07:44","1"),
("65","1","3",NULL,NULL,"5","1","402","0","0","402","0.00","partially_paid","317","product","asdasd","cash","2025-04-23 02:50:58","2025-06-09 21:07:44","1"),
("66","1","2",NULL,NULL,"5","1","2","0","0","2","0.00","fully_paid","2","product","sdfsdf","cash","2025-04-24 15:11:46","2025-06-09 21:07:44","1"),
("67","1","2",NULL,NULL,"5","1","402","0","0","402","0.00","fully_paid","402","product","fszdfdas","cash","2025-04-24 15:28:34","2025-06-09 21:07:44","1"),
("68","1","2",NULL,NULL,"5","1","400","0","0","400","0.00","fully_paid","400","product","asdsad","cash","2025-04-27 22:57:24","2025-06-09 21:07:44","1"),
("69","1","2",NULL,NULL,"5","1","402","0","0","402","0.00","fully_paid","402","product","","cash","2025-05-03 17:17:44","2025-06-09 21:07:44","1"),
("70","1","3",NULL,NULL,"5","1","2700","0","0","2700","0.00","partially_paid","300","product","","cash","2025-05-19 23:29:30","2025-06-09 21:07:44","1"),
("71","1","3",NULL,NULL,"5","1","402","0","0","402","0.00","partially_paid","0","product","","cash","2025-05-21 19:31:04","2025-06-09 21:07:44","1"),
("72","1","3",NULL,NULL,"5","1","400","0","0","400","0.00","partially_paid","0","product","sdfsdf","cash","2025-05-21 19:31:37","2025-06-09 21:07:44","1"),
("73","1","3",NULL,NULL,"5","1","400","0","0","400","0.00","partially_paid","200","product","sdfsdf","cash","2025-05-21 19:33:26","2025-06-09 21:07:44","1"),
("74","1","2",NULL,NULL,"5","1","502","0","0","502","0.00","partially_paid","0","product","asd","cash","2025-06-08 05:41:40","2025-06-09 21:07:44","1"),
("75","1","2",NULL,NULL,"5","1","505","0","0","505","0.00","partially_paid","0","product","sadasd","cash","2025-06-08 05:42:49","2025-06-09 21:07:44","1"),
("76","1","3",NULL,NULL,"5","1","1","0","0","1","0.00","unpaid","100","product","asd","cash","2025-06-08 05:45:13","2025-06-09 21:07:44","1");



DROP TABLE IF EXISTS orders_items;

CREATE TABLE `orders_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `product_variant_id` (`product_variant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO orders_items VALUES("7","39","17","2","هێلکە","300","0","500","","0","1","[]","150000","5",NULL,NULL,"2025-04-01 00:45:41","2025-04-01 00:45:41"),
("8","40","17","3","هێلکەی سور","3","0","450","","0","1","[]","1350","5",NULL,NULL,"2025-04-01 00:51:02","2025-04-01 00:51:02"),
("9","41","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","7",NULL,NULL,"2025-04-01 01:00:15","2025-04-01 01:00:15"),
("10","42","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","8",NULL,NULL,"2025-04-01 01:02:20","2025-04-01 01:02:20"),
("11","43","17","3","هێلکەی سور","400","0","450","","0","1","[]","180000","8",NULL,NULL,"2025-04-01 01:06:53","2025-04-01 01:06:53"),
("12","44","18","5","مریشک سپی","1","0","6","","0","1","[]","6","8",NULL,NULL,"2025-04-03 00:22:51","2025-04-03 00:22:51"),
("13","44","17","3","هێلکەی سور","1","0","450","","0","1","[]","450","8",NULL,NULL,"2025-04-03 00:22:51","2025-04-03 00:22:51"),
("14","45","18","4","مریشک سوور","1","1","5","","0","1","[]","5","8",NULL,NULL,"2025-04-03 00:24:34","2025-04-09 16:38:32"),
("15","45","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","8",NULL,NULL,"2025-04-03 00:24:34","2025-04-03 00:24:34"),
("16","47","18","4","مریشک سوور","1","1","5","","0","1","[]","5","8","1",NULL,"2025-04-03 17:12:33","2025-04-09 16:37:45"),
("17","47","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","8","1",NULL,"2025-04-03 17:12:34","2025-04-03 17:14:36"),
("18","48","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","8",NULL,NULL,"2025-04-05 02:55:21","2025-04-05 02:55:21"),
("19","48","18","4","مریشک سوور","1","0","5","","0","1","[]","5","8",NULL,NULL,"2025-04-05 02:55:21","2025-04-05 02:55:21"),
("20","49","18","4","مریشک سوور","4","0","5","","0","1","[]","20","5",NULL,NULL,"2025-04-06 04:34:06","2025-04-06 04:34:06"),
("21","50","18","4","مریشک سوور","290","290","5","","0","1","[]","1450","5",NULL,NULL,"2025-04-06 04:51:49","2025-04-09 01:25:13"),
("22","51","17","2","هێلکەی سپی","50","10","500","","0","1","[]","25000","5",NULL,NULL,"2025-04-09 01:26:05","2025-04-09 01:42:22"),
("23","52","19","6","سلاو","50","7","2","","0","1","[]","100","5",NULL,NULL,"2025-04-09 01:37:09","2025-04-09 16:36:28"),
("24","53","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","5",NULL,NULL,"2025-04-09 01:43:20","2025-04-09 01:43:20"),
("25","54","20","7","asdasd","60","0","400","","0","1","[]","24000","5",NULL,NULL,"2025-04-09 16:46:29","2025-04-09 16:46:29"),
("26","55","18","4","مریشک سوور","20","0","5","","0","1","[]","100","5",NULL,NULL,"2025-04-09 16:47:10","2025-04-09 16:47:10"),
("27","56","18","4","مریشک سوور","600","0","5","","0","1","[]","3000","5",NULL,NULL,"2025-04-09 20:29:13","2025-04-09 20:29:13"),
("28","57","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-04-10 05:08:30","2025-04-10 05:08:30"),
("29","58","18","5","مریشک سپی","5","2","6","","0","1","[]","30","5",NULL,NULL,"2025-04-10 21:02:11","2025-04-10 21:04:24"),
("30","58","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-04-10 21:02:12","2025-04-10 21:02:12"),
("31","58","19","6","سلاو","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-04-10 21:02:12","2025-04-10 21:02:12"),
("32","58","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","5",NULL,NULL,"2025-04-10 21:02:12","2025-04-10 21:02:12"),
("33","58","21","9","test2","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-04-10 21:02:12","2025-04-10 21:02:12"),
("34","58","18","4","مریشک سوور","1","0","5","","0","1","[]","5","5",NULL,NULL,"2025-04-10 21:02:12","2025-04-10 21:02:12"),
("35","59","17","2","هێلکەی سپی","40","0","500","","0","1","[]","20000","5",NULL,NULL,"2025-04-11 00:38:01","2025-04-11 00:38:01"),
("36","60","17","2","هێلکەی سپی","1","1","500","","0","1","[]","500","5",NULL,NULL,"2025-04-13 22:18:19","2025-04-16 23:56:40"),
("37","60","19","6","سلاو","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-04-13 22:18:19","2025-04-13 22:18:19"),
("38","61","19","6","سلاو","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-04-16 23:58:19","2025-04-16 23:58:19"),
("39","61","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-04-16 23:58:19","2025-04-16 23:58:19"),
("40","61","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","5",NULL,NULL,"2025-04-16 23:58:19","2025-04-16 23:58:19"),
("41","62","19","6","سلاو","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-04-17 01:05:52","2025-04-17 01:05:52"),
("42","62","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-04-17 01:05:52","2025-04-17 01:05:52"),
("43","62","21","8","test1","1","0","1","","0","1","[]","1","5",NULL,NULL,"2025-04-17 01:05:52","2025-04-17 01:05:52"),
("44","62","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","5",NULL,NULL,"2025-04-17 01:05:52","2025-04-17 01:05:52"),
("45","63","21","8","test1","1","1","1","","0","1","[]","1","5",NULL,NULL,"2025-04-21 20:46:04","2025-04-23 23:33:59"),
("46","63","20","7","asdasd","1","1","400","","0","1","[]","400","5",NULL,NULL,"2025-04-21 20:46:04","2025-04-23 23:33:59"),
("47","64","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-04-21 20:46:17","2025-04-21 20:46:17"),
("48","65","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-04-23 02:50:58","2025-04-23 02:50:58"),
("49","65","19","6","سلاو","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-04-23 02:50:58","2025-04-23 02:50:58"),
("50","66","21","9","test2","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-04-24 15:11:46","2025-04-24 15:11:46"),
("51","67","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-04-24 15:28:34","2025-04-24 15:28:34"),
("52","67","19","6","سلاو","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-04-24 15:28:34","2025-04-24 15:28:34"),
("53","68","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-04-27 22:57:24","2025-04-27 22:57:24"),
("54","69","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-05-03 17:17:44","2025-05-03 17:17:44"),
("55","69","19","6","سلاو","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-05-03 17:17:44","2025-05-03 17:17:44"),
("56","70","17","3","هێلکەی سور","6","0","450","","0","1","[]","2700","5",NULL,NULL,"2025-05-19 23:29:30","2025-05-19 23:29:30"),
("57","71","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-05-21 19:31:04","2025-05-21 19:31:04"),
("58","71","19","6","سلاو","1","0","2","","0","1","[]","2","5",NULL,NULL,"2025-05-21 19:31:04","2025-05-21 19:31:04"),
("59","72","22","10","مریشکی گورزە","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-05-21 19:31:37","2025-05-21 19:31:37"),
("60","73","20","7","asdasd","1","0","400","","0","1","[]","400","5",NULL,NULL,"2025-05-21 19:33:26","2025-05-21 19:33:26"),
("61","74","21","8","test1","2","0","1","","0","1","[]","2","5",NULL,NULL,"2025-06-08 05:41:40","2025-06-08 05:41:40"),
("62","74","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","5",NULL,NULL,"2025-06-08 05:41:40","2025-06-08 05:41:40"),
("63","75","18","4","مریشک سوور","1","0","5","","0","1","[]","5","5",NULL,NULL,"2025-06-08 05:42:49","2025-06-08 05:42:49"),
("64","75","17","2","هێلکەی سپی","1","0","500","","0","1","[]","500","5",NULL,NULL,"2025-06-08 05:42:49","2025-06-08 05:42:49"),
("65","76","21","8","test1","1","0","1","","0","1","[]","1","5",NULL,NULL,"2025-06-08 05:45:13","2025-06-08 05:45:13");



DROP TABLE IF EXISTS orders_services;

CREATE TABLE `orders_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO orders_services VALUES("4","46","4","ئیشتیراکی مانگانە","5","1","gram","2","5","","0","1","[]","1","-30","2025-05-03 00:00:00","2025-06-02 00:00:00",NULL,"8","2025-04-03 01:06:05","2025-04-03 01:06:05");



DROP TABLE IF EXISTS packages;

CREATE TABLE `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `no_of_businesses` int(11) NOT NULL,
  `no_of_delivery_boys` int(11) NOT NULL,
  `no_of_products` int(11) NOT NULL,
  `no_of_customers` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS packages_tenures;

CREATE TABLE `packages_tenures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `tenure` varchar(64) NOT NULL,
  `months` varchar(32) NOT NULL,
  `price` double DEFAULT NULL,
  `discounted_price` double DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS payments;

CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `converted_iqd` double NOT NULL,
  `rate_at_payment` double NOT NULL,
  `payment_type` varchar(64) NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS positions;

CREATE TABLE `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO positions VALUES("1","asdasd","5","asd",NULL,NULL);



DROP TABLE IF EXISTS products;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `business_id` (`business_id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `tax_id` (`tax_ids`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products VALUES("17","1",NULL,"5","1","[]","هێلکە","اسداسداسد","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg","479","1","2025-04-01 00:38:42","2025-06-08 05:42:49"),
("18","1","1","5","1","[]","مریشک","mrishk","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg","20","1","2025-04-03 00:21:54","2025-04-10 05:57:25"),
("19","1",NULL,"5","1","[]","سلاو","اسداسداسداسد","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg","0","1","2025-04-09 01:35:25","2025-05-21 19:31:04"),
("20","1",NULL,"5","1","[]","شیر","اسداسداسداس","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg","881","1","2025-04-09 16:45:50","2025-05-21 19:33:26"),
("21","1",NULL,"5","1","[]","test","adsadas","public/uploads/products/457624148_899136962237390_8858762611595115315_n.jpg","0","1","2025-04-10 06:00:00","2025-04-10 06:00:28"),
("22","14","1","5","1","[]","مریشکی گورزە","دفڤسجسدف","public/uploads/products/344751598_198947899627155_3579641464036871083_n.jpg","40","1","2025-05-21 19:20:40","2025-05-21 19:20:40"),
("23","1","1","5","1",NULL,"dell","aedasd","public/uploads/products/0f2c91febe39793fac9fccd3837b9119_1.jpg","0","1","2025-06-08 20:53:45","2025-06-08 20:53:45"),
("24","1","1","5","1",NULL,"dell","aedasd","public/uploads/products/0f2c91febe39793fac9fccd3837b9119_2.jpg","0","1","2025-06-08 20:53:45","2025-06-08 20:53:45"),
("25","1","1","5","1",NULL,"HP","asdasd","public/uploads/products/8eded34f1cdf0f4d5fb23a4622e2cf33.jpg","0","1","2025-06-08 20:54:38","2025-06-08 20:54:38"),
("26","1","1","5","1",NULL,"HP","asdasd","public/uploads/products/8eded34f1cdf0f4d5fb23a4622e2cf33_1.jpg","0","1","2025-06-08 20:54:38","2025-06-08 20:54:38"),
("27","1","1","5","1",NULL,"Lenovo","asd","public/uploads/products/20f769f1d0cc871a275d386331525256_2.jpg","0","1","2025-06-08 20:57:07","2025-06-08 20:57:07"),
("28","1","1","5","1",NULL,"چا","اسداسد","public/uploads/products/312367664_132130252949354_4616343409897773976_n.jpg","0","1","2025-06-14 03:39:33","2025-06-14 03:39:33"),
("29","1","1","5","1",NULL,"لۆکە","سدفاد","public/uploads/products/311519710_10221927455160784_8720504819187467067_n.jpg","0","1","2025-06-14 05:39:34","2025-06-14 05:39:34");



DROP TABLE IF EXISTS products_variants;

CREATE TABLE `products_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `variant_name` varchar(512) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `qty_alert` varchar(256) NOT NULL,
  `expiry_alert_days` int(11) DEFAULT NULL COMMENT 'Number of days before expiry to send alert',
  `unit_id` int(11) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `barcode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products_variants VALUES("2","17","هێلکەی سپی","10","10",NULL,"0","1","2025-04-01 00:38:42","2025-04-09 01:42:44","123123"),
("3","17","هێلکەی سور","5","10",NULL,"0","1","2025-04-01 00:38:42","2025-04-10 05:56:33","1231234"),
("4","18","مریشک سوور","19","50",NULL,"5","1","2025-04-03 00:21:54","2025-06-08 05:42:49","6435632"),
("5","18","مریشک سپی","36","50",NULL,"5","1","2025-04-03 00:21:54","2025-04-10 21:04:24","535625"),
("6","19","سلاو","0","0","16","0","1","2025-04-09 01:35:25","2025-06-14 14:41:25","3123123"),
("7","20","asdasd","0","0",NULL,"0","1","2025-04-09 16:45:50","2025-04-09 16:45:50","1232213123"),
("8","21","test1","32","10",NULL,"1","1","2025-04-10 06:00:00","2025-06-08 05:45:13","12354673"),
("9","21","test2","58","10","14","13","1","2025-04-10 06:00:00","2025-06-14 14:37:58","6756353"),
("10","22","مریشکی گورزە","50","10",NULL,"3","1","2025-05-21 19:20:40","2025-06-08 05:47:35","65734126"),
("11","23","dell latitude","151","50","1","1","1","2025-06-08 20:53:45","2025-06-14 14:15:40",NULL),
("12","24","dell latitude","200","50","14","1","1","2025-06-08 20:53:45","2025-06-15 06:01:42",NULL),
("13","25","HP 30","0","50","16","1","1","2025-06-08 20:54:38","2025-06-14 14:42:49",NULL),
("14","25","HP 40","0","50",NULL,"1","1","2025-06-08 20:54:38","2025-06-08 20:54:38",NULL),
("15","26","HP 30","0","50",NULL,"1","1","2025-06-08 20:54:38","2025-06-08 20:54:38",NULL),
("16","26","HP 40","0","50",NULL,"1","1","2025-06-08 20:54:38","2025-06-08 20:54:38",NULL),
("17","27","Lenovo 100","141","50","16","1","1","2025-06-08 20:57:07","2025-06-14 14:45:55",NULL),
("18","27","Lenovo 200","300","50","4","1","1","2025-06-08 21:03:43","2025-06-15 06:02:42",NULL),
("19","28","چای گەرم","401","100","1","3","1","2025-06-14 03:39:33","2025-06-14 14:16:52",NULL),
("20","29","لۆکە سور","201","100","5","1","1","2025-06-14 05:39:34","2025-06-15 05:37:24",NULL);



DROP TABLE IF EXISTS purchases;

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `warehouse_id` int(11) unsigned DEFAULT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO purchases VALUES("3","5","1","108","2","1","2025-04-01","[]","5","0","order","40000","cash","fully_paid","40000","sadds","0","2025-04-01 03:18:30","2025-06-09 21:07:44"),
("4","5","1","108","2","","2025-04-01","[]","5","0","order","30000","cash","fully_paid","30000","","0","2025-04-01 03:39:46","2025-06-09 21:07:44"),
("5","5","1","108","2","56","2025-04-03","[]","8","0","order","200","cash","partially_paid","100","fsdfs","0","2025-04-03 02:57:47","2025-06-09 21:07:44"),
("6","5","1","108","2","","2025-04-06","[]","5","0","order","600","cash","fully_paid","600","","0","2025-04-06 07:22:23","2025-06-09 21:07:44"),
("7","5","1","108","2","","2025-04-09","[]","5","0","order","500","cash","fully_paid","500","asdsadasdasd","0","2025-04-09 04:06:05","2025-06-09 21:07:44"),
("8","5","1","108","2","","2025-04-09","[]","5","0","order","150000","cash","fully_paid","150000","asdasdad","0","2025-04-09 19:18:58","2025-06-09 21:07:44"),
("9","5","1","108","2","","2025-04-10","[]","5","0","order","15000","cash","fully_paid","15000","asdasd","0","2025-04-10 08:24:59","2025-06-09 21:07:44"),
("10","5","1","108","2","","2025-05-04","[]","5","0","return","8","cash","fully_paid","8","sdfsdf","0","2025-05-04 06:03:38","2025-06-09 21:07:44"),
("11","5","1","108","2","235","2025-05-19","[]","5","0","order","15000","cash","fully_paid","15000","sfsdf","0","2025-05-20 02:10:32","2025-06-09 21:07:44"),
("12","5","1","108","2","","2025-06-08","[]","7","0","order","288","cash","fully_paid","288","asd","12","2025-06-08 08:17:35","2025-06-09 21:07:44"),
("13","5","1","108","2","","2025-06-08",NULL,"5","0","order","300",NULL,"fully_paid","300","asd","0","2025-06-08 23:28:00","2025-06-09 21:07:44"),
("14","5","1","108","2","","2025-06-08",NULL,"5","0","order","17500",NULL,"fully_paid","14500","asddas","0","2025-06-08 23:28:35","2025-06-09 21:07:44"),
("15","5","1","108","2","","2025-06-09",NULL,"5","0","order","1300",NULL,"fully_paid","1300","asd","0","2025-06-09 07:19:08","2025-06-09 21:07:44"),
("16","5","1","108","2","","2025-06-09",NULL,"5","0","order","10000",NULL,"fully_paid","10000","asd","0","2025-06-09 08:09:12","2025-06-09 21:07:44"),
("17","5","1","108","2","","2025-06-14",NULL,"5","0","order","200",NULL,"fully_paid","200","asdasd","0","2025-06-14 06:10:20","2025-06-14 03:40:20"),
("18","5","1","108","2","","2025-06-14",NULL,"9","0","order","200",NULL,"fully_paid","200","asd","0","2025-06-14 06:12:15","2025-06-14 03:42:15"),
("19","5","1","108","2","","2025-06-14",NULL,"6","0","order","1",NULL,"fully_paid","1","asd","0","2025-06-14 06:12:59","2025-06-14 03:42:59"),
("20","5","1","108","2","","2025-06-14",NULL,"5","0","order","150",NULL,"fully_paid","150","sdf","0","2025-06-14 07:34:03","2025-06-14 05:04:03"),
("21","5","1","108","2","","2025-06-14",NULL,"5","0","order","200",NULL,"fully_paid","200","asd","0","2025-06-14 16:49:26","2025-06-14 14:19:26"),
("22","5","1","108","2","","2025-06-14",NULL,"6","0","order","200",NULL,"fully_paid","200","","0","2025-06-14 17:13:54","2025-06-14 14:43:54"),
("23","5","1","108","2","","2025-06-15",NULL,"5","0","order","1",NULL,"fully_paid","1","asd","0","2025-06-15 08:07:24","2025-06-15 05:37:24"),
("24","5","1","108","2","","2025-06-15",NULL,"5","0","order","200",NULL,"fully_paid","200","asd","0","2025-06-15 08:31:42","2025-06-15 06:01:42"),
("25","5","1","108","2","","2025-06-15",NULL,"6","0","order","1000",NULL,"fully_paid","1000","asd","0","2025-06-15 08:32:42","2025-06-15 06:02:42");



DROP TABLE IF EXISTS purchases_items;

CREATE TABLE `purchases_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  `discount` float NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO purchases_items VALUES("3","3","2","100","400","0","5","2025-04-01 03:18:30","2025-06-09 21:16:43"),
("4","4","3","300","100","0","5","2025-04-01 03:39:46","2025-06-09 21:16:43"),
("5","5","5","50","4","0","8","2025-04-03 02:57:47","2025-06-09 21:16:43"),
("6","6","4","200","3","0","5","2025-04-06 07:22:23","2025-06-09 21:16:43"),
("7","7","6","500","0","0","5","2025-04-09 04:06:05","2025-04-09 01:36:05"),
("8","8","7","500","300","0","5","2025-04-09 19:18:58","2025-06-09 21:16:43"),
("9","9","7","50","300","0","5","2025-04-10 08:24:59","2025-06-09 21:16:43"),
("10","10","8","4","2","0","5","2025-05-04 06:03:38","2025-06-09 21:16:43"),
("11","11","7","50","300","0","5","2025-05-20 02:10:32","2025-06-09 21:16:43"),
("12","12","10","1","300","0","7","2025-06-08 08:17:35","2025-06-09 21:16:43"),
("13","13","17","1","300","0","5","2025-06-08 23:28:00","2025-06-09 21:16:43"),
("14","14","17","50","350","0","5","2025-06-08 23:28:35","2025-06-09 21:16:43"),
("19","15","11","1","300","0","5","2025-06-09 07:19:08","2025-06-09 21:16:43"),
("20","15","3","100","10","0","5","2025-06-09 07:19:08","2025-06-09 21:16:43"),
("21","16","18","100","100","0","5","2025-06-09 08:09:12","2025-06-09 21:16:43"),
("22","17","19","200","1","0","5","2025-06-14 06:10:20","2025-06-14 03:40:20"),
("23","18","19","200","1","0","9","2025-06-14 06:12:15","2025-06-14 03:42:15"),
("24","19","19","1","1","0","6","2025-06-14 06:12:59","2025-06-14 03:42:59"),
("25","20","11","150","1","0","5","2025-06-14 07:34:03","2025-06-14 05:04:03"),
("26","21","20","200","1","0","5","2025-06-14 16:49:26","2025-06-14 14:19:26"),
("27","22","17","100","2","0","6","2025-06-14 17:13:54","2025-06-14 14:43:54"),
("28","23","20","1","1","0","5","2025-06-15 08:07:24","2025-06-15 05:37:24"),
("29","24","12","200","1","0","5","2025-06-15 08:31:42","2025-06-15 06:01:42"),
("30","25","18","200","5","0","6","2025-06-15 08:32:42","2025-06-15 06:02:42");



DROP TABLE IF EXISTS services;

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO services VALUES("4","1","5","[]","2","ئیشتیراکی مانگانە","بۆ ئەو کەسانە حەز ئەکەن مانگانە پارە بەن","public/uploads/services/fc68c7853d0b5ecd72df224a7fe57071.jpg","5","0","1","1","30","30","1","2025-04-03 01:02:43","2025-04-03 01:02:43");



DROP TABLE IF EXISTS settings;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` varchar(128) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO settings VALUES("2","general","{\"title\":\"UpBiz\",\"support_email\":\"admin@admin.com\",\"currency_symbol\":\"\\u062f.\\u0639\",\"currency_locate\":\"left\",\"date_format\":\"m\\/d\\/y H:i A\",\"time_format\":null,\"decimal_points\":\"\",\"mysql_timezone\":\"\",\"select_time_zone\":\"Select Timezone\",\"phone\":\"9089789098\",\"primary_color\":\"#000000\",\"secondary_color\":\"#000000\",\"primary_shadow\":\"#fcf6f5\",\"address\":\"Address\",\"short_description\":\"Short description\",\"support_hours\":\"Support hours\",\"logo\":\"\\/public\\/uploads\\/1749923935_a9b8aa183be978d3ac5b.png\",\"half_logo\":\".\\/public\\/uploads\\/favicon-128.png\",\"favicon\":\"\\/public\\/uploads\\/favicon-128_1.png\",\"copyright_details\":\"<span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\">\\u00a9 Copyright 2022\\u00a0<\\/span><span style=\\\"font-weight: bolder; color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\">UpBiz.<\\/span><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"> All Rights Reserved<\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span>\"}"),
("36","about_us","{\"about_us\":\"<h4><span style=\\\"color: #000000;\\\">UPBiz - A platform for transforming your conventional business into digital.<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Inventory, Accounting, Invoicing Software. <\\/span><span style=\\\"color: #000000;\\\">UpBiz provides features for strong order management, it helps you manage everything from product management to order management to track every transaction. Upbiz offers a system of multiple roles for users. <\\/span><span style=\\\"color: #000000;\\\">With UpBiz businessmen can easily manage inventory and subscriptions with the help of its prominent features.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Grow your business rapidly as it will reduce your paperwork. <\\/span><span style=\\\"color: #000000;\\\">Straightforward solution for companies that include subscription services as well as products, and stock management, now easily managed at this platform.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Inventory :&nbsp;<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Track Complete inventory with Upbiz such as product type, stock, units, variants, and all other details by listing them. it makes sure that all the items being ordered in the store remain available when required.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Accounting:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">With Upbiz record all the transactions of orders whether its fully paid or partially paid. Further maximizing sales its suitable for growing businesses that need to keep their accounting in check. By recording the expenses, Moreover, it will help save time creating business reports.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Invoicing Software:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\"><strong>UpBiz<\\/strong> is an excellent addition to your business as it helps you automate your billing requirements, including GST return filing, inventory management, invoicing, and billing.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">We are talking about improving the life of a segment that is the largest in our nation, i.e &lsquo;Small Business Sector&rsquo; the heartbeat of our economy. One major aspect holding down the small and medium enterprise (SME) sector is that they hardly have any access to proper technology. Easing this situation will go a long way in nurturing and sustaining SMEs. To let India emerge as one of the brightest economic spots in the coming years, businesses should focus on ways to make cash rather than getting stuck up in counting cash.It basically helps them do business accounting easier with the modern digital way!<\\/span><\\/p>\\r\\n<p>&nbsp;<\\/p>\"}"),
("37","refund_policy","{\"refund_policy\":\"<h4>The following terms are applicable for any products that You purchased with Us.<\\/h4>\\r\\n<p>First of all, we thank you and appreciate your service or product purchase with us on our Website upbiz.taskhub.company. Please read this policy carefully as it will give you important information and guidelines about your rights and obligations as our customer, with respect to any purchase or service we provide to you.<\\/p>\\r\\n<p>At upBiz, we take pride in the services delivered by us and guarantee your satisfaction with our services and support. We constantly improve and strive to deliver the best accounting, financial or secretarial services through the internet. We make every effort to provide the service to you as per the specifications and timelines mentioned against each service or product purchased by you from upBiz, however, if, due for any reason, we are unable to provide to you the service or product you purchased from us, please contact us immediately and we will correct the situation, provide a refund or offer credit that can be used for future upBiz orders.<\\/p>\\r\\n<h4>You shall be entitled to a refund which shall be subject to the following situations:<\\/h4>\\r\\n<p>The Refund shall be only considered in the event there is a clear, visible deficiency with the service or product purchased from upBiz. No refund shall be issued if upBiz processed the registration\\/application as per the government guidelines and registration is pending on part of a government department or officials. If any government fee, duty, challan, or any other sum is paid in the course of processing your registration application. We will refund the full payment less the government fee paid. (Don&rsquo;t worry no government fee shall be deducted until Government challan or any other payment proof is provided to you)<\\/p>\\r\\n<p>In the event a customer has paid for a service and then requests for a refund only because there was a change of mind, the refund shall not be considered as there is no fault, defect, or onus on upBiz. Refund requests shall not be entertained after the work has been shared with you in the event of a change of mind. However, we shall give you the option of using the amount paid for by you, for an alternative service in upBiz amounting to the same value and the said amount could be applied in part or whole towards the said new service; and If the request for a refund has been raised 30 (thirty) days after the purchase of a service or product has been completed and the same has been intimated and indicated via email or through any form of communication stating that the work has been completed, then, such refund request shall be deemed invalid and shall not be considered.<\\/p>\\r\\n<p>If the request for the refund has been approved by upBiz, the same shall be processed and intimated to you via email. This refund process could take a minimum of 15 (fifteen) business days to process and shall be credited to your bank account accordingly. We shall handle the refund process with care and ensure that the money spent by you is returned to you at the earliest.<\\/p>\\r\\n<h4>Fees for Services<\\/h4>\\r\\n<p>When the payment of fee is made to upBiz, the fees paid in advance is retained by upBiz in a client account. upBiz will earn the fees upon working on a client&rsquo;s matter. During an engagement, upBiz earns fee at different rates and different times depending on the completion of various milestones (e.g. providing client portal access, assigning relationship manager, obtaining DIN, Filing of forms, etc.,). Refund cannot be provided for the earned fee because resources and man-hours spent on delivering the service are non-returnable in nature. Further, we can&rsquo;t refund or credit any money paid to government entities, such as filing fees or taxes, or to other third parties with a role in processing your order. Under any circumstance, upBiz shall be liable to refund only up to the fee paid by the client.<\\/p>\\r\\n<h4>Change of Service<\\/h4>\\r\\n<p>If you want to change the service you ordered for a different one, you must request this change of service within 30 days of purchase. The purchase price of the original service, less any earned fee and money paid to government entities, such as filing fees or taxes, or to other third parties with a role in processing your order, will be credited to your upBiz account. You can use the balance credit for any other upBiz service.<\\/p>\\r\\n<h4>Standard Pricing<\\/h4>\\r\\n<p>upBiz has a standard pricing policy wherein no additional service fee is requested under any circumstance. However, the standard pricing policy is not applicable for an increase in the total fee paid by the client to upBiz due to an increase in the government fee or fee incurred by the client for completion of legal documentation or re-filing of forms with the government due to rejection or resubmission. upBiz is not responsible or liable for any other cost incurred by the client related to the completion of the service.<\\/p>\\r\\n<h4>Factors outside our Control<\\/h4>\\r\\n<p>We cannot guarantee the results or outcome of your particular procedure. For instance, the government may reject a trademark application for legal reasons beyond the scope of upBiz service. In some cases, a government backlog or problems with the government platforms (e.g. MCA website, Income Tax website, FSSAI website) can lead to long delays before your process is complete. Similarly, upBiz does not guarantee the results or outcomes of the services rendered by our Associates on a Nearest Expert platform, who are not employed by upBiz. Problems like these are beyond our control and are not covered by this guarantee or eligible for a refund. Hence, the delay in processing your file by the Government cannot be a reason for the refund.<\\/p>\\r\\n<h4>Force Majeure<\\/h4>\\r\\n<p>upBiz shall not be considered in breach of its Satisfaction Guarantee policy or default under any terms of service, and shall not be liable to the Client for any cessation, interruption, or delay in the performance of its obligations by reason of earthquake, flood, fire, storm, lightning, drought, landslide, hurricane, cyclone, typhoon, tornado, natural disaster, act of God or the public enemy, epidemic, famine or plague, action of a court or public authority, change in law, explosion, war, terrorism, armed conflict, labor strike, lockout, boycott or similar event beyond our reasonable control, whether foreseen or unforeseen (each a &ldquo;Force Majeure Event&rdquo;).<\\/p>\\r\\n<h4>Cancellation Fee<\\/h4>\\r\\n<p>Since we&rsquo;re incurring costs and dedicating time, manpower, technology resources, and effort to your service or document preparation, our guarantee only covers satisfaction issues caused by upBiz &ndash; not changes to your situation or your state of mind. In case you require us to hold the processing of service, we will hold the fee paid on your account until you are ready to commence the service.<\\/p>\\r\\n<p>Before processing any refund, we reserve the right to make the best effort to complete the service. In case, you are not satisfied with the service, a cancellation fee of 20% + earned fee + fee paid to the government would be applicable. In case of a change of service, the cancellation fee would not be applicable.<\\/p>\"}"),
("42","payment_gateway","{\"razorpay_payment_mode\":\"Test\",\"razorpay_secret_key\":\"Y0mPvWDwSEVqGo7WhOqDuRrF\",\"razorpay_api_key\":\"rzp_test_yUGY97WyLX7BwZ\",\"razorpay_status\":\"1\",\"stripe_payment_mode\":\"Test\",\"stripe_currency_symbol\":\"INR\",\"stripe_publishable_key\":\"sk_test_51JgnbISHhf5LKO0I0wNtrjf4Hc3pbHjUDJFFQvKgi7ga1I3jgbhJ53bTc3fPMb6qOleEWw66a7XYPo0fevZKGHR900RZc6mkEM\",\"stripe_secret_key\":\"pk_test_51JgnbISHhf5LKO0IQdzXM2b4iZAizrgwaNFfLiQYkq9XdfYQLOw5HQGbOxT4MJAtSjDdOVgYzQ1djB3LEdSSt6AA001sjpQvvN\",\"stripe_webhook_secret_key\":\"Stripe Webhook Secret Key\",\"stripe_status\":\"1\",\"flutterwave_payment_mode\":\"Test\",\"flutterwave_currency_symbol\":\"NGN\",\"flutterwave_public_key\":\"FLWPUBK_TEST-1ffbaed6ee3788cd2bcbb898d3b90c59-X\",\"flutterwave_secret_key\":\"FLWSECK_TEST-c659ffd76304fff90fc4b67ae735b126-X\",\"flutterwave_encryption_key\":\"FLWSECK_TEST25c36edcfcaa\",\"flutterwave_status\":\"1\"}"),
("43","email","{\"email\":\"your_smtp@email.com\",\"password\":\"your password\",\"smtp_host\":\"your host\",\"smtp_port\":\"465\",\"mail_content_type\":\"html\",\"smtp_encryption\":\"ssl\"}"),
("44","terms_and_conditions","{\"terms_and_conditions\":\"<p><span style=\\\"color: #000000;\\\">Welcome to <strong>Upbiz!<\\/strong><\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">These terms and conditions outline the rules and regulations for the use of upbiz\'s Website, located at https:\\/\\/upbiz.taskhub.company\\/.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By accessing this website we assume you accept these terms and conditions. Do not continue to use Upbiz if you do not agree to take all of the terms and conditions stated on this page.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: \\\"Client\\\", \\\"You\\\" and \\\"Your\\\" refers to you, the person log on this website and compliant to the Company&rsquo;s terms and conditions. \\\"The Company\\\", \\\"Ourselves\\\", \\\"We\\\", \\\"Our\\\" and \\\"Us\\\", refers to our Company. \\\"Party\\\", \\\"Parties\\\", or \\\"Us\\\", refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client&rsquo;s needs in respect of provision of the Company&rsquo;s stated services, in accordance with and subject to, prevailing law of India. Any use of the above terminology or other words in the singular, plural, capitalization and\\/or he\\/she or they, are taken as interchangeable and therefore as referring to same.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Cookies<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We employ the use of cookies. By accessing Upbiz , you agreed to use cookies in agreement with the upbiz\'s Privacy Policy.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Most interactive websites use cookies to let us retrieve the user&rsquo;s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate\\/advertising partners may also use cookies.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">License<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Unless otherwise stated, upbiz and\\/or its licensors own the intellectual property rights for all material on Upbiz . All intellectual property rights are reserved. You may access this from Upbiz for your own personal use subjected to restrictions set in these terms and conditions.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">You must not:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Republish material from Upbiz<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Sell, rent or sub-license material from Upbiz<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Reproduce, duplicate or copy material from Upbiz<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Redistribute content from Upbiz<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">This Agreement shall begin on the date hereof.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. upbiz does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of upbiz,its agents and\\/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, upbiz shall not be liable for the Comments or for any liability, damages or expenses caused and\\/or suffered as a result of any use of and\\/or posting of and\\/or appearance of the Comments on this website.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">upbiz reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">You warrant and represent that:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">You hereby grant upbiz a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Hyperlinking to our Content<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">The following organizations may link to our Website without prior written approval:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Government agencies;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Search engines;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">News organizations;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and\\/or services; and (c) fits within the context of the linking party&rsquo;s site.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">We may consider and approve other link requests from the following types of organizations:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">commonly-known consumer and\\/or business information sources;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">dot.com community sites;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">associations or other groups representing charities;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">online directory distributors;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">internet portals;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">accounting, law and consulting firms; and<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">educational institutions and trade associations.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of upbiz; and (d) the link is in the context of general resource information.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party&rsquo;s site.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to upbiz.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Approved organizations may hyperlink to our Website as follows:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By use of our corporate name; or<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By use of the uniform resource locator being linked to; or<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party&rsquo;s site.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">No use of upbiz\'s logo or other artwork will be allowed for linking absent a trademark license agreement.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">iFrames<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Content Liability<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Your Privacy<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Please read Privacy Policy<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Reservation of Rights<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it&rsquo;s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Removal of links from our website<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Disclaimer<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">limit or exclude our or your liability for death or personal injury;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">limit or exclude our or your liability for fraud or fraudulent misrepresentation;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">limit any of our or your liabilities in any way that is not permitted under applicable law; or<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">exclude any of our or your liabilities that may not be excluded under applicable law.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.<\\/span><\\/p>\"}"),
("45","privacy_policy","{\"privacy_policy\":\"<p><span style=\\\"color: #000000;\\\">At Upbiz , accessible from http:\\/\\/upbiz.taskhub.company\\/, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Upbiz and how we use it. <\\/span><span style=\\\"color: #000000;\\\">If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">This Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and\\/or collect in Upbiz . This policy is not applicable to any information collected offline or via channels other than this website.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Consent<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">By using our website, you hereby consent to our Privacy Policy and agree to its terms.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Information we collect<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">The personal information that you are asked to provide, and the reasons why you are asked to provide it, will be made clear to you at the point we ask you to provide your personal information. <\\/span><span style=\\\"color: #000000;\\\">If you contact us directly, we may receive additional information about you such as your name, email address, phone number, the contents of the message and\\/or attachments you may send us, and any other information you may choose to provide. <\\/span><span style=\\\"color: #000000;\\\">When you register for an Account, we may ask for your contact information, including items such as name, company name, address, email address, and telephone number.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">How we use your information<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We use the information we collect in various ways, including to:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">1. Provide, operate, and maintain our website<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">2. Improve, personalize, and expand our website<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">3. Understand and analyze how you use our website<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">4. Develop new products, services, features, and functionality<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">5. Communicate with you, either directly or through one of our partners, including for customer service, to provide you with updates and other information relating to the website, and for marketing and promotional purposes<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">6. Send you emails<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">7. Find and prevent fraud<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Log Files<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Upbiz follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services\' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring\\/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users\' movement on the website, and gathering demographic information.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Advertising Partners Privacy Policies<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">You may consult this list to find the Privacy Policy for each of the advertising partners of Upbiz .<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on Upbiz , which are sent directly to users\' browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and\\/or to personalize the advertising content that you see on websites that you visit.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Note that Upbiz has no access to or control over these cookies that are used by third-party advertisers.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Third Party Privacy Policies<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Upbiz \'s Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options. <\\/span><span style=\\\"color: #000000;\\\">You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers\' respective websites.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Request that a business that collects a consumer\'s personal data disclose the categories and specific pieces of personal data that a business has collected about consumers. <\\/span><span style=\\\"color: #000000;\\\">Request that a business delete any personal data about the consumer that a business has collected. <\\/span><span style=\\\"color: #000000;\\\">Request that a business that sells a consumer\'s personal data, not sell the consumer\'s personal data.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">GDPR Data Protection Rights -&nbsp;<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We would like to make sure you are fully aware of all of your data protection rights. Every user is entitled to the following:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">1. The right to access &ndash; You have the right to request copies of your personal data. We may charge you a small fee for this service.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">2. The right to rectification &ndash; You have the right to request that we correct any information you believe is inaccurate. You also have the right to request that we complete the information you believe is incomplete.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">3. The right to erasure &ndash; You have the right to request that we erase your personal data, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">4. The right to restrict processing &ndash; You have the right to request that we restrict the processing of your personal data, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">5. The right to object to processing &ndash; You have the right to object to our processing of your personal data, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">6. The right to data portability &ndash; You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">7. If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.<\\/span><\\/p>\"}");



DROP TABLE IF EXISTS status;

CREATE TABLE `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `status` varchar(256) NOT NULL,
  `operation` tinyint(4) NOT NULL COMMENT '0-do nothing | 1-credit | 2-debit\r\n',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `business_id` (`business_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO status VALUES("5","1","5","تەواو ووە","0","2025-04-01 00:44:12","2025-04-01 00:44:12"),
("6","1","5","تەواو ووە","0","2025-04-01 00:45:03","2025-04-01 00:45:03"),
("7","1","5","قەرز","2","2025-04-01 00:59:43","2025-04-01 00:59:43"),
("8","1","5","زیا بوون","1","2025-04-01 01:02:01","2025-04-01 01:02:01"),
("9","1","5","گەڕانەوە","2","2025-04-05 03:00:48","2025-04-05 03:00:48"),
("10","1","5","asd","0","2025-06-08 05:41:30","2025-06-08 05:41:30"),
("11","1","5","sad","0","2025-06-08 05:42:32","2025-06-08 05:42:32"),
("12","1","5","asd","0","2025-06-08 05:45:09","2025-06-08 05:45:09");



DROP TABLE IF EXISTS subscription;

CREATE TABLE `subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL COMMENT 'RENEWABLE SERVICES',
  `customer_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `customer_id` (`customer_id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `business_id` (`business_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO subscription VALUES("2","4","2","1",NULL,"5","2025-04-03 01:06:05","2025-04-03 01:06:05");



DROP TABLE IF EXISTS suppliers;

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO suppliers VALUES("2","108","1","500","asadasd","asdasdsad","400","800","asd","asd","1","2025-04-01 03:17:45","2025-04-01 00:47:45"),
("3","111","1","300","KURDDSTAN","sadsad","12","22","","","1","2025-06-14 17:37:31","2025-06-14 15:07:31");



DROP TABLE IF EXISTS tax;

CREATE TABLE `tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `percentage` float NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS team_members;

CREATE TABLE `team_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `vendor_id` int(11) unsigned NOT NULL,
  `business_ids` text DEFAULT NULL,
  `permissions` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO team_members VALUES("2","104","1","[\"5\"]","{\"\'pos\'\":[\"\'can_create\'\"],\"\'products\'\":[\"\'can_create\'\"]}");



DROP TABLE IF EXISTS transactions;

CREATE TABLE `transactions` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `user_id` int(12) NOT NULL,
  `amount` double NOT NULL,
  `txn_id` varchar(128) NOT NULL,
  `payment_method` varchar(64) NOT NULL,
  `status` varchar(256) NOT NULL,
  `message` varchar(264) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS units;

CREATE TABLE `units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `symbol` varchar(64) NOT NULL,
  `conversion` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO units VALUES("1","0","0","kilogram","kg","0","2022-01-03 10:43:05","2022-01-12 10:38:11"),
("2","0","1","gram","g","1000","2022-01-03 10:44:03","2022-01-12 08:14:13"),
("3","0","0","liter","l","0","2022-01-03 11:05:44","2022-01-03 11:09:05"),
("4","0","3","milliliter","ml","1000","2022-01-03 11:09:44","2022-01-03 11:09:44"),
("5","0","0","pack","pk","0","2022-01-03 11:20:47","2022-01-03 11:28:03"),
("6","0","0","piece","pc","0","2022-01-03 11:28:26","2022-01-03 11:28:26"),
("7","15","0","meter","mm","0","2022-01-03 11:29:35","2022-01-13 07:57:47"),
("8","15","7","millimeter","mm","0","2022-01-03 11:31:01","2022-01-13 12:51:32"),
("9","14","0","foot","ft","0","2022-01-03 14:35:49","2022-01-12 10:48:38"),
("10","0","9","inch","in","12","2022-01-03 14:36:19","2022-01-03 14:37:06"),
("11","0","0","square foot","sqft","0","2022-01-05 13:55:15","2022-01-05 13:55:15"),
("12","0","11","square meter","sqm","0","2022-01-05 13:58:11","2022-01-05 13:58:11"),
("13","0","0","bundles","bdl","0","2022-01-06 12:45:48","2022-01-06 12:45:48"),
("32","0","0","months","m","0","2022-01-13 13:16:35","2022-03-28 14:39:15"),
("33","15","0","hours","h","3600","2022-01-28 09:36:17","2022-01-28 09:36:17"),
("34","15","0","general","g","0","2022-03-30 08:58:26","2022-03-30 08:58:26"),
("35","1","0","دانە","دانە","24","2025-04-01 00:30:25","2025-04-01 00:30:58");



DROP TABLE IF EXISTS updates;

CREATE TABLE `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO updates VALUES("1","1.0"),
("2","1.1"),
("3","1.2"),
("4","1.3"),
("5","1.4"),
("6","1.5"),
("7","1.6"),
("8","1.7"),
("9","1.8"),
("11","1.9");



DROP TABLE IF EXISTS user_permissions;

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `business_id` varchar(255) NOT NULL DEFAULT '1',
  `role` int(11) NOT NULL,
  `permissions` text NOT NULL,
  `created_by` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;




DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
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
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `activation_selector` (`activation_selector`),
  UNIQUE KEY `forgotten_password_selector` (`forgotten_password_selector`),
  UNIQUE KEY `remember_selector` (`remember_selector`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO users VALUES("1","127.0.0.1","mhamadaras10@gmail.com","1234567890","$2y$12$TSY7xOZEZaadKneUgikdEOd.ceM2X.4rl9tmXV1cpyCmX4H.iKbRi","mhamadaras10@gmail.com",NULL,NULL,"",NULL,NULL,NULL,NULL,NULL,"1268889823","1750121264","1","admin","admin","admin",""),
("104","::1","123456789","123456789","$2y$10$v6TVuHv4u.N1ymvJvVT9aema93kf5ICwKF/rvjxAumbejWj3FWWGO","mhamadaras17@gmail.com",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"1743455833","1743458187","1","zanko","ibrahim",NULL,"123456789"),
("105","::1","1234567899","1234567899","$2y$10$.RmCowdjcoEzPW0N6vhTWOknrn1Xh7xt65fTc/pjCbHCzWTWFb11S","mhamadaras13@gmail.com",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"1743456144","1744841060","1","zanko",NULL,NULL,NULL),
("106","::1","2349456184","2349456184","$2y$10$IzZqLeMkURcYc5NzvZsPQem.G5B4CCdgT7m0FOtdbvqBNSRV6g87y","commandermhamad@yahoo.com",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"1743456213",NULL,"1","عەباس",NULL,NULL,NULL),
("107","::1","12345678999","12345678999","$2y$10$TA04Gh5mInkT.d/ajNrjOOnUoJSgEgfhTh323gfM47xxHxtL5wt.y","mhamadaras@gmail.com",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"1743457376",NULL,"1","mhamad",NULL,NULL,NULL),
("108","::1","12223456","12223456","$2y$10$lw7XUnHZhecO5c./csNLPeXzxo6KNaLVxbWpGpN6Y8Uq/hvkUHVCq","mhamad1@gmail.com",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"1743457665",NULL,"1","mhamadd",NULL,NULL,NULL),
("109","::1","11111111","11111111","$2y$10$zZnV0k/rli33i7xp051Kv.acnH5O6MbOsQBjCUWhJEuCAYmI.ANdC","commandermham1ad@yahoo.com",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"1743459036","1743459045","1","mhamadddd",NULL,NULL,NULL),
("110","::1","542324","542324","$2y$10$Bxvy5rvGBp9b6jp.XIbb4.VCKhJpNOazwwPLAaeg8Lza3E.ESAljK","shahram@gmail.com",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"1747752812",NULL,"1","shahram",NULL,NULL,NULL),
("111","::1","45234","45234","$2y$10$j61dPIL4KhmUiEHaIrUu1u4iVXO7mdcfG0Z89j1JEkwk9eVHjqzsS","commandermhamad222@yahoo.com",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"1749902851",NULL,"1","shahram",NULL,NULL,NULL);



DROP TABLE IF EXISTS users_groups;

CREATE TABLE `users_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_groups_user_id_foreign` (`user_id`),
  KEY `users_groups_group_id_foreign` (`group_id`),
  CONSTRAINT `users_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `users_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO users_groups VALUES("1","1","1"),
("81","104","6"),
("82","105","3"),
("83","106","4"),
("84","107","4"),
("85","108","5"),
("86","109","3"),
("87","110","4"),
("88","111","5");



DROP TABLE IF EXISTS users_packages;

CREATE TABLE `users_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS view_product_details;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_product_details` AS select `p`.`id` AS `product_id`,`p`.`name` AS `product_name`,`p`.`description` AS `description`,`p`.`image` AS `image`,`p`.`stock` AS `stock`,`p`.`status` AS `status`,`p`.`category_id` AS `category_id`,`p`.`business_id` AS `business_id`,`categories`.`name` AS `category_name`,`brands`.`name` AS `brand_name`,`u`.`first_name` AS `creator`,`p`.`brand_id` AS `brand_id` from (((`products` `p` left join `brands` on(`brands`.`id` = `p`.`brand_id`)) left join `categories` on(`categories`.`id` = `p`.`category_id`)) left join `users` `u` on(`u`.`id` = `p`.`vendor_id`));

INSERT INTO view_product_details VALUES("17","هێلکە","اسداسداسد","public/uploads/products/0f2c91febe39793fac9fccd3837b9119.jpg","479","1","1","5","general",NULL,"admin",NULL),
("18","مریشک","mrishk","public/uploads/products/20f769f1d0cc871a275d386331525256_1.jpg","20","1","1","5","general","مریشک","admin","1"),
("19","سلاو","اسداسداسداسد","public/uploads/products/a07f39855f8968114740148b235eb15b.jpg","0","1","1","5","general",NULL,"admin",NULL),
("20","شیر","اسداسداسداس","public/uploads/products/fc68c7853d0b5ecd72df224a7fe57071.jpg","881","1","1","5","general",NULL,"admin",NULL),
("21","test","adsadas","public/uploads/products/457624148_899136962237390_8858762611595115315_n.jpg","0","1","1","5","general",NULL,"admin",NULL),
("22","مریشکی گورزە","دفڤسجسدف","public/uploads/products/344751598_198947899627155_3579641464036871083_n.jpg","40","1","14","5","پاکەت","مریشک","admin","1"),
("23","dell","aedasd","public/uploads/products/0f2c91febe39793fac9fccd3837b9119_1.jpg","0","1","1","5","general","مریشک","admin","1"),
("24","dell","aedasd","public/uploads/products/0f2c91febe39793fac9fccd3837b9119_2.jpg","0","1","1","5","general","مریشک","admin","1"),
("25","HP","asdasd","public/uploads/products/8eded34f1cdf0f4d5fb23a4622e2cf33.jpg","0","1","1","5","general","مریشک","admin","1"),
("26","HP","asdasd","public/uploads/products/8eded34f1cdf0f4d5fb23a4622e2cf33_1.jpg","0","1","1","5","general","مریشک","admin","1"),
("27","Lenovo","asd","public/uploads/products/20f769f1d0cc871a275d386331525256_2.jpg","0","1","1","5","general","مریشک","admin","1"),
("28","چا","اسداسد","public/uploads/products/312367664_132130252949354_4616343409897773976_n.jpg","0","1","1","5","general","مریشک","admin","1"),
("29","لۆکە","سدفاد","public/uploads/products/311519710_10221927455160784_8720504819187467067_n.jpg","0","1","1","5","general","مریشک","admin","1");



DROP TABLE IF EXISTS warehouse_batches;

CREATE TABLE `warehouse_batches` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_item_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `warehouse_id` int(10) unsigned NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost_price` float NOT NULL,
  `sell_price` float DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_batch_number` (`batch_number`),
  KEY `fk_business` (`business_id`),
  KEY `fk_product_variant` (`product_variant_id`),
  KEY `fk_purchase_items` (`purchase_item_id`),
  KEY `fk_warehouse` (`warehouse_id`),
  CONSTRAINT `fk_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  CONSTRAINT `fk_product_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `products_variants` (`id`),
  CONSTRAINT `fk_purchase_items` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchases_items` (`id`),
  CONSTRAINT `fk_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO warehouse_batches VALUES("1","13","5","17","2","ORDER-13-1749405480-2025-06-30","1","300","400","2025-06-30","2025-06-08 23:28:00"),
("2","14","5","17","2","ORDER-14-1749405515-2025-06-30","40","350","450","2025-06-27","2025-06-08 23:28:35"),
("7","19","5","11","2","ORDER-19-1749433748-2025-06-30","1","300","400","2025-06-30","2025-06-09 07:19:08"),
("8","20","5","3","2","ORDER-20-1749433748-2025-06-30","100","10","20","2025-06-30","2025-06-09 07:19:08"),
("9","21","5","18","2","ORDER-21-1749436752-2025-06-30","100","100","120","2025-06-30","2025-06-09 08:09:12"),
("10","22","5","19","2","ORDER-22-1749861620-2025-06-30","200","1","2","2025-06-18","2025-06-14 06:10:20"),
("11","23","5","19","2","ORDER-23-1749861735-2025-06-30","200","1","2","2025-06-16","2025-06-14 06:12:15"),
("12","24","5","19","2","ORDER-24-1749861779-2025-06-30","1","1","2","2025-06-30","2025-06-14 06:12:59"),
("13","25","5","11","2","ORDER-25-1749866644-2025-06-14","150","1","2","2025-06-15","2025-06-14 07:34:04"),
("14","26","5","20","2","ORDER-26-1749899966-2025-06-20","200","1","2","2025-06-19","2025-06-14 16:49:26"),
("15","27","5","17","2","ORDER-27-1749901434-2025-06-30","100","2","4","2025-06-30","2025-06-14 17:13:54"),
("16","28","5","20","2","ORDER-28-1749955044-2025-06-30","1","1","2","2025-06-30","2025-06-15 08:07:24"),
("17","29","5","12","2","ORDER-29-1749956502-2025-06-16","200","1","2","2025-06-16","2025-06-15 08:31:42"),
("18","30","5","18","2","ORDER-30-1749956562-2025-06-24","200","5","8","2025-06-24","2025-06-15 08:32:42");



DROP TABLE IF EXISTS warehouse_batches_returns;

CREATE TABLE `warehouse_batches_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_item_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `warehouse_id` int(10) unsigned NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost_price` float NOT NULL,
  `return_price` float DEFAULT NULL,
  `return_reason` text DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `warehouse_batches_returns_business_id_foreign` (`business_id`),
  KEY `warehouse_batches_returns_product_variant_id_foreign` (`product_variant_id`),
  KEY `warehouse_batches_returns_purchase_item_id_foreign` (`purchase_item_id`),
  KEY `warehouse_batches_returns_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `warehouse_batches_returns_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouse_batches_returns_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `products_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouse_batches_returns_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchases_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouse_batches_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO warehouse_batches_returns VALUES("1","14","5","17","2","ORDER-14-1749405515-2025-06-30","10","350","340","sdfsd","2025-06-08","2025-06-08 23:32:28");



DROP TABLE IF EXISTS warehouse_product_stock;

CREATE TABLE `warehouse_product_stock` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) unsigned NOT NULL,
  `product_variant_id` int(11) DEFAULT NULL,
  `stock` double DEFAULT 0,
  `qty_alert` double DEFAULT 0,
  `vendor_id` int(11) unsigned NOT NULL,
  `business_id` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_product_stock_warehouse_id_foreign` (`warehouse_id`),
  KEY `warehouse_product_stock_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `warehouse_product_stock_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `products_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `warehouse_product_stock_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO warehouse_product_stock VALUES("2","2","2","588","100","1","5","2025-04-01 00:38:42","2025-06-08 05:42:49"),
("3","2","3","440","100","1","5","2025-04-01 00:38:42","2025-06-09 04:49:08"),
("4","2","4","381","50","1","5","2025-04-03 00:21:54","2025-06-08 05:42:49"),
("5","2","5","344","50","1","5","2025-04-03 00:21:54","2025-04-10 21:02:12"),
("6","2","6","0","100","1","5","2025-04-09 01:35:25","2025-05-21 19:31:04"),
("7","2","7","880","10","1","5","2025-04-09 16:45:50","2025-05-21 19:33:26"),
("8","2","8","31","10","1","5","2025-04-10 06:00:00","2025-06-08 05:45:13"),
("9","2","9","38","10","1","5","2025-04-10 06:00:00","2025-04-24 15:11:46"),
("10","3","9","20","0","1","5","2025-04-24 15:11:11","2025-04-24 15:11:11"),
("11","2","10","50","10","1","5","2025-05-21 19:20:40","2025-06-08 05:47:35"),
("12","2","17","151",NULL,"1","5","2025-06-08 20:58:00","2025-06-14 14:43:54"),
("13","2","11","151",NULL,"1","5","2025-06-09 04:49:08","2025-06-14 05:04:04"),
("14","2","18","300",NULL,"1","5","2025-06-09 05:39:12","2025-06-15 06:02:42"),
("15","2","19","401",NULL,"1","5","2025-06-14 03:40:20","2025-06-14 03:42:59"),
("16","2","20","201",NULL,"1","5","2025-06-14 14:19:26","2025-06-15 05:37:24"),
("17","2","12","200",NULL,"1","5","2025-06-15 06:01:42","2025-06-15 06:01:42");



DROP TABLE IF EXISTS warehouses;

CREATE TABLE `warehouses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) unsigned NOT NULL,
  `business_id` text DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO warehouses VALUES("1","1","4","Default Warehouse","Default Country","Default City","0000000","Default Warehouse Address"),
("2","1","5","مەخزەن","iraq","slemani","00904","sdfsdf"),
("3","1","5","پێشانگا","Iraq","sulaymany","00904","mamostayan street\nBsbs");



