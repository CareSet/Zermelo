-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Mar 25, 2019 at 02:24 PM
-- Server version: 5.6.38
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `_zermelo2`
--

-- --------------------------------------------------------

--
-- Table structure for table `socket`
--

CREATE TABLE `socket` (
  `id` int(10) UNSIGNED NOT NULL,
  `wrench_id` int(11) NOT NULL,
  `wrench_value` varchar(1024) NOT NULL,
  `wrench_label` varchar(1024) NOT NULL,
  `is_default_socket` tinyint(1) NOT NULL,
  `socketsource_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `socket`
--

INSERT INTO `socket` (`id`, `wrench_id`, `wrench_value`, `wrench_label`, `is_default_socket`, `socketsource_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'customer.jobTitle=\'Purchasing Manager\'', 'Purchasing Manager Only', 1, 1, NULL, NULL),
(2, 1, 'customer.jobTitle=\'Owner\'', 'Owner Only', 0, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socketsource`
--

CREATE TABLE `socketsource` (
  `id` int(10) UNSIGNED NOT NULL,
  `socketsource_name` varchar(1024) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `socketsource`
--

INSERT INTO `socketsource` (`id`, `socketsource_name`, `created_at`, `updated_at`) VALUES
(1, 'Test Import', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `socket_user`
--

CREATE TABLE `socket_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `wrench_id` int(11) NOT NULL,
  `current_chosen_socket_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wrench`
--

CREATE TABLE `wrench` (
  `id` int(10) UNSIGNED NOT NULL,
  `wrench_lookup_string` varchar(1024) NOT NULL,
  `wrench_label` varchar(1024) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wrench`
--

INSERT INTO `wrench` (`id`, `wrench_lookup_string`, `wrench_label`, `created_at`, `updated_at`) VALUES
(1, 'job_title_filter', 'Job Title Filter', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `socket`
--
ALTER TABLE `socket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socketsource`
--
ALTER TABLE `socketsource`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socket_user`
--
ALTER TABLE `socket_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wrench`
--
ALTER TABLE `wrench`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `socket`
--
ALTER TABLE `socket`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `socketsource`
--
ALTER TABLE `socketsource`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `socket_user`
--
ALTER TABLE `socket_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wrench`
--
ALTER TABLE `wrench`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
