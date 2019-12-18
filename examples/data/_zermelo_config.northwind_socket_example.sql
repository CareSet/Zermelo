-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 18, 2019 at 04:05 AM
-- Server version: 10.2.22-MariaDB-10.2.22+maria~xenial-log
-- PHP Version: 7.2.15-1+ubuntu16.04.1+deb.sury.org+1

CREATE TABLE IF NOT EXISTS `socket` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wrench_id` int(11) NOT NULL,
  `socket_value` varchar(1024) NOT NULL,
  `socket_label` varchar(1024) NOT NULL,
  `is_default_socket` tinyint(1) NOT NULL,
  `socketsource_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


--
-- Dumping data for table `socket`
--

INSERT INTO `socket` (`id`, `wrench_id`, `socket_value`, `socket_label`, `is_default_socket`, `socketsource_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'customer.jobTitle=\'Purchasing Manager\'', 'Purchasing Manager Only', 0, 1, NULL, NULL),
(2, 1, 'customer.jobTitle=\'Owner\'', 'Owner Only', 0, 1, NULL, NULL),
(3, 1, 'customer.jobTitle IN (\'Purchasing Manager\',\'Owner\')', 'Purchasing Managers or Owners Only', 0, 1, '2019-04-17 05:00:00', '2019-04-17 05:00:00'),
(5, 1, 'CHAR_LENGTH(customer.jobTitle) > 0 ', 'Any Job Title', 1, 1, '2019-04-17 05:00:00', '2019-04-17 05:00:00'),
(6, 2, 'stateProvince IN (\'NY\', \'TX\', \'FL\', \'CA\')', 'Largest States Only', 0, 1, '2019-04-17 05:00:00', '2019-04-17 05:00:00'),
(7, 2, '', 'Any States', 1, 1, '2019-04-17 05:00:00', '2019-04-17 05:00:00'),
(8, 3, 'MyWind_northwind_data.order', 'Only Order Table', 1, 1, '2019-04-17 05:00:00', '2019-04-17 05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `wrench`
--

CREATE TABLE IF NOT EXISTS `wrench` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wrench_lookup_string` varchar(200) NOT NULL,
  `wrench_label` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX(wrench_label),
  UNIQUE INDEX(wrench_lookup_string)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



--
-- Dumping data for table `wrench`
--

INSERT INTO `wrench` (`id`, `wrench_lookup_string`, `wrench_label`, `created_at`, `updated_at`) VALUES
(1, 'MyWind_job_title_filter', 'Job Title Filter', NULL, NULL),
(2, 'MyWind_big_state_filter', 'Limit Report to certain states', '2019-04-17 05:00:00', '2019-04-17 05:00:00'),
(3, 'MyWind_order_year_db_table', 'Limit Report to Order table', '2019-04-17 05:00:00', '2019-04-17 05:00:00');

--
-- Indexes for dumped tables
--

