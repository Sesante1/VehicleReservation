-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2025 at 09:17 PM
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
-- Database: `chatapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `msg_id` int(11) NOT NULL,
  `incoming_msg_id` int(255) NOT NULL,
  `outgoing_msg_id` int(255) NOT NULL,
  `msg` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`msg_id`, `incoming_msg_id`, `outgoing_msg_id`, `msg`) VALUES
(1, 874659974, 124135190, 'wassp'),
(2, 1285939110, 124135190, 'hoi'),
(3, 124135190, 1285939110, 'hoi sad'),
(4, 124135190, 1285939110, 'asdf'),
(5, 1285939110, 124135190, 'hi'),
(6, 124135190, 1285939110, 'hello'),
(7, 1285939110, 124135190, 'testing'),
(8, 124135190, 1285939110, 'testing'),
(9, 1285939110, 124135190, 'asdfasdfasdf'),
(10, 1285939110, 124135190, 'asdfasdf'),
(11, 1285939110, 124135190, 'asdfasdf'),
(12, 1285939110, 124135190, 'asdfasdf'),
(13, 124135190, 1285939110, 'asdfasdf'),
(17, 874659974, 124135190, 'sdfg'),
(18, 874659974, 124135190, 'sdfgdsfg'),
(19, 656372565, 1285939110, 'Hellow'),
(20, 1285939110, 656372565, 'Hi'),
(24, 124135190, 1285939110, 'a'),
(29, 124135190, 1285939110, 'asdf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`msg_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
