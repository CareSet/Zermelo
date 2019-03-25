-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Mar 25, 2019 at 02:24 PM
-- Server version: 5.6.38
-- PHP Version: 7.2.1


--
-- Dumping data for table `socket`
--

INSERT INTO `socket` (`id`, `wrench_id`, `wrench_value`, `wrench_label`, `is_default_socket`, `socketsource_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'customer.jobTitle=\'Purchasing Manager\'', 'Purchasing Manager Only', 1, 1, NULL, NULL),
(2, 1, 'customer.jobTitle=\'Owner\'', 'Owner Only', 0, 1, NULL, NULL);

-- --------------------------------------------------------
--
-- Dumping data for table `socketsource`
--

INSERT INTO `socketsource` (`id`, `socketsource_name`, `created_at`, `updated_at`) VALUES
(1, 'Test Import', NULL, NULL);

-- --------------------------------------------------------
--
-- Dumping data for table `wrench`
--

INSERT INTO `wrench` (`id`, `wrench_lookup_string`, `wrench_label`, `created_at`, `updated_at`) VALUES
(1, 'job_title_filter', 'Job Title Filter', NULL, NULL);

