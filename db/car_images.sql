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
-- Table structure for table `car_images`
--

CREATE TABLE `car_images` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_images`
--

INSERT INTO `car_images` (`id`, `car_id`, `image_path`, `is_primary`, `created_at`) VALUES
(4, 4, '67fd2a2e46ac5.png', 1, '2025-04-14 15:30:54'),
(5, 4, '67fd2a2e46d77.png', 0, '2025-04-14 15:30:54'),
(6, 4, '67fd2a2e46fd6.png', 0, '2025-04-14 15:30:54'),
(7, 5, '67fd3c38b67ff.avif', 1, '2025-04-14 16:47:52'),
(8, 5, '67fd3c38b9ac4.jpg', 0, '2025-04-14 16:47:52'),
(9, 5, '67fd3c38bb811.avif', 0, '2025-04-14 16:47:52'),
(13, 7, '67ffb4b20cffd.jpg', 1, '2025-04-16 13:46:26'),
(14, 7, '67ffb4b20d424.avif', 0, '2025-04-16 13:46:26'),
(15, 7, '67ffb4b20d6c8.jpg', 0, '2025-04-16 13:46:26'),
(16, 8, '67fffa27247b2.jpg', 1, '2025-04-16 18:42:47'),
(17, 8, '67fffa2728eff.jpg', 0, '2025-04-16 18:42:47'),
(18, 8, '67fffa272ae9b.jpg', 0, '2025-04-16 18:42:47'),
(19, 9, '67fffba16b53e.jpg', 1, '2025-04-16 18:49:05'),
(20, 9, '67fffba16ba10.jpg', 0, '2025-04-16 18:49:05'),
(21, 9, '67fffba16de5f.webp', 0, '2025-04-16 18:49:05'),
(22, 10, '67fffedda918c.jpg', 1, '2025-04-16 19:02:53'),
(23, 10, '67fffeddab4cd.jpg', 0, '2025-04-16 19:02:53'),
(24, 10, '67fffeddad8a2.jpg', 0, '2025-04-16 19:02:53'),
(25, 10, '67fffeddadece.jpg', 0, '2025-04-16 19:02:53'),
(26, 11, '6803848666d3d.avif', 1, '2025-04-19 11:09:58'),
(27, 11, '6803848667490.jpg', 0, '2025-04-19 11:09:58'),
(28, 11, '6803848667795.webp', 0, '2025-04-19 11:09:58'),
(29, 12, '68038ac193047.avif', 1, '2025-04-19 11:36:33'),
(30, 12, '68038ac195595.webp', 0, '2025-04-19 11:36:33'),
(31, 12, '68038ac197782.jpg', 0, '2025-04-19 11:36:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `car_images`
--
ALTER TABLE `car_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `car_images`
--
ALTER TABLE `car_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car_images`
--
ALTER TABLE `car_images`
  ADD CONSTRAINT `car_images_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
