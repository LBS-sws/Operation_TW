-- phpMyAdmin SQL Dump
-- version 4.0.10.20
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 05, 2017 at 05:11 AM
-- Server version: 5.6.26
-- PHP Version: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `operation`
--

-- --------------------------------------------------------

--
-- Table structure for table `opr_monthly_dtl`
--

CREATE TABLE IF NOT EXISTS `opr_monthly_dtl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hdr_id` int(10) unsigned NOT NULL,
  `data_field` char(5) NOT NULL,
  `data_value` varchar(100) DEFAULT NULL,
  `manual_input` char(1) DEFAULT 'N',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `opr_monthly_dtl`
--

INSERT INTO `opr_monthly_dtl` (`id`, `hdr_id`, `data_field`, `data_value`, `manual_input`, `lcu`, `luu`, `lcd`, `lud`) VALUES
(1, 4, '10001', '1000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:00'),
(2, 4, '10002', '2000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:03'),
(3, 4, '10003', '3000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:05'),
(4, 4, '10004', '4000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:09'),
(5, 4, '10005', '5000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:12'),
(6, 4, '10006', '6000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:16'),
(7, 4, '10007', '1000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:21'),
(8, 4, '10008', '3000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:23'),
(9, 4, '10009', '5000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:26'),
(10, 4, '10010', '6000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:28'),
(11, 4, '10011', '7000', 'Y', 'admin', 'admin', '2017-06-29 02:49:25', '2017-07-04 06:05:30');

-- --------------------------------------------------------

--
-- Table structure for table `opr_monthly_field`
--

CREATE TABLE IF NOT EXISTS `opr_monthly_field` (
  `code` char(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `upd_type` char(1) NOT NULL DEFAULT 'M',
  `field_type` char(1) NOT NULL DEFAULT 'N',
  `status` char(1) DEFAULT 'Y',
  `function_name` varchar(200) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `opr_monthly_field`
--

INSERT INTO `opr_monthly_field` (`code`, `name`, `upd_type`, `field_type`, `status`, `function_name`, `lcu`, `luu`, `lcd`, `lud`) VALUES
('10001', '清洁收入', 'M', 'N', 'Y', '1', 'admin', 'admin', '2017-05-25 00:48:01', '2017-07-05 04:57:12'),
('10002', '灭虫收入', 'M', 'N', 'Y', '2', 'admin', 'admin', '2017-05-25 00:48:01', '2017-07-05 04:57:18'),
('10003', '杂项及其他销售收入', 'M', 'N', 'Y', '3', 'admin', 'admin', '2017-05-25 00:48:01', '2017-07-05 01:07:12'),
('10004', '飘盈香收入', 'M', 'N', 'Y', '4', 'admin', 'admin', '2017-05-25 00:48:01', '2017-07-05 01:07:16'),
('10005', '甲醛收入', 'M', 'N', 'Y', '5', 'admin', 'admin', '2017-05-25 00:48:01', '2017-07-05 04:59:53'),
('10008', '纸品销售收入', 'M', 'N', 'Y', '6', 'admin', 'admin', '2017-05-25 00:48:01', '2017-07-05 04:59:56'),
('10006', '服务费收入合共', 'Y', 'N', 'Y', '7', 'admin', 'admin', '2017-05-30 17:59:48', '2017-07-05 01:07:23'),
('10007', '服务专利费', 'Y', 'N', 'Y', '8', 'admin', 'admin', '2017-05-30 17:59:48', '2017-07-05 01:07:26'),
('10009', '纸品专利费', 'Y', 'N', 'Y', '9', 'admin', 'admin', '2017-05-30 18:01:46', '2017-07-05 01:07:28'),
('10010', '专利费合共', 'Y', 'N', 'Y', '10', 'admin', 'admin', '2017-05-30 18:01:46', '2017-07-05 01:07:30'),
('10011', '收入合計', 'Y', 'N', 'Y', '11', 'admin', 'admin', '2017-06-30 08:27:31', '2017-07-05 01:07:38');

-- --------------------------------------------------------

--
-- Table structure for table `opr_monthly_hdr`
--

CREATE TABLE IF NOT EXISTS `opr_monthly_hdr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city` char(5) NOT NULL,
  `year_no` smallint(5) unsigned NOT NULL,
  `month_no` tinyint(3) unsigned NOT NULL,
  `status` char(1) DEFAULT 'N',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `opr_monthly_hdr`
--

INSERT INTO `opr_monthly_hdr` (`id`, `city`, `year_no`, `month_no`, `status`, `lcu`, `luu`, `lcd`, `lud`) VALUES
(4, 'SH', 2017, 1, 'Y', 'admin', 'admin', '2017-06-29 02:49:24', '2017-06-29 02:49:24');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
