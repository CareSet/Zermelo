-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 05, 2018 at 04:30 PM
-- Server version: 5.6.38
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zermelo_tags`
--

-- --------------------------------------------------------

--
-- Table structure for table `tags_report`
--

CREATE TABLE `tags_report` (
  `id` bigint(20) NOT NULL,
  `field_to_bold_in_report_display` varchar(255) NOT NULL,
  `field_to_hide_by_default` varchar(255) NOT NULL,
  `field_to_italic_in_report_display` varchar(255) NOT NULL,
  `field_to_right_align_in_report` varchar(255) NOT NULL,
  `field_to_bolditalic_in_report_display` varchar(255) NOT NULL,
  `numeric_field` int(11) NOT NULL,
  `decimal_field` decimal(10,4) NOT NULL,
  `currency_field` varchar(255) NOT NULL,
  `percent_field` int(11) NOT NULL,
  `url_field` varchar(255) NOT NULL,
  `time_field` time NOT NULL,
  `date_field` date NOT NULL,
  `datetime_field` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tags_report`
--

INSERT INTO `tags_report` (`id`, `field_to_bold_in_report_display`, `field_to_hide_by_default`, `field_to_italic_in_report_display`, `field_to_right_align_in_report`, `field_to_bolditalic_in_report_display`, `numeric_field`, `decimal_field`, `currency_field`, `percent_field`, `url_field`, `time_field`, `date_field`, `datetime_field`) VALUES
(1, 'bold', 'hidden', 'italic', 'r-align', 'bold and italic', 100, '12.3450', '36.50', 85, 'https://google.com', '02:11:00', '2018-12-05', '2018-12-19 06:05:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tags_report`
--
ALTER TABLE `tags_report`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tags_report`
--
ALTER TABLE `tags_report`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
