-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 11:33 AM
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
-- Database: `photo_gallery`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Mountains'),
(2, 'City Lights'),
(3, 'Desert'),
(4, 'Ocean'),
(5, 'Nature'),
(6, 'Sunset'),
(7, 'Architecture'),
(8, 'Wildlife');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'K', 'k@gmail.com', 'jsbdh vjdkjsnaiojdwuewjdns', '2025-05-21 13:11:21'),
(2, 'K', 'k@gmail.com', 'jsbdh vjdkjsnaiojdwuewjdns', '2025-05-21 13:13:12'),
(3, 'K', 'k@gmail.com', 'jsbdh vjdkjsnaiojdwuewjdns', '2025-05-21 13:13:13'),
(4, 'K', 'k@gmail.com', 'jsbdh vjdkjsnaiojdwuewjdns', '2025-05-21 13:13:21'),
(5, 'k', 'k@gmail.com', 'bubovbobvobd', '2025-05-21 13:13:31'),
(6, 'k', 'k@gmail.com', 'bubovbobvobd', '2025-05-21 13:15:06'),
(7, 'keto', 'keto@gmail.com', 'knoia', '2025-05-21 13:19:09'),
(8, 'keto', 'keto@gmail.com', 'knoia', '2025-05-21 13:19:22'),
(9, 'keto', 'keto@gmail.com', 'knoia', '2025-05-21 13:19:25'),
(10, 'keto', 'keto@gmail.com', 'nsnvsid', '2025-05-21 13:20:11'),
(11, 'keto', 'keto@gmail.com', 'nsnvsid', '2025-05-21 13:21:21'),
(12, 'keto', 'keto@gmail.com', 'nsnvsid', '2025-05-21 13:21:34');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`id`, `user_id`, `category_id`, `title`, `description`, `image_path`, `created_at`) VALUES
(1, 3, 1, 'Mountains', 'Beautiful mountain range photo', 'images/Photo1.avif', '2025-05-20 10:13:51'),
(2, 3, 2, 'City Lights', 'Night view of a bustling city', 'images/Photo2.jpeg', '2025-05-20 10:13:51'),
(3, 3, 3, 'Desert', 'Sandy dunes and desert vibes', 'images/Photo3.jpg', '2025-05-20 10:13:51'),
(4, 3, 4, 'Ocean', 'Deep blue ocean view', 'images/Photo4.jpg', '2025-05-20 10:13:51'),
(5, 3, 5, 'Nature', 'Green forest and wildlife', 'images/Photo5.jpeg', '2025-05-20 10:13:51'),
(6, 3, 6, 'Sunset', 'Colorful sky at sunset', 'images/Photo6.jpg', '2025-05-20 10:13:51'),
(7, 3, 7, 'Architecture', 'Iconic urban architecture', 'images/Photo7.jpeg', '2025-05-20 10:13:51'),
(8, 3, 8, 'Wildlife', 'Animals in their natural habitat', 'images/Photo8.jpeg', '2025-05-20 10:13:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `is_admin`) VALUES
(1, 'sacsasc', 'asc@gn.c', '$2y$10$TPQjxNJ2YIeMqz63pqA0C.tJiT7o77dxlPC4OGMyWWZ1zp8VumdYW', 'user', 0),
(3, 'Keto', 'keti@gmail.com', '$2y$10$AAruOa/i.MTBOWWC8lq7P.u6HCHBN7.1TKUnnM2vtUI9XZ0KqHWhy', 'admin', 1),
(4, 'k', 'k@gmail.com', '$2y$10$/Ope/jrnJkPRVKpDV6aDIOrpUx/F.CJY6MFPk.9n7Tmr91R94htMK', 'user', 0),
(5, 'kk', 'kk@gmail.com', '$2y$10$VckPKuMYWNF/G./c4AtuKOO7qvUEi1f8hr./gjF2w3R2UYaeBjFSG', 'user', 0),
(6, 'Keto', 'keto@gmail.com', '$2y$10$Bc1.BK/NsHQqhSxjabWk4O8CkM7CWNfgr/wD8rtB7O1.RjISfQIv6', 'user', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `photo_id` (`photo_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `photos_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
