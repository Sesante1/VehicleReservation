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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `unique_id` int(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `unique_id`, `fname`, `lname`, `email`, `password`, `img`, `status`, `created_at`) VALUES
(1, 874659974, 'testing', 'tseting', 'testing@gmail.com', '25d55ad283aa400af464c76d713c07ad', '1741793173bg-image.png', 'Active now', '2025-04-24 18:42:27'),
(2, 124135190, 'Sarah', 'Miller', 'doe@email.com', '25d55ad283aa400af464c76d713c07ad', '1741793309profile-pic.png', 'Active now', '2025-04-24 18:44:00'),
(3, 1285939110, 'jhon', 'doe', 'otlom@email.com', '25d55ad283aa400af464c76d713c07ad', '1741793490girl-bg.png', 'Active now', '2025-04-24 18:43:21'),
(4, 304972074, 'asdf', 'asd', 'asdf@email.com', '09d10ea1b73944c406a4c0717c54a4aa', '1741793926Screenshot 2024-02-06 131725.png', 'Offline now', '2025-04-16 14:57:18'),
(8, 656372565, 'testing', 'testing', 'testing@email.com', '25d55ad283aa400af464c76d713c07ad', '1744186820jogjakarta.png', 'Offline now', '2025-04-16 14:57:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
