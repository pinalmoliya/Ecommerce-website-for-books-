-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2025 at 01:02 PM
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
-- Database: `ebookhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `books_for_sale`
--

CREATE TABLE `books_for_sale` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `condition_status` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books_for_sale`
--

INSERT INTO `books_for_sale` (`id`, `title`, `author`, `condition_status`, `price`, `image`, `created_at`) VALUES
(2, 'randhir', 'pinal', 'new', 266.00, '1753506786_logo.png', '2025-07-26 05:13:06'),
(3, 'miss word', 'pinal', 'new', 266.00, '1753516863_i1.jpg', '2025-07-26 08:01:03'),
(4, 'miss word', 'pinal', 'Used - Like New', 400.00, '1755363787_Screenshot1.png', '2025-08-16 17:03:07');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `buyer_name` varchar(255) NOT NULL,
  `buyer_email` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `book_id`, `buyer_name`, `buyer_email`, `payment_method`, `status`, `created_at`) VALUES
(1, 3, 'pinal', 'pinalmoliya24@gmail.com', 'UPI', 'Pending', '2025-08-17 08:54:21'),
(2, 3, 'pinal', 'pinalmoliya24@gmail.com', 'UPI', 'Pending', '2025-08-17 08:54:32'),
(3, 2, 'pinal', 'pinalmoliya24@gmail.com', 'UPI', 'Pending', '2025-08-17 09:04:35'),
(4, 2, 'pinal', 'pinalmoliya24@gmail.com', 'UPI', 'Pending', '2025-08-17 09:09:27'),
(5, 2, 'pinal', 'pinalmoliya24@gmail.com', 'UPI', 'Pending', '2025-08-17 09:10:07'),
(6, 4, 'pinal', 'pinalmoliya2005@gmail.com', 'UPI', 'Pending', '2025-08-17 10:31:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `created_at`) VALUES
(1, 'moliya pinal', 'pinalmoliya24@gmail.com', '$2y$10$CTT/6qQvx/zGG9XXqCNE/umQBj/Ep3o704x0G5./tcdsiyANhNIMm', '2025-07-26 08:21:15'),
(2, 'pinal', 'pinal@gmail.com', '$2y$10$ANlO6yM.LLooWYaXw.i42umWfD/VeLduYn88pkS9QbjLd5G7FhLPe', '2025-07-26 08:37:36'),
(3, 'moliya pinal chandubhai', 'pinalmoliya@gmail.com', '$2y$10$VWTq4QA5f8KpaJJob8uT9eo3rYPcMgK8yPcXg1Ky/Hgve.mE41mCK', '2025-08-02 06:34:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books_for_sale`
--
ALTER TABLE `books_for_sale`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books_for_sale`
--
ALTER TABLE `books_for_sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books_for_sale` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
