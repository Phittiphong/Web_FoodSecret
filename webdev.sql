-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2024 at 06:40 PM
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
-- Database: `webdev`
--

-- --------------------------------------------------------

--
-- Table structure for table `campaign`
--

CREATE TABLE `campaign` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campaign`
--

INSERT INTO `campaign` (`Id`, `firstName`, `lastName`, `email`) VALUES
(2, 'พิตติพงษ์', 'ผ่านแสนเสาร์', '1111@gmail'),
(3, 'วสันต์', 'ดวงเกิด', 'sekcy01@gmail.com'),
(5, 'สุทธิดา', 'มีสานุ', 'Namo@mail.com'),
(10, 'โอจิน', 'อิอิ', 'eiei@mail.com'),
(12, 'พิตติพงษ์', 'ผ่านแสนเสาร์', 'now@gmail.com'),
(13, 'พิตติพงษ์', 'ผ่านแสนเสาร์', 'now@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'ทอด'),
(2, 'ผัด'),
(3, 'ต้ม'),
(4, 'นึ่ง'),
(5, 'ตุ๋น'),
(6, 'ย่าง'),
(7, 'อบ'),
(8, 'ยำ'),
(9, 'แกง');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment_text`, `created_at`) VALUES
(12, 52, 3, '...', '2024-09-13 03:13:46'),
(67, 57, 3, 'dsadsadasdas', '2024-09-26 17:56:03'),
(69, 112, 3, 'เสกอ้วง', '2024-10-22 14:09:37'),
(70, 51, 3, 'Party อ้วน', '2024-10-22 14:14:06'),
(71, 52, 3, 'ddd', '2024-10-23 07:55:07'),
(72, 56, 3, 'ddddd', '2024-10-23 17:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `message`, `submitted_at`) VALUES
(15, 'พิตติพงษ์ ผ่านแสนเสาร์', 'now@gmail.com', 'พบเจอปัญหา', '2024-10-24 06:54:45'),
(16, 'พิตติพงษ์ ผ่านแสนเสาร์', 'now@gmail.com', 'เทสครับ', '2024-10-24 07:27:50');

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `following`
--

CREATE TABLE `following` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `post_content` text NOT NULL,
  `post_image` varchar(255) DEFAULT NULL,
  `post_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `username`, `post_content`, `post_image`, `post_time`, `created_at`, `category_id`, `user_id`) VALUES
(51, '@Admin', 'ทอดมันกุ้งกรอบ\r\n\r\nวัตถุดิบ\r\n1.กุ้งขาว 500 กรัม\r\n2.หมูสามชั้น 100 กรัม\r\n3.คนอร์ขวด ซอสข้นปรุงรส รสหมู 1 ช้อนโต๊ะ\r\n4.รากผักชี 1 ช้อนโต๊ะ\r\n5.กระเทียม 1 ช้อนโต๊ะ\r\n6.พริกไทย 1 ช้อนโต๊ะ\r\n7.ไข่ไก่ 2 ฟอง\r\n8.แป้งทอดกรอบ 100 กรัม\r\n9.เกล็ดขนมปัง 100 กรัม\r\n10.น้ำมันสำหรับทอด 2 ถ้วยตวง\r\n\r\nวิธีทำ\r\nSTEP 1 : ผสมทอดมันกุ้ง\r\nนำกุ้งขาว หมูสามชั้น ไข่ไก่ 1 ฟอง รากผักชี พริกไทย และกระเทียมใส่ลงในโถปั่น ปรุงรสด้วยคนอร์ขวด ซอสข้นปรุงรส รสหมู 1 ช้อนโต๊ะ ปั่นทุกอย่างให้ละเอียดเข้ากัน\r\nนำส่วนผสมที่ปั่นเสร็จแล้วไปแช่ในตู้เย็นประมาณ 30 นาที\r\nSTEP 2 : ทอด\r\nนำส่วมผสมของทอดมันกุ้งมาปั้นเป็นก้อนเท่า ๆ กันแล้วนำไปคลุกกับแป้งทอดกรอบ ไข่ไก่ และเกล็ดขนมปัง จากนั้นนำลงไปทอดในน้ำมันร้อน ๆ ทอดจนมันสุกกรอบ และเปลี่ยนสีน้ำตาล จากนั้นจึงตักขึ้นพักให้สะเด็ดน้ำมัน \r\nSTEP 3 : จัดเสิร์ฟ\r\nนำทอดมันกุ้งกรอบที่สะเด็ดน้ำมันแล้วมาเสียบไม้ จัดเรียงใส่จานเสิร์ฟคู่กับน้ำจิ้มไก่ เพียงเท่านี้ “ทอดมันกุ้งกรอบ” ของเราก็พร้อมให้ทุกคนลิ้มลองแล้วค่า!', 'ทอดมันกุ้ง-02.jpg', '2024-09-06 00:39:40', '2024-09-06 00:39:40', 1, 3),
(52, '@Admin', 'หมูทอดเกลือ\r\n\r\nวัตถุดิบ\r\n1.หมูสันนอกหั่นเส้น ½ กิโลกรัม\r\n2.เกลือ 2 ช้อนชา\r\n3.พริกไทย ½ ช้อนชา\r\n4.น้ำมันสำหรับทอด\r\n5.ต้นหอมซอย สำหรับโรยตกแต่ง\r\n\r\nวิธีทำ\r\nSTEP 1 : หมัก\r\nหมักหมูสันนอก กับ เกลือ และพริกไทย คลุกผสมให้เข้ากัน หมักทิ้งไว้ 30 นาที\r\nSTEP 2 : ทอด\r\nตั้งกระทะใส่น้ำมัน ใช้ไฟกลางค่อยแรง นำหมูที่หมักไว้ลงไปทอดจนสุกเหลือง แล้วตักขึ้นสะเด็ดน้ำมันพักไว้\r\nSTEP 3 : จัดเสิร์ฟ\r\nจัดใส่จานจัดเสิร์ฟ โรยด้วยต้นหอมซอย จะกินกับข้าวสวยหรือข้าวเหนียวก็เลิศ!', 'hqdefault.jpg', '2024-09-06 00:44:02', '2024-09-06 00:44:02', 1, 3),
(54, '@Admin', 'ผัดผักบุ้งไฟแดง\r\n\r\nวิธีทำผัดผักบุ้งไฟแดง\r\n\r\nส่วนผสม\r\nผักบุ้ง 1 กำ\r\nกระเทียมสับ 1 ช้อนโต๊ะ\r\nพริกขี้หนูแดงสับ ½ ช้อนโต๊ะ\r\nเต้าเจี้ยว 1 ช้อนชา\r\nซีอิ๊วขาว 2 ช้อนชา\r\nน้ำมันหอย 1 ช้อนโต๊ะ\r\nน้ำตาลทราย 1 ช้อนชา\r\nน้ำเปล่า 3 ช้อนโต๊ะ\r\n\r\nวิธีทำ\r\n1.นำก้านผักบุ้งที่หนามาซอยแฉลบ เพื่อที่เวลาผัดจะได้สุกง่าย จากนั้น ล้างน้ำแล้วพักไว้\r\n2.ใส่ส่วนผสม และเครื่องปรุงต่าง ๆ บนผักบุ้งสำหรับเตรียมผัด\r\n3.ตั้งกระทะให้ร้อนจัด จากนั้นเทน้ำมันใส่ลงไปประมาณ ¼ ถ้วยตวง แล้วเกลี่ยน้ำมันให้ทั่วพื้นกระทะ จากนั้น จึงใส่ผักบุ้งลงไป เอียงกระทะให้ออกนอกตัวเล็กน้อย แล้วผัดให้ผักบุ้งสัมผัสกับกระทะเท่า ๆ กัน ไม่ให้จับตัวเป็นกระจุกก้อน ๆ ติดกัน\r\n4.เมื่อผัดจนผักบุ้งเริ่มสลบแล้ว ปิดไฟแล้วตักขึ้นใส่จานเสิร์ฟกับข้าวร้อน ๆ พร้อมเสิร์ฟ', '648cd7cf4258a83caa69d229bdc9368d_stir-fried-thai-morning-glory.jpeg', '2024-09-06 00:47:29', '2024-09-06 00:47:29', 2, 3),
(55, '@Admin', 'ผัดมาม่า\r\n\r\nส่วนผสม ผัดมาม่า\r\nบะหมี่กึ่งสำเร็จรูป 4 ห่อ\r\nหมูหมัก 150 กรัม\r\nไข่ไก่ 4 ฟอง\r\nกระเทียม 20 กรัม\r\nน้ำตาลทราย 1/2 ช้อนโต๊ะ\r\nพริกไทย 1/2 ช้อนชา\r\nเครื่องปรุงรสบะหมี่สำเร็จรูป 2 ห่อ\r\nซอสหอยนางรม 1+1/2 ช้อนโต๊ะ\r\nซีอิ้วดำหวาน 1/2 ช้อนโต๊ะ\r\nผักชี 5 กรัม\r\nคะน้า หรือกะหล่ำปลี 300 กรัม\r\n\r\nส่วนผสม หมักหมู\r\nหมูสันนอก 150 กรัม\r\nแป้งมัน 1/2 ช้อนโต๊ะ\r\nพริกไทย 1/4 ช้อนชา\r\nน้ำตาลทราย 1/2 ช้อนชา\r\nหอยนางรม 1 ช้อนโต๊ะ\r\nน้ำปลา 1/2 ช้อนโต๊ะ\r\nนมสด รสจืด 2 ช้อนโต๊ะ\r\nน้ำมันพืช 1 ช้อนโต๊ะ\r\n\r\nวิธีทำผัดมาม่า\r\n1.นำหมู สไลด์หมูบาง ๆ จากนั้น นำไปหมักด้วย ซอสหอยนางรม น้ำปลา น้ำตาลทราย น้ำมันพืช พริกไทยป่น แป้งมัน นมสด รสจืด คลุกเคล้าให้ส่วนผสมเข้ากัน หมักทิ้งไว้ 10-15 นาที เตรียมไว้\r\n2.จากนั้น นำมาม่าลงไปลวกในน้ำเดือด ลวกให้พอนิ่ม ประมาณ 1 นาที เสร็จแล้วนำเส้นมาคลุกเคล้ากับน้ำมันเจียว ที่ให้มาในซองมาม่า เตรียมไว้\r\n3.ตั้งกระทะ ใส่น้ำมันลงไปเล็กน้อย รอจนน้ำมันร้อน ใส่กระเทียม ลงไปผัดหอม\r\n4.จากนั้น ใส่หมูที่หมักไว้ลงไปผัด ใช้ไฟกลางค่อนแรง ผัดจนหมูเริ่มสุกประมาณ 50%\r\n5.ตามด้วย ตอกไข่ไก่ ลงไปด้านข้าง เร่งไฟแรงขึ้นมา ยีให้ไข่กระจายตัว รอจนสุกสัก 50% จากนั้น นำมาคลุกเคล้ากับหมู\r\n6.ใส่พริกไทย ลงไปเล็กน้อย พอหมูกับไข่เริ่มสุก ใส่เส้นมาม่าลงไป ผัดให้เข้ากันดี\r\n7.รอจนเส้นเริ่มร้อน ใส่ผักคะน้า หรือผักกะหล่ำลงไป ปรุงรสด้วย น้ำตาลทราย พริกไทยป่น ซอสหอยนางรม และเครื่องปรุงรสบะหมี่สำเร็จรูป\r\n8.ผัดให้เข้ากันกันดี แต่งสีด้วยซีอิ๊วดำหวานเล็กน้อย ผัดให้เข้ากัน ปิดเตา ตักใส่จาน ตกแต่งด้วยผักชี พร้อมเสิร์ฟ', 'maxresdefault.jpg', '2024-09-06 00:49:48', '2024-09-06 00:49:48', 2, 3),
(56, '@Admin', 'ต้มยำกุ้ง\r\n\r\nวัตถุดิบ\r\nกุ้ง 12 ตัว\r\nเห็ดฟาง 200 กรัม\r\nข่า 5 แว่น\r\nตะไคร้ 3 ต้น\r\nหอมแดง 20 กรัม\r\nรากผักชี 4 ราก\r\nเกลือ 1 ช้อนชา\r\nพริกขี้หนูสวน 2 ช้อนโต๊ะ\r\nน้ำปลา 80 กรัม\r\nน้ำพริกเผา 2 ช้อนโต๊ะ\r\nพริกแห้ง 5 เม็ด\r\nใบมะกรูด 6 ใบ\r\nนมข้นจืด 100 กรัม\r\nมะนาว 3-4 ลูก\r\nผักชีฝรั่ง 2 ต้น\r\nผักชี 2 ต้น\r\nน้ำเปล่า 2 ลิตร\r\n\r\nวิธีการทำ\r\nSTEP 1 : เตรียมเครื่องต้มยำ\r\nใช้มีดหั่นข่าเป็นแว่น ๆ\r\nฉีกใบมะกรูดออกจากก้านใบ เตรียมไว้\r\nใส่ตะไคร้ หอมแดง พริกขี้หนูสวน ลงไปในครก ทีละอย่าง ตำให้พอแหลก\r\nSTEP 2 : ทำน้ำต้มยำ\r\nตั้งหม้อ ใส่น้ำเปล่าลงไปให้ท่วม\r\nใส่ข่า ตะไคร้ ใบมะกรูด หอมแดง รากผักชี พริกขี้หนูสวน\r\nปรุงรสด้วย เกลือ เล็กน้อย\r\nปิดฝา ต้มน้ำให้เดือด\r\nSTEP 3 : ใส่เห็ดฟาง และ น้ำพริกเผา\r\nใส่เห็ดฟางลงไป ต้มให้สุก\r\nใส่น้ำพริกเผา คนให้ละลายเข้ากัน แล้วรอน้ำเดือดอีก 1  รอบ\r\nSTEP 4 : ใส่กุ้งลงไป\r\nเตรียมกุ้ง โดยแกะเปลือกกุ้งเฉพาะลำตัวออก ให้เหลือมันกุ้งส่วนหัวเอาไว้\r\nใส่ลงไปในหม้อ ต้มให้สุก\r\nSTEP 5 : ปรุงรส และ ใส่พริกแห้ง ใบมะกรูด\r\nใส่ น้ำปลา\r\nใส่ นมข้นจืด แล้ว คนให้ละลายเข้ากัน\r\nใส่พริกแห้งคั่ว\r\nใส่ใบมะกรูดฉีก แล้วต้มให้น้ำเดือดอีกรอบ เสร็จแล้ว ปิดเตา\r\nSTEP 6 : ใส่น้ำมะนาว ผักโรย ตามชอบ\r\nบีบน้ำมะนาว เพื่อเพิ่มรสเปรี้ยว\r\nใส่ผักชีฝรั่ง ผักชี ตามชอบ\r\nSTEP 7 : จัดเสิร์ฟใส่ชาม\r\nตักต้มยำกุ้งใส่ชาม\r\nจัดเสิร์ฟ เป็นอันเสร็จ', 'รป-หลก-ของ-สตร-ตมยำกง-นำขน-tom-yum-kung-creamy-river-prawn-spicy-soup-recipe-รสเดด-อรอยแซบ-จดจาด.jpg', '2024-09-06 00:52:07', '2024-09-06 00:52:07', 3, 3),
(57, '@Admin', 'ซาลาเปาหมูสับไข่เค็ม\r\n\r\nวัตถุดิบแป้งซาลาเปา\r\nแป้งเค้ก 350 กรัม\r\nยีสต์ 1 ช้อนโต๊ะ\r\nน้ำตาลทราย 1 ช้อนโต๊ะ\r\nน้ำอุ่น 200 มิลลิลิตร\r\nวัตถุดิบแป้งซาลาเปา\r\nแป้งเค้ก 150 กรัม\r\nผงฟู ½ ช้อนโต๊ะ\r\nน้ำเปล่า 50 มิลลิลิตร\r\nน้ำตาลทราย 100 กรัม\r\nเกลือ 1 ช้อนชา\r\nน้ำมันพืช 50 มิลลิลิตร\r\n\r\nวัตถุดิบไส้หมูสับไข่เค็ม\r\nหมูบด 500 กรัม\r\nรากผักชี 3 ราก\r\nกระเทียม 1 หัว\r\nพริกไทยเม็ด 1 ช้อนโต๊ะ\r\nซอสหอยนางรม 3 ช้อนโต๊ะ\r\nซอสปรุงรส 2 ช้อนโต๊ะ\r\nน้ำตาลทราย 1 ช้อนโต๊ะ\r\nน้ำมันงา 1 ช้อนโต๊ะ\r\nไข่ไก่ 1 ฟอง\r\nแป้งข้าวโพด 2 ช้อนโต๊ะ\r\nต้นหอมซอย ตามชอบ\r\nไข่แดงเค็ม ตามชอบ\r\n\r\nวิธีทําซาลาเปาหมูสับไข่เค็ม\r\nSTEP 1 : ทำแป้งเชื้อซาลาเปา\r\nผสมแป้งเค้ก ยีสต์ น้ำตาลทราย และน้ำอุ่นเข้าด้วยกัน\r\nนวดให้เข้ากันประมาณ 10 นาที จนแป้งเนียน คลุมด้วยพลาสติกคลุมอาหาร พักไว้อุณหภูมิห้องระยะเวลา 60 นาที\r\nSTEP 2 : ทำแป้งซาลาเปา\r\nโขลกรากผักชี กระเทียม และพริกไทยเข้าด้วยกัน จากนั้นตักใส่ในชามหมูสับ\r\nใส่ซอสหอยน้ำมันหอยลงไป ตามด้วยซอสปรุงรส น้ำตาลทราย น้ำมันงา แป้งข้าวโพด ต้นหอมซอย และไข่ไก่ คนให้เข้ากันแล้วพักไว้\r\nทำแป้งโดว์ โดยร่อนแป้งเค้ก ผงฟู และเกลือ เข้าด้วยกัน ใส่น้ำตาลทรายลงไป ตามด้วยน้ำเปล่า น้ำมันพืช และแป้งเชื้อที่หมักไว้ นวดให้เข้ากันประมาณ 10 นาที จนแป้งเนียน จากนั้นแบ่งเป็นก้อนละ 60 กรัม เตรียมห่อกับไส้\r\nSTEP 3 : นึ่งซาลาเปา\r\nคลึงแป้งให้แบน ใส่ไส้หมูสับลงไป ตามด้วยไข่แดงเค็ม จากนั้นห่อให้มิด แล้ววางบนกระดาษรอง\r\nนำไปนึ่งบนน้ำเดือด 20 นาที เพียงเท่านี้ “ซาลาเปาไส้หมูสับไข่เค็ม” ของเราก็พร้อมกินแล้วจ้า!\r\nTIPS : เวลาที่ใช้นึ่งขึ้นอยู่กับขนาดของซาลาเปา', 'ไข่เค็ม.jpg', '2024-09-06 00:54:25', '2024-09-06 00:54:25', 4, 3),
(58, '@Jimmy', 'เนื้อน่องลายตุ๋น\r\n\r\nส่วนผสม เนื้อน่องลายตุ๋น\r\n\r\n     • เนื้อน่องลาย 700 กรัม\r\n     • หอมหัวใหญ่ 4 หัว\r\n     • มะเขือเทศ 4 ลูก\r\n     • มันฝรั่ง (หัวขนาดเท่ากำปั้น) 5 หัว\r\n     • ขึ้นฉ่าย 4-5 ต้น\r\n     • กระเทียมกลีบใหญ่ 5-6 กลีบ\r\n     • พริกไทยดำ 2 ช้อนชา\r\n     • เกลือป่น 3/4 ช้อนโต๊ะ\r\n     • ซอสถั่วเหลือง 2 ทัพพี\r\n     • ซีอิ๊วขาว 1 ทัพพี\r\n     • น้ำสะอาด\r\n\r\nวิธีทำเนื้อน่องลายตุ๋น\r\n\r\n     1. โขลกกระเทียมกับพริกไทยรวมกันแล้วพักไว้\r\n     2. ตั้งหม้อใส่น้ำประมาณ 1/2 ของหม้อ เติมเกลือลงไป ใส่เนื้อน่องลาย ใช้ไฟกลางค่อนไปทางแรง ปิดฝารอให้เดือด ช้อนฟองทิ้ง เบาไฟลงนิดหนึ่ง ตักชิ้นเนื้อออกมาใส่จานพักไว้ให้คลายร้อน เวลาหั่นจะได้ไม่ร้อนมาก\r\n     3. ปรับไฟเตาให้แรงปานกลาง ใส่กระเทียมกับพริกไทยโขลก ปรุงรสด้วยซีอิ๊วขาวกับซอสถั่วเหลือง ตั้งไฟไปเรื่อย ๆ ปล่อยให้เดือดเบา ๆ\r\n\r\n\r\n     4. พอเนื้อคลายร้อนแล้วหั่นเนื้อเป็นชิ้นพอดีคำ ใส่กลับลงไปในหม้อน้ำซุป เร่งไฟขึ้นอีกนิด ปิดฝาแล้วรอให้เดือดพล่านสักพัก ใส่มันฝรั่ง หอมหัวใหญ่ และมะเขือเทศ ปิดฝา รอให้เดือดอีกครั้ง ใส่ขึ้นฉ่าย เบาไฟลงเพื่อที่น้ำจะได้ไม่เดือดพล่านจนเกินไปเพราะจะทำให้น้ำซุปขุ่นข้นเกินไป เผยอฝาหม้อขึ้นเล็กน้อย แล้วก็ปล่อยให้เดือดเบา ๆ ไปเรื่อย ๆ ประมาณ 30 นาทีถึง 1 ชั่วโมง หรือจนเปื่อยตามชอบ ตักใส่ถ้วย', 'น่องลาย.jpg', '2024-09-06 00:57:38', '2024-09-06 00:57:38', 5, 5),
(109, 'Jordan', 'ส่วนผสม\r\nสำหรับ 1ท่าน\r\nสามชั้น1ชิ้น\r\nใบกระเพรา50กรัม\r\nไข่ไก่1ฟอง\r\nกระเทียม1-2กลีบ\r\nน้ำตาลทราย1ช้อนโต๊ะ\r\nน้ำมันหอย2ช้อนชา\r\nน้ำมันพืช2ช้อนชา\r\nพริกขี้หนู5เม็ด\r\nน้ำซุป3ช้อนชา\r\nถั่วฝักยาวหั่น2เส้น\r\nน้ำปลา2ช้อนชา\r\nวิธีทำ\r\nเวลาเตรียมส่วนผสม: 30 นาที\r\nเวลาปรุงอาหาร: 20 นาที\r\n1\r\nโคลกกระเทียมและพริกขี้หนูให้เข้ากัน พอละเอียด\r\n\r\n2\r\nนำกระเทียมและพริกขี้หนูตำใส่ลงไปในกระทะที่มีน้ำมันพืชลงไปผัด พอได้ที่\r\n\r\n3\r\nเตรียมหมูกรอบ\r\n\r\n4\r\nนำหมูกรอบลงไปผัดในน้ำพริกที่เตรียมไว้\r\n\r\n5\r\nใส่น้ำตาลทรายลงไปในหมูกรอบ\r\n\r\n6\r\nใส่น้ำซุปลงไปเพื่อไม่ให้แห้ง\r\n\r\n7\r\nใส่น้ำมันหอยลงไป\r\n\r\n8\r\nใส่ถั่วฝักยาวแล้วผัดเครื่องปรุงให้เข้ากัน\r\n\r\n9\r\nใส่ใบกระเพราลงไป\r\nเคล็ดลับ: ไม่ควรใช้ไฟอ่อนเพราะอาจทำให้ใบกระเพราจะเหลือง\r\n\r\n10\r\nตอกไข่ลงไปในน้ำมันร้อนๆ\r\nเคล็ดลับ: ไม่ควรใช้ไฟอ่อนในการทอดไข่ดาว\r\n\r\n11\r\nทอดไข่จนไหม้ฟู\r\n\r\n12\r\nวางไข่ลงไปในจานพร้อมข้าวหอมมะลิสวยๆ\r\n\r\n13\r\nจัดจานพร้อมรับประทานได้เลยค่า', 'หมูกรอบ.jpg', '2024-10-21 09:36:45', '2024-10-21 09:36:45', 2, 7),
(112, 'Jordan', 'แค่เห็นก็กลืนน้ำลายแล้วกับเมนู “ต้มยำหมูสับไข่น้ำ” ที่คนรักไข่น้ำและความแซ่บต้องถูกใจ เป็นส่วนผสมที่ลงตัวแบบไม่น่าเชื่อ รสชาติจัดเต็มความจัดจ้าน หุงข้าวร้อน ๆ สักหม้อแล้วกินคู่กัน เด็ดโดนไม่ยอมให้แย่ง! ถ้าเพื่อน ๆ พร้อมแล้ว เรามาเตรียมวัตถุดิบกันเลยยย\r\n\r\nวัตถุดิบ\r\nหมูสับ 300 กรัม  \r\nไข่ไก่ 3 ฟอง \r\nน้ำมันหอย 1 ช้อนโต๊ะ  \r\nพริกป่น 1 ช้อนโต๊ะ  \r\nน้ำมะนาว 3 ช้อนโต๊ะ  \r\nน้ำตาล 1/2 ช้อนโต๊ะ  \r\nถั่วลิสงคั่วบด 5 ช้อนโต๊ะ  \r\nน้ำปลา 5 ช้อนโต๊ะ  \r\nน้ำพริกเผา 1 ช้อนโต๊ะ  \r\nผงปรุงรส 1/2 ช้อนโต๊ะ  \r\nน้ำ 700 กรัม \r\nกระเทียม 3 กลีบ  \r\nผักชี ตามชอบ  \r\nต้นหอม ตามชอบ\r\nวิธีทำ\r\nSTEP 1 : ทอดไข่\r\nในชามผสมตอกไข่ลงไป เติมน้ำมันหอย ตีให้เข้ากัน\r\nตั้งกระทะไฟกลาง ใส่น้ำมันลงไป ทอดไข่จนสุก\r\nเมื่อไข่เย็นลงแล้ว หั่นเป็นชิ้น ๆ พักไว้\r\n\r\nSTEP 2 : ทำต้มยำ\r\nตั้งกระทะไฟกลางใส่น้ำมันเล็กน้อย นำกระเทียมลงไปผัดจนหอม จากนั้นใส่หมูสับลงไป ผัดจนหมูสับเริ่มแห้งลง\r\nเติมน้ำเปล่าลงไป ปรุงรสด้วยผงปรุงรส น้ำตาล พริกป่น น้ำปลา น้ำพริกเผาและถั่วลิสงคั่วป่น คนให้เข้ากัน\r\nเมื่อเข้ากันแล้ว เติมไข่ที่ทอดไว้ลงไป คนให้เข้ากัน แล้วบีบมะนาว\r\n\r\nSTEP 3 : จัดเสิร์ฟ\r\nใส่ต้มยำหมูสับไข่น้ำลงในถ้วย โรยด้วยต้นหอมและผักชีตามชอบ พร้อมเสิร์ฟ\r\n', '5928eb26d42f4a3d93b3522f23a44192[1].jpg', '2024-10-21 09:40:57', '2024-10-21 09:40:57', 3, 7),
(114, 'Jordan', 'ข้าวผัดไข่ โฮมเมด', 'ข้าวผัดไข่โฮมเมดเมนูตอนไปโรงเรียนที่แม่ชอบทำให้กิน _ farmiscooking.mp4', '2024-10-21 09:50:50', '2024-10-21 09:50:50', 2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(102, 51, 5, '2024-09-12 16:06:18'),
(104, 52, 5, '2024-09-12 16:13:03'),
(199, 52, 3, '2024-09-26 19:58:58'),
(201, 51, 3, '2024-09-26 19:59:10'),
(211, 55, 3, '2024-09-26 20:03:56'),
(220, 56, 3, '2024-09-26 20:07:06'),
(221, 57, 3, '2024-09-26 20:07:09'),
(248, 54, 3, '2024-10-23 14:06:31'),
(256, 58, 3, '2024-10-24 07:42:57');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `report_title` varchar(255) NOT NULL,
  `report_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `post_id`, `report_title`, `report_description`, `created_at`) VALUES
(7, 60, 'Spam', 'jhjhhjhjhjj', '2024-09-26 17:40:31'),
(9, 58, 'Spam', 'wwwwwwwwwwwwwwwww', '2024-09-26 18:08:54'),
(10, 60, 'Spam', 'dddd', '2024-10-15 08:16:32'),
(11, 51, 'Spam', '', '2024-10-22 15:48:04'),
(12, 112, 'Spam', '', '2024-10-22 19:35:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.jpg',
  `theme` enum('light','dark') DEFAULT 'light',
  `role` enum('user','admin') DEFAULT 'user',
  `bio` text DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Id`, `firstName`, `lastName`, `email`, `password`, `profile_picture`, `theme`, `role`, `bio`, `username`) VALUES
(3, 'Phittiphong', 'Phansaensao', 'admin@mail.com', '1234', 'catttt.gif', 'light', 'admin', 'เมี๊ยวววววว', 'Admin'),
(5, 'Seksan', 'KIKI', 'test@mail.com', '1234', 'cattttttttt.jpg', 'light', 'user', NULL, 'Jimmy'),
(7, 'Jordy', 'Ponya', 'John@mail.com', '1234', '96018_xampp_icon.png', 'light', 'user', '', 'Jordan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `campaign`
--
ALTER TABLE `campaign`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`follower_id`),
  ADD KEY `follower_id` (`follower_id`);

--
-- Indexes for table `following`
--
ALTER TABLE `following`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_posts_category` (`category_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `campaign`
--
ALTER TABLE `campaign`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `following`
--
ALTER TABLE `following`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`Id`);

--
-- Constraints for table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`follower_id`) REFERENCES `users` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `following`
--
ALTER TABLE `following`
  ADD CONSTRAINT `following_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `following_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`Id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
