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
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `unique_id` int(11) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `car_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `location` varchar(100) NOT NULL,
  `transmission` varchar(20) NOT NULL,
  `seats` varchar(10) NOT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `available_from` date NOT NULL,
  `available_until` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `status` varchar(50) NOT NULL DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `user_id`, `unique_id`, `make`, `model`, `year`, `car_type`, `description`, `daily_rate`, `location`, `transmission`, `seats`, `features`, `available_from`, `available_until`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(4, 3, 0, 'Toyota', 'Carmry', 2021, 'SUV', 'akong gi testingan', 50.00, 'Cebu', 'Manual', '4', '[\"bluetooth\",\"camera\"]', '2025-04-14', '2025-04-15', 1, 'Available', '2025-04-14 15:30:54', '2025-04-24 06:00:15'),
(5, 3, 0, 'Mercedes', 'Mayback', 2025, 'Electric', 'This a mecedes mayback good condition kaayo ni ', 120.00, 'Cebu, Mingla', 'Manual', '4', '[\"bluetooth\",\"camera\",\"android\",\"sunroof\"]', '2025-04-15', '2025-04-17', 1, 'Available', '2025-04-14 16:47:52', '2025-04-24 06:08:53'),
(7, 3, 0, 'Mercedes', 'AMG C63', 2025, 'Electric', 'This is Mercedes-AMG C63 good condition!', 300.00, 'Cebu, Minglanilla', 'Manual', '4', '[\"bluetooth\",\"camera\",\"sunroof\",\"keyless\"]', '2025-04-16', '2025-04-17', 1, 'Available', '2025-04-16 13:46:26', '2025-04-24 05:56:34'),
(8, 3, 0, 'BMW', '3 Series', 2022, 'Gasoline', 'Luxury sedan with sporty handling and premium features. Great for business trips or special occasions.', 110.00, 'Los Angeles, CA', 'Automatic', '5', '[\"sunroof\"]', '2025-04-18', '2025-04-18', 1, 'Booked', '2025-04-16 18:42:47', '2025-04-24 06:10:55'),
(9, 3, 0, 'Tesla', 'Model 3', 2021, 'Electric', 'Sleek electric car with autopilot features and great range. Perfect for city driving and weekend getaways', 85.00, 'San Francisco, CA', 'Automatic', '5', '[\"camera\",\"android\",\"keyless\"]', '2025-04-18', '2025-04-19', 1, 'Available', '2025-04-16 18:49:05', '2025-04-24 06:08:50'),
(10, 3, 0, 'Toyota', 'RAV4', 2021, 'Gasoline', 'Reliable and fuel-efficient SUV, perfect for family trips and outdoor adventures.', 75.00, 'Seattle, WA', 'Automatic', '5', '[\"bluetooth\",\"camera\"]', '2025-04-20', '2025-04-21', 1, 'Available', '2025-04-16 19:02:53', '2025-04-24 05:56:37'),
(11, 2, 0, 'Honda', 'Civic', 2021, 'Gasoline', 'Reliable and fuel-efficient sedan, perfect for city driving and daily commutes.', 65.00, 'Chicago, IL', 'Automatic', '4', '[\"air-condition\",\"navigation-system\",\"apple-carplay\",\"bluetooth\",\"leather-seats\",\"camera\",\"android\"]', '2025-04-19', '2025-04-20', 1, 'Available', '2025-04-19 11:09:58', '2025-04-24 06:15:07'),
(12, 2, 0, 'Ford', 'Mustang Convertible', 2022, 'Galosine', 'Classic American muscle car with convertible top. Perfect for coastal drives and making an impression.', 120.00, 'Miami, FL', 'Automatic', '4', '[\"air-condition\",\"heated-seats\",\"leather-seats\",\"sunroof\",\"sound-system\"]', '2025-04-21', '2025-04-22', 1, 'Available', '2025-04-19 11:36:33', '2025-04-24 06:15:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
