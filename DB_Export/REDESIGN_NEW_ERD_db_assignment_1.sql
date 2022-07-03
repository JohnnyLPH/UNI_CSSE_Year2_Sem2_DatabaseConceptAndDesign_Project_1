-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2022 at 07:31 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_assignment_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `UserID` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`UserID`) VALUES
(23),
(49),
(50);

-- --------------------------------------------------------

--
-- Table structure for table `block`
--

CREATE TABLE `block` (
  `BlockID` int(6) NOT NULL,
  `OrchardID` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `block`
--

INSERT INTO `block` (`BlockID`, `OrchardID`) VALUES
(1, 1),
(9, 1),
(12, 1),
(15, 1),
(2, 2),
(6, 2),
(8, 2),
(3, 3),
(14, 3),
(4, 4),
(7, 4),
(13, 4),
(5, 5),
(10, 5),
(11, 5),
(27, 5),
(21, 10),
(23, 10),
(25, 12);

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `UserID` int(6) NOT NULL,
  `Address` varchar(256) NOT NULL,
  `Country` varchar(128) NOT NULL,
  `Photo` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`UserID`, `Address`, `Country`, `Photo`) VALUES
(1, 'G 38 Jln 8/23E Danau Kota, Kuala Lumpur', 'Malaysia', '/img/client/clientID1_2022-06-29_1656505665.jpg'),
(2, 'G Wisma Ung Hwa Geok 2 Jln Kulas Utara 2 Kuching', 'Malaysia', '/img/client/clientID2_2022-06-29_1656505880.jpg'),
(3, 'Nan Yuan San Chuang B-213, Kunming - Chenggongcounty', 'China', '/img/client/clientID3_2022-06-29_1656505908.jpg'),
(4, '344 Pine Tree Lane, Memphis, Tennessee', 'United States', '/img/client/clientID4_2022-06-29_1656505962.jpg'),
(5, 'Alley 15, Lane 340, Chunghwa 2nd Rd., Yungkang City, Tainan Hsien', 'Taiwan', '/img/client/clientID5_2022-06-29_1656505984.jpg'),
(24, 'No 123, Street ABC.', 'Madagascar', '/img/client/clientID24_2022-06-29_1656506487.jpg'),
(25, 'Some Street.', 'Afghanistan', '/img/client/clientID25_2022-06-29_1656506567.jpg'),
(27, 'No 99, Street 99.', 'Austria', '/img/client/clientID27_2022-06-29_1656507296.jpg'),
(30, 'No 1, Street A.', 'Benin', '/img/client/clientID30_2022-06-29_1656507199.jpg'),
(41, 'No B, Street Client B.', 'Australia', '/img/client/clientID41_2022-06-29_1656507093.jpg'),
(42, 'No D, Street D.', 'China', '/img/client/clientID42_2022-06-29_1656507876.jpg'),
(53, 'No E, Street E.', 'Denmark', '/img/client/clientID53_2022-07-01_1656684586.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `UserID` int(6) NOT NULL,
  `EstablishDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`UserID`, `EstablishDate`) VALUES
(14, '2022-06-16 00:00:00'),
(15, '2022-06-16 00:00:00'),
(16, '2022-06-16 00:00:00'),
(18, '2021-03-12 00:00:00'),
(19, '2012-12-12 00:00:00'),
(20, '2019-09-09 00:00:00'),
(21, '2019-09-09 00:00:00'),
(22, '2011-12-12 00:00:00'),
(38, '1988-12-12 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `onsale`
--

CREATE TABLE `onsale` (
  `SaleID` int(6) NOT NULL,
  `BlockID` int(6) NOT NULL,
  `SalePrice` float NOT NULL DEFAULT 10000,
  `SaleDate` datetime NOT NULL DEFAULT current_timestamp(),
  `SellerID` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `onsale`
--

INSERT INTO `onsale` (`SaleID`, `BlockID`, `SalePrice`, `SaleDate`, `SellerID`) VALUES
(1, 1, 15000, '2022-04-24 15:29:17', NULL),
(2, 2, 14000, '2022-04-24 15:29:17', NULL),
(3, 3, 17000, '2022-04-24 15:29:32', NULL),
(4, 4, 16500, '2022-04-24 15:29:32', NULL),
(5, 5, 20000, '2022-05-24 15:29:40', NULL),
(6, 4, 15000, '2022-05-24 15:29:17', 5),
(7, 6, 10000, '2022-06-23 21:20:47', NULL),
(8, 7, 10000, '2022-06-23 21:30:23', NULL),
(9, 8, 10000, '2022-06-23 23:06:56', NULL),
(10, 9, 10000, '2022-06-23 23:36:22', NULL),
(11, 4, 15000, '2022-06-26 15:29:17', 4),
(12, 15, 5000.08, '2022-06-27 20:23:05', NULL),
(18, 21, 14912, '2022-06-27 22:36:28', NULL),
(20, 23, 12345, '2022-06-28 12:38:27', NULL),
(22, 23, 10000, '2022-06-29 23:43:54', NULL),
(23, 25, 14111, '2022-06-30 14:31:43', NULL),
(25, 8, 20000, '2022-06-30 21:30:21', 5),
(26, 27, 30000, '2022-07-01 17:19:40', NULL),
(27, 5, 15000, '2022-07-03 10:50:25', 3),
(28, 14, 15000, '2022-07-03 13:19:29', NULL),
(29, 13, 20000, '2022-07-03 13:21:31', NULL),
(30, 12, 6000, '2022-07-03 13:21:49', NULL),
(31, 11, 8000, '2022-07-03 13:22:18', NULL),
(32, 10, 9000, '2022-07-03 13:22:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orchard`
--

CREATE TABLE `orchard` (
  `OrchardID` int(6) NOT NULL,
  `Address` varchar(256) NOT NULL,
  `Latitude` float NOT NULL,
  `Longitude` float NOT NULL,
  `CompanyID` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orchard`
--

INSERT INTO `orchard` (`OrchardID`, `Address`, `Latitude`, `Longitude`, `CompanyID`) VALUES
(1, 'Lorong 23C, Jalan Stampin, 93350 Kuching, Sarawak', 1.51493, 120.352, 15),
(2, '28620 Karak, Pahang', 3.36419, 102.071, 15),
(3, 'Jalan UP 4/2, Ukay Perdana, 68000 Ampang, Selangor', 3.22003, 101.768, 16),
(4, 'KM48 Persimpangan Bertingkat, Lebuhraya Karak, 28750 Bentong, Pahang', 3.37125, 101.856, 14),
(5, 'Jalan Besar Kampung Baharu Teras 27600, 27600 Raub, Pahang', 3.76008, 101.793, 14),
(10, 'No 89, Street Test, Sabah.', 1.231, 103.123, 22),
(12, 'Another New Orchard', 90, 123, 38),
(13, 'Test A New Orchard', 1, 9, 38);

-- --------------------------------------------------------

--
-- Table structure for table `purchaserequest`
--

CREATE TABLE `purchaserequest` (
  `RequestID` int(6) NOT NULL,
  `SaleID` int(6) NOT NULL,
  `ClientID` int(6) NOT NULL,
  `RequestDate` datetime NOT NULL DEFAULT current_timestamp(),
  `RequestPrice` float NOT NULL DEFAULT 10000,
  `AdminID` int(6) DEFAULT NULL,
  `ApprovalStatus` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `purchaserequest`
--

INSERT INTO `purchaserequest` (`RequestID`, `SaleID`, `ClientID`, `RequestDate`, `RequestPrice`, `AdminID`, `ApprovalStatus`) VALUES
(1, 1, 2, '2022-05-23 08:42:33', 15000, 23, 2),
(2, 4, 5, '2022-05-23 14:48:07', 16500, 23, 1),
(3, 6, 1, '2022-06-23 14:48:34', 15000, 23, 2),
(4, 5, 3, '2022-06-23 15:42:26', 20000, 23, 1),
(5, 6, 4, '2022-06-23 14:48:34', 15000, 23, 1),
(6, 2, 1, '2022-06-23 15:46:28', 14000, 23, 1),
(7, 3, 5, '2022-06-23 15:46:28', 17000, 23, 1),
(8, 9, 5, '2022-06-24 13:54:38', 10000, 23, 2),
(9, 11, 3, '2022-06-26 15:17:20', 15000, 23, 1),
(10, 9, 5, '2022-06-27 13:54:38', 10000, 23, 1),
(11, 1, 4, '2022-06-28 15:12:45', 12000, 23, 2),
(14, 7, 4, '2022-06-30 11:53:55', 10000, NULL, 2),
(15, 7, 1, '2022-06-30 15:51:26', 20000, NULL, 0),
(17, 8, 27, '2022-06-30 21:56:59', 10000, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `UserID` int(6) NOT NULL,
  `EmployDate` datetime NOT NULL DEFAULT current_timestamp(),
  `Salary` float NOT NULL,
  `CompanyID` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`UserID`, `EmployDate`, `Salary`, `CompanyID`) VALUES
(6, '2022-04-23 22:10:08', 3450.5, 14),
(7, '2022-04-23 22:11:56', 5725.9, 14),
(8, '2022-04-23 22:13:54', 3650.5, 15),
(9, '2022-04-23 22:14:22', 3601, 15),
(10, '2022-04-23 22:14:41', 6210.2, 16),
(11, '2022-04-24 16:01:15', 5900, 16),
(12, '2022-04-24 16:01:15', 6400, 15),
(13, '2022-04-24 16:02:07', 6304.5, 14),
(26, '2022-06-26 21:02:27', 3000, 22),
(29, '2001-11-11 00:00:00', 4444, 14),
(39, '2001-11-11 00:00:00', 1234, 38);

-- --------------------------------------------------------

--
-- Table structure for table `tree`
--

CREATE TABLE `tree` (
  `TreeID` int(6) NOT NULL,
  `SpeciesName` varchar(128) DEFAULT NULL,
  `Latitude` float NOT NULL,
  `Longitude` float NOT NULL,
  `PlantDate` datetime NOT NULL DEFAULT current_timestamp(),
  `BlockID` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tree`
--

INSERT INTO `tree` (`TreeID`, `SpeciesName`, `Latitude`, `Longitude`, `PlantDate`, `BlockID`) VALUES
(1, 'American Chestnut', 1.51392, 110.352, '2022-06-16 16:11:55', 1),
(2, 'Aspen Tree', 3.3642, 102.07, '2022-06-16 16:11:55', 2),
(3, 'Black Walnut', 3.22005, 101.769, '2022-06-16 16:11:55', 3),
(4, 'Markhamia', 3.37126, 101.857, '2022-06-16 16:11:55', 4),
(5, 'Medang', 3.76009, 101.792, '2022-06-16 16:11:55', 5),
(6, 'Chestnut Oak', 1.51391, 110.351, '2022-06-16 16:11:55', 1),
(7, 'Croton Megabcarpas', 3.36419, 102.071, '2022-06-16 16:11:55', 2),
(8, 'Giant Sequoia', 3.2204, 101.768, '2022-06-16 16:11:55', 3),
(9, 'Cottonwood Tree', 3.37125, 101.878, '2022-06-16 16:11:55', 4),
(10, 'Giant Yellow Mulberry', 3.76008, 101.791, '2022-06-16 16:11:55', 5),
(11, 'Jeffrey Pine', 1.51392, 110.351, '2022-06-16 16:11:55', 1),
(12, 'Lodgepole Pine', 3.3642, 102.072, '2022-06-16 16:11:55', 2),
(13, 'Longleaf Pine', 3.22005, 101.768, '2022-06-16 16:11:55', 3),
(14, 'Northern Red Oak', 3.37127, 101.877, '2022-06-16 16:11:55', 4),
(15, 'Oak Tree', 3.76009, 101.791, '2022-06-16 16:11:55', 5),
(16, 'Olive Tree', 1.5139, 110.35, '2022-06-16 16:11:55', 1),
(17, 'Palm Tree', 3.36418, 102.068, '2022-06-16 16:11:55', 2),
(18, 'Paper Birch', 3.22003, 101.767, '2022-06-16 16:11:55', 3),
(19, 'Patula Pine', 3.37124, 101.855, '2022-06-16 16:11:55', 4),
(20, 'Pignut Hickory', 3.76007, 101.79, '2022-06-16 16:11:55', 5),
(21, 'Pinabete Tree', 1.5139, 110.351, '2022-06-16 16:11:55', 1),
(22, 'Pitch Pine', 3.36418, 102.069, '2022-06-16 16:11:55', 2),
(23, 'Ponderosa Pine', 3.22004, 101.768, '2022-06-16 16:11:55', 3),
(24, 'Quaking Aspen', 3.37125, 101.856, '2022-06-16 16:11:55', 4),
(25, 'Red Pine', 3.76008, 101.791, '2022-06-16 16:11:55', 5),
(26, 'American Chestnut', 1.51381, 110.362, '2022-06-23 23:47:14', 9),
(27, 'American Chestnut', 1.5169, 120.383, '2022-06-27 21:49:47', 9),
(34, 'American Chestnut', 69.69, 69.69, '2022-06-29 21:25:27', 23),
(37, 'American Chestnut', 69, 69, '2022-06-29 23:28:04', 23),
(38, 'American Chestnut', 9, 6, '2022-06-29 23:28:46', 23),
(39, 'Fat Nut', 12, 21, '2022-06-29 23:28:56', 23);

-- --------------------------------------------------------

--
-- Table structure for table `treeupdate`
--

CREATE TABLE `treeupdate` (
  `UpdateID` int(9) NOT NULL,
  `TreeID` int(6) NOT NULL,
  `StaffID` int(6) NOT NULL,
  `TreeImage` varchar(256) NOT NULL,
  `TreeHeight` float NOT NULL,
  `Diameter` float NOT NULL,
  `Status` varchar(1) NOT NULL,
  `UpdateDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `treeupdate`
--

INSERT INTO `treeupdate` (`UpdateID`, `TreeID`, `StaffID`, `TreeImage`, `TreeHeight`, `Diameter`, `Status`, `UpdateDate`) VALUES
(1, 1, 6, '????\0Exif\0\0II*\0\0\0\0\0\0\0\0\0\0\0\0??\0Ducky\0\0\0\0\0<\0\0??+http://ns.adobe.com/xap/1.0/\0<?xpacket begin=\"﻿\" id=\"W5M0MpCehiHzreSzNTczkc9d\"?> <x:xmpmeta xmlns:x=\"adobe:ns:meta/\" x:xmptk=\"Adobe XMP Core 5.3-c011 66.145661, 2012/02/06-14:56:27        \"> <rdf:RDF xmlns', 30, 0.95, 'G', '2022-04-25 21:05:30'),
(2, 2, 8, '????\0JFIF\0\0\0\0\0\0??\0;CREATOR: gd-jpeg v1.0 (using IJG JPEG v62), quality = 90\n??\0C\0\n\n\n\r\r??\0C		\r\r??\0XX\"\0??\0\0\0\0\0\0\0', 35, 0.9, 'Y', '2022-04-25 21:05:30'),
(3, 3, 9, '????\0JFIF\0\0\0\0\0\0??XICC_PROFILE\0\0\0HLino\0\0mntrRGB XYZ ?\0\0	\0\01\0\0acspMSFT\0\0\0\0IEC sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0??\0\0\0\0\0?-HP  \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0cprt\0\0P\0\0\03desc\0\0?\0\0\0lwtpt\0\0?\0\0\0bkpt\0\0\0\0\0rXYZ\0\0\0\0\0gXYZ\0\0,\0\0\0bXYZ\0\0@\0\0\0dm', 40, 1.12, 'G', '2022-04-25 21:13:51'),
(4, 6, 8, '????\0JFIF\0,,\0\0??\02Processed By eBay with ImageMagick, z1.1.0. ||B2??\0C\0		\n\r\Z\Z $.\' \",#(7),01444\'9=82<.342??\0C			\r\r2!!22222222222222222222222222222222222222222222222222??\0??\0??\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 43, 1.83, 'Y', '2022-04-25 21:13:51'),
(5, 9, 6, '????\0C\0\n\r	\n\n\r\r\Z\Z\Z??\0C\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z??\0?J\"\0??\0\0\0\0\0\0\0\0\0\0\0\0\0\0	??\0I\0\0\0!\"1A2Q#aqB??$?3CRb??%r??S??DV', 22, 0.55, 'G', '2022-04-25 21:19:26'),
(6, 7, 9, '????\0DExif\0\0MM\0*\0\0\0\0??\0\0\0\0\0\0\0&?i\0\0\0\0\0\0\06\0\0\0\0COPYRIGHT, 2007\0\0\0\0\0\0\0??\0?Photoshop 3.0\08BIM\0\0\0\0\0gZ\0%G\0\0\0\0\0\0?\0Ehttps://flickr.com/e/cApSgUUBnM6T3mZtul0SjiLl5kwFES1mYU%2BYFJiUCVM%3D\0\0\0\0??\0JFIF\0\0\0\0\0\0??\0C\0\n', 36, 0.7, 'G', '2022-04-25 21:19:26'),
(7, 8, 9, '????ICC_PROFILE\0\0\0lcms\0\0mntrRGB XYZ ?\0\0\0\0)\09acspAPPL\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0??\0\0\0\0\0?-lcms\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\ndesc\0\0\0?\0\0\0^cprt\0\0\\\0\0\0wtpt\0\0h\0\0\0bkpt\0\0|\0\0\0rXYZ\0\0?\0\0\0gXYZ\0\0?\0\0\0bXYZ\0\0?\0\0\0rTRC\0\0?\0\0\0@gTRC\0\0?', 86, 7.5, 'R', '2022-04-25 21:24:26'),
(8, 11, 9, '????\0JFIF\0\0\0\0\0\0??\0;CREATOR: gd-jpeg v1.0 (using IJG JPEG v62), quality = 85\n??\0C\0	\Z!\Z\"$\"$??\0C??\0?,\"\0??\0\0\0\0\0\0\0', 42, 2.5, 'G', '2022-04-25 21:24:26'),
(9, 12, 8, '????\0Exif\0\0II*\0\0\0\0\0\0\0\0\0\0\0\0??\0Ducky\0\0\0\0\0<\0\0??http://ns.adobe.com/xap/1.0/\0<?xpacket begin=\"﻿\" id=\"W5M0MpCehiHzreSzNTczkc9d\"?> <x:xmpmeta xmlns:x=\"adobe:ns:meta/\" x:xmptk=\"Adobe XMP Core 5.6-c148 79.164036, 2019/08/13-01:06:57        \"> <rdf:RDF xmlns', 50, 2, 'G', '2022-04-25 21:28:07'),
(10, 13, 8, '????\0JFIF\0\0H\0H\0\0??TICC_PROFILE\0\0\0DUCCM@\0\0mntrRGB XYZ ?\0\0\0\0\0\0\0\0acspMSFT\0\0\0\0CANOZ009\0\0\0\0\0\0\0\0\0\0\0\0\0\0??\0\0\0\0\0?-CANO\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0rTRC\0\0,\0\0gTRC\0\0,\0\0bTRC\0\0,\0\0rXYZ\0\0	8\0\0\0gXYZ\0\0	L\0\0\0bXYZ\0\0	`\0\0\0chad\0\0	t\0\0\0,cp', 47, 1.2, 'Y', '2022-04-25 21:28:07'),
(11, 4, 6, '????\0C\0\n\r	\n\n\r\r\Z\Z\Z??\0C\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z??\0??\"\0??\0\0\0\0\0\0\0\0\0\0\0\0	??\0C\0\0\0!1\"AQa2q#B???3Rbr??$%??C??S?s', 5.31, 0.7, 'G', '2022-04-25 21:33:33'),
(12, 5, 6, '????\0JFIF\0\0`\0`\0\0??\0C\0??\0C??\0?T\0??\0\0\0\0\0\0\0\0\0\0\0	\n\0??\0I\0\r\0!\"1	#2AQa', 8.45, 1.67, 'G', '2022-04-25 21:33:33'),
(13, 14, 9, '????\0JFIF\0\0\0\0\0\0??\0>CREATOR: gd-jpeg v1.0 (using IJG JPEG v62), default quality\n??\0C\0		\n\r\Z\Z $.\' \",#(7),01444\'9=82<.342??\0C			\r\r2!!22222222222222222222222222222222222222222222222222??\0?\"\0??\0\0\0\0', 28, 1.99, 'G', '2022-04-25 21:38:51'),
(14, 15, 6, '????\0?Exif\0\0MM\0*\0\0\0\0\Z\0\0\0\0\0\0\0V\0\0\0\0\0\0\0^(\0\0\0\0\0\0\0;\0\0\0\0\0\0\0f\0\0\0\0\0\0\0??\0\0\0\0\r\0\0\0x\0\0\0\0\0\0\0H\0\0\0\0\0\0H\0\0\0Frank Schulenburg\0CC BY-SA 4.0\0\0??ICC_PROFILE\0\0\0lcms\0\0mntrRGB XYZ ?\0\0\0\0)\09acspAPPL\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0??\0\0\0\0\0?-lcms\0\0\0\0\0\0\0\0', 40, 3, 'G', '2022-04-25 21:38:51'),
(15, 16, 8, '????\0(Exif\0\0MM\0*\0\0\0\0?i\0\0\0\0\0\0\0\Z\0\0\0\0\0\0\0\0\0\0??\0?Photoshop 3.0\08BIM\0\0\0\0\0eZ\0%G\0\0\0\0\0\0?\0Chttps://flickr.com/e/NqSwW7acLJNc8OyaQTE6ik7jWqyqq0MC7eWCrAJRc2I%3D\0\0\0\0??\0C\0??\0C', 14, 9, 'R', '2022-04-25 21:45:07'),
(16, 17, 8, '????\0JFIF\0,,\0\0??\0IFile source: https://commons.wikimedia.org/wiki/File:Palm_tree_CANA.JPG??ICC_PROFILE\0\0\0lcms\0\0mntrRGB XYZ ?\0\0\0\0)\09acspAPPL\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0??\0\0\0\0\0?-lcms\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\ndesc\0\0\0?\0\0\0', 50, 5, 'G', '2022-04-25 21:45:07'),
(17, 18, 6, '????\0C\0\n\r	\n\n\r\r\Z\Z\Z??\0C\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z??\0%?\"\0??\0\0\0\0\0\0\0\0\0\0\0\0\0	??\0>\0\0!1\"A2Q#aBq$3??R?%Cb??4?c??r??', 38, 0.75, 'G', '2022-04-25 21:50:56'),
(18, 19, 8, '????\0JFIF\0\0d\0d\0\0??\0C\0	\n\n			\n\n		\r\r\n??\0C	??\0?\0??\0\0\0\0\0\0\0\0\0\0\0\0\0	??\0A\0\0\0\0!\"1A#2QBaq?$', 15, 0.5, 'G', '2022-04-25 21:50:56'),
(19, 20, 9, '????\0JFIF\0\0\0\0\0\0??\0>CREATOR: gd-jpeg v1.0 (using IJG JPEG v62), default quality\n??\0C\0		\n\r\Z\Z $.\' \",#(7),01444\'9=82<.342??\0C			\r\r2!!22222222222222222222222222222222222222222222222222??\0??\"\0??\0\0\0\0', 37, 1.2, 'Y', '2022-04-25 22:10:20'),
(20, 21, 8, '????\0JFIF\0\0\0\0\0\0??\0;CREATOR: gd-jpeg v1.0 (using IJG JPEG v62), quality = 95\n??\0C\0			\n\n\n\n\n\n	\n\n\n??\0C\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n??\0?\"\0??\0\0\0\0\0\0\0', 34, 0.88, 'G', '2022-04-25 22:10:20'),
(21, 22, 9, '????\0JFIF\0\0`\0`\0\0??\0C\0\n\n\n		\n\Z%\Z# , #&\')*)-0-(0%()(??\0C\n\n\n\n(\Z\Z((((((((((((((((((((((((((((((((((((((((((((((((((??\0X?!\0??\0\0\0\0\0\0\0\0\0\0\0\0\0\0??\0D\0\r\0!1A\"Q2a#Bq?R?3?', 30, 1.56, 'G', '2022-04-25 22:15:13'),
(22, 10, 6, '????\0JFIF\0\0\0\0\0\0??\0?\0\n\n\n\"\"$$6*&&*6>424>LDDL_Z_||?\0\r\0\r\0\r\0\r\0\0\r\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0-\0 \0\"\0 \0\"\0 \0-\0D\0*\02\0*\0*\02\0*\0D\0<\0I\0;\07\0;\0I\0<\0l\0U\0K\0K\0U\0l\0}\0i\0c\0i\0}\0?\0?\0?\0?\0?\0?\0?\0?\0?N??\0,,\"\0??\0\0\0\0\0\0\0\0\0\0\0', 22, 1.33, 'G', '2022-04-25 22:15:13'),
(23, 23, 6, '????ICC_PROFILE\0\0\0lcms\0\0mntrRGB XYZ ?\0\0\0\0)\09acspAPPL\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0??\0\0\0\0\0?-lcms\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\ndesc\0\0\0?\0\0\0^cprt\0\0\\\0\0\0wtpt\0\0h\0\0\0bkpt\0\0|\0\0\0rXYZ\0\0?\0\0\0gXYZ\0\0?\0\0\0bXYZ\0\0?\0\0\0rTRC\0\0?\0\0\0@gTRC\0\0?', 69, 5.2, 'G', '2022-04-25 22:20:14'),
(24, 24, 8, '????\0C\0\n\r	\n\n\r\r\Z\Z\Z??\0C\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z\Z??\0\0?\0?\"\0??\0\0\0\0\0\0\0\0\0\0\0\0\0	??\09\0\0\0!\"1A#2QBaq$3R?b??5C?????\0\0', 5, 0.45, 'G', '2022-04-25 22:20:14'),
(25, 25, 8, '????\0JFIF\0^^\0\0??\0]File source: http://commons.wikimedia.org/wiki/File:Japanese_Red_Pine_(Japanese_garden).JPG??TICC_PROFILE\0\0\0DUCCM@\0\0mntrRGB XYZ ?\0\0\0\0\0\0\0\0acspMSFT\0\0\0\0CANOZ009\0\0\0\0\0\0\0\0\0\0\0\0\0\0??\0\0\0\0\0?-CANO\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 25, 1.2, 'Y', '2022-04-25 22:21:44'),
(26, 1, 11, 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fimg-aws.ehowcdn.com%2Fdefault%2Fds-photo%2Fgetty%2Farticle%2F217%2F99%2F122495142.jpg&f=1&nofb=1', 30.5, 1, 'G', '2022-06-24 01:05:30');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(6) NOT NULL,
  `Username` varchar(128) NOT NULL,
  `Email` varchar(256) NOT NULL,
  `PasswordHash` varchar(128) NOT NULL,
  `RealName` varchar(128) NOT NULL,
  `UserType` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Username`, `Email`, `PasswordHash`, `RealName`, `UserType`) VALUES
(1, 'bobwasp', 'bobwasp@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Bob Wong', 'CL'),
(2, 'generousgenerous', 'genehhc@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Eugene He Huaicheng', 'CL'),
(3, 'swingiguana', 'fangjie@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Li Fangjie', 'CL'),
(4, 'lexusstal', 'lexad@hotmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Alex Andrews', 'CL'),
(5, 'kimonorabbit', 'liaozh94@outlook.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Liao Zhiwang', 'CL'),
(6, 'alimuhd75', 'alimuhd75@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Muhammad Ali bin Mahmood', 'ST'),
(7, 'flipflopshamster', 'hamsterff89@hotmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Mohammad Hamdan bin Asyraf', 'ST'),
(8, 'huaff88', 'huaff88@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Feng Fanghua', 'ST'),
(9, 'kamban83', 'kamban83@outlook.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Kamala Bhandari', 'ST'),
(10, 'dergzw90', 'dergzw90@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Long Zhiwang', 'ST'),
(11, 'marcell19', 'marcell19@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Marcell Cynova', 'ST'),
(12, 'sean91', 'sean91@hotmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Sean Slager', 'ST'),
(13, 'damon47', 'damon47@outlook.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Damon Ziegenfuss', 'ST'),
(14, 'companyA', 'companyA@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Company A', 'CO'),
(15, 'companyB', 'companyB@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Company B', 'CO'),
(16, 'companyC', 'companyC@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Company C', 'CO'),
(18, 'companyD', 'companyD@gmail.com', '$2y$10$ccTFckg.nrktZrp5VVOOhu5I2xTOq37ygOt9NcXLr9/QvIOKDNyt6', 'Company D', 'CO'),
(19, 'companyE', 'companyE@gmail.com', '$2y$10$TBRau9GL5IyOa5j3BBcmaeqY8pXum0vGekZ9nfJQo5KkcJoou1Hjy', 'Company E', 'CO'),
(20, 'companyF', 'companyF@gmail.com', '$2y$10$O3Y4UZQgLmnakhBWo0m5pu91V18y4ASyRhf3XEgiXp6lueQTFByaC', 'Company F', 'CO'),
(21, 'companyG', 'companyG@gmail.com', '$2y$10$sZaERhUBBMgGsQhqF/kkhutB0hyU21kOxsp2zWY55QdMiO/ICMuxi', 'Company G', 'CO'),
(22, 'companyH', 'companyH@gmail.com', '$2y$10$dq/lVrjhct6MnqGHltIfuO8xqw5XEH2iHptniKPs08Fk3Ay8xtOVG', 'Company H', 'CO'),
(23, 'LPH', 'adminlph@gmail.com', '$2y$10$OVtzd7NRIsy9QtKVJimYAuLIsJLJagV/ZsFhRYeB3yk9OeWVNG61K', 'Mr Lau, P.H.', 'AD'),
(24, 'BlackMan', 'mj@gmail.com', '$2y$10$eKZtwCUZ4kpsB6co7WDjwOqDudGqW.ckjOKfXtrK5eZXKR/TLnYxK', 'Michael Jordan', 'CL'),
(25, 'Deez', 'dMJ@gmail.com', '$2y$10$1980r2SUfhKFrwEsETxh.OeeA5kO8IsTp6YRXJ9COwnSMN5U4pUlW', 'Michael Jackson', 'CL'),
(26, 'staffA', 'staffA@gmail.com', '$2y$10$zC41MBIhehz.asdh6KPNhexeFgWEN7Tss2WqUOAFxevWV64Am74I.', 'Staff A', 'ST'),
(27, 'clientA', 'clientA@gmail.com', '$2y$10$BSI7263MJgKhKQYWnZ7vxu.xgCqzO6zsxp6LpB65BTq8NjushEGA6', 'Client A', 'CL'),
(29, 'staffB', 'staffB@gmail.com', '$2y$10$g4ErGmRtXbX4ztPUhE85qeh.BHB90XTM7S1mQU4/4RrnMN2Re3NJm', 'Staff B', 'ST'),
(30, 'clientB', 'clientB@gmail.com', '$2y$10$c.DnM2PvUVsR4WFepSNbK.TmBnWvCwChnI1/W4U.0GqNu.s64wWwy', 'Client B', 'CL'),
(38, 'companyI', 'companyI@gmail.com', '$2y$10$d8H7DOZrmwwdIt5QovBpu.SJxrru/7Gd9v93Exu.hDDc.81CVbcLK', 'Company I', 'CO'),
(39, 'staffC', 'staffC@gmail.com', '$2y$10$apqOcePweNCp4t6yd0.i/uEWWRsBPEoBxBNx.yHB/1TSMb9a9DUfi', 'Staff C', 'ST'),
(41, 'clientC', 'clientC@gmail.com', '$2y$10$sVkkp8iVFEFA.p5Mbjfgw.Z1EMbrP.zlRhpL65LLBaFRDZWSzAAja', 'Client C', 'CL'),
(42, 'clientD', 'clientD@gmail.com', '$2y$10$19IhNIspio8o7G6V8jTO6uhBhAY14xhL6Xe8XJAl7mQOJ5t1pbaI2', 'Client D', 'CL'),
(49, 'adminA', 'adminA@gmail.com', '$2y$10$44ZJ72pV5/2ulxK8/la8De8a0rNL2tOgErPm3wf3WPICSwiQNXjPm', 'Admin A', 'AD'),
(50, 'adminB', 'adminB@gmail.com', '$2y$10$RPMp58PPh41KToECWI5ofOWA3Aqv2BqSctzc1/pXEpBVRNGIoU.4C', 'Admin B', 'AD'),
(53, 'clientE', 'clientE@gmail.com', '$2y$10$66wSPHZRMAoii0nZmZNJye2qcARxNlG8UI6numZyi2zbHTaxBt3p2', 'Client E', 'CL');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `block`
--
ALTER TABLE `block`
  ADD PRIMARY KEY (`BlockID`),
  ADD KEY `block_in_orchard` (`OrchardID`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `onsale`
--
ALTER TABLE `onsale`
  ADD PRIMARY KEY (`SaleID`),
  ADD KEY `ClientID` (`BlockID`),
  ADD KEY `sale_by_client` (`SellerID`);

--
-- Indexes for table `orchard`
--
ALTER TABLE `orchard`
  ADD PRIMARY KEY (`OrchardID`),
  ADD KEY `CompanyID` (`CompanyID`);

--
-- Indexes for table `purchaserequest`
--
ALTER TABLE `purchaserequest`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `purchase_request_for_sale` (`SaleID`),
  ADD KEY `purchase_request_by_client` (`ClientID`),
  ADD KEY `purchase_request_approve_by_admin` (`AdminID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`UserID`),
  ADD KEY `staff_in_company` (`CompanyID`);

--
-- Indexes for table `tree`
--
ALTER TABLE `tree`
  ADD PRIMARY KEY (`TreeID`),
  ADD KEY `BlockID` (`BlockID`);

--
-- Indexes for table `treeupdate`
--
ALTER TABLE `treeupdate`
  ADD PRIMARY KEY (`UpdateID`,`TreeID`,`StaffID`),
  ADD KEY `StaffID` (`StaffID`),
  ADD KEY `TreeID` (`TreeID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `block`
--
ALTER TABLE `block`
  MODIFY `BlockID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `onsale`
--
ALTER TABLE `onsale`
  MODIFY `SaleID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `orchard`
--
ALTER TABLE `orchard`
  MODIFY `OrchardID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `purchaserequest`
--
ALTER TABLE `purchaserequest`
  MODIFY `RequestID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tree`
--
ALTER TABLE `tree`
  MODIFY `TreeID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `treeupdate`
--
ALTER TABLE `treeupdate`
  MODIFY `UpdateID` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_is_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `block`
--
ALTER TABLE `block`
  ADD CONSTRAINT `block_in_orchard` FOREIGN KEY (`OrchardID`) REFERENCES `orchard` (`OrchardID`);

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_is_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `company_is_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `onsale`
--
ALTER TABLE `onsale`
  ADD CONSTRAINT `block_for_sale` FOREIGN KEY (`BlockID`) REFERENCES `block` (`BlockID`),
  ADD CONSTRAINT `sale_by_client` FOREIGN KEY (`SellerID`) REFERENCES `client` (`UserID`);

--
-- Constraints for table `orchard`
--
ALTER TABLE `orchard`
  ADD CONSTRAINT `orchard_own_by_company` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`UserID`);

--
-- Constraints for table `purchaserequest`
--
ALTER TABLE `purchaserequest`
  ADD CONSTRAINT `purchase_request_approve_by_admin` FOREIGN KEY (`AdminID`) REFERENCES `admin` (`UserID`),
  ADD CONSTRAINT `purchase_request_by_client` FOREIGN KEY (`ClientID`) REFERENCES `client` (`UserID`),
  ADD CONSTRAINT `purchase_request_for_sale` FOREIGN KEY (`SaleID`) REFERENCES `onsale` (`SaleID`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_in_company` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`UserID`),
  ADD CONSTRAINT `staff_is_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `tree`
--
ALTER TABLE `tree`
  ADD CONSTRAINT `tree_in_block` FOREIGN KEY (`BlockID`) REFERENCES `block` (`BlockID`);

--
-- Constraints for table `treeupdate`
--
ALTER TABLE `treeupdate`
  ADD CONSTRAINT `update_by_staff` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`UserID`),
  ADD CONSTRAINT `update_for_tree` FOREIGN KEY (`TreeID`) REFERENCES `tree` (`TreeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
