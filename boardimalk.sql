-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2024 at 05:03 PM
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
-- Database: `boardimalk`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `rental_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `customer_id`, `rental_id`, `rating`, `feedback`, `created_at`) VALUES
(1, 3, 1, 4, 'good place', '2024-10-24 05:50:00'),
(2, 3, 4, 1, 'bad neighborhood', '2024-10-24 05:51:24'),
(3, 3, 4, 5, 'good place', '2024-10-24 07:30:29'),
(4, 3, 1, 1, 'not clean', '2024-10-24 07:40:51'),
(5, 3, 1, 3, 'price okay', '2024-10-24 14:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `renter_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `full_address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT NULL,
  `is_furnished` tinyint(1) DEFAULT NULL,
  `has_garden` tinyint(1) DEFAULT NULL,
  `has_kitchen` tinyint(1) DEFAULT NULL,
  `is_air_conditioned` tinyint(1) DEFAULT NULL,
  `separate_utility_bills` tinyint(1) DEFAULT NULL,
  `has_parking` tinyint(1) DEFAULT NULL,
  `has_security_cameras` tinyint(1) DEFAULT NULL,
  `proximity_to_road` varchar(100) DEFAULT NULL,
  `contact_whatsapp` varchar(20) DEFAULT NULL,
  `images` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `removed_by_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`id`, `renter_id`, `title`, `description`, `full_address`, `city`, `district`, `price`, `rooms`, `bathrooms`, `is_furnished`, `has_garden`, `has_kitchen`, `is_air_conditioned`, `separate_utility_bills`, `has_parking`, `has_security_cameras`, `proximity_to_road`, `contact_whatsapp`, `images`, `rating`, `feedback`, `created_at`, `is_active`, `removed_by_admin`) VALUES
(1, 1, 'Boarding room for rent', 'Friendly neighbourhood. Calm environment. Grocery shops near by. 500m to 120 Horana-Colombo bus route.', '291/s, 3rd lane, mandawila road, piliyandala', 'piliyandala', 'Colombo', 15000.00, 1, 1, 1, 0, 0, 0, 1, 0, 0, '500m', '0718760054', '../RESOURCES/uploads/6712aa399405b-pic3.jpg,../RESOURCES/uploads/6712aa39941f2-pic5.jpg,../RESOURCES/uploads/6712aa3994355-pic4.jpg,../RESOURCES/uploads/6712aa39944af-pic2.jpg,../RESOURCES/uploads/6712aa399460c-pic1.jpg', 2.67, NULL, '2024-10-18 18:34:33', 1, 0),
(2, 1, 'Boading house for rent', 'entire floor for rent. 2nd floor of a 2 storied building', '291/s, 3rd lane, mandawila road, piliyandala', 'piliyandala', 'Colombo', 75000.00, 3, 2, 1, 1, 0, 0, 1, 1, 0, '500m', '0718760054', '../RESOURCES/uploads/6712b0b0b2bad-pic3.jpg,../RESOURCES/uploads/6712b0b0b2ffb-pic5.jpg,../RESOURCES/uploads/6712b0b0b3359-pic4.jpg,../RESOURCES/uploads/6712b0b0b36ba-pic2.jpg,../RESOURCES/uploads/6712b0b0b3985-pic1.jpg', NULL, NULL, '2024-10-18 19:02:08', 0, 1),
(3, 1, 'Separate boarding house for rent', '', '31/s, 2nd street, Modara', 'Modara', 'Colombo', 90000.00, 4, 2, 1, 1, 1, 1, 1, 1, 0, '50m', '0718760054', '../RESOURCES/uploads/6712b1b07168f-pic3.jpg,../RESOURCES/uploads/6712b1b07198a-pic5.jpg,../RESOURCES/uploads/6712b1b071c6c-pic4.jpg,../RESOURCES/uploads/6712b1b07200e-pic2.jpg,../RESOURCES/uploads/6712b1b072394-pic1.jpg', NULL, NULL, '2024-10-18 19:06:24', 0, 0),
(4, 1, 'Boarding room for girls', '', '56/4, highlevel road, nugegoda', 'nugegoda', 'Colombo', 18000.00, 1, 1, 1, 0, 0, 0, 1, 0, 0, '10m', '0765301376', '../RESOURCES/uploads/6712c71f0dc4a-pic3.jpg,../RESOURCES/uploads/6712c71f0df91-pic5.jpg,../RESOURCES/uploads/6712c71f0e392-pic4.jpg,../RESOURCES/uploads/6712c71f0e702-pic2.jpg,../RESOURCES/uploads/6712c71f0ea3f-pic1.jpg', 3.00, NULL, '2024-10-18 20:37:51', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `saved_properties`
--

CREATE TABLE `saved_properties` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_properties`
--

INSERT INTO `saved_properties` (`id`, `user_id`, `property_id`) VALUES
(1, 3, 4),
(2, 3, 1),
(3, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('renter','customer','admin') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'Thanuri', '$2y$10$ViAtJimp2bc8wc/BhmEB/u9AI161GsCa367wzZAVJJnYqZLZhZ25O', 't@gmail.com', 'renter', '2024-10-18 16:33:39'),
(2, 'Admin', '$2b$12$pk3EbF1mQ1fvyDT0XJhLOeG/An.OaGWnIJW3rCYnY5k7M8iMyedOK', 'admin@gmail.com', 'admin', '2024-10-19 04:25:03'),
(3, 'Michelle', '$2y$10$8CqNJwT2aobyigVGGlPhWOTemuIt6KPmHzeMU8ezFADGz/c2TwRCm', 'm@gmail.com', 'customer', '2024-10-23 19:01:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `rental_id` (`rental_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `renter_id` (`renter_id`);

--
-- Indexes for table `saved_properties`
--
ALTER TABLE `saved_properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `saved_properties`
--
ALTER TABLE `saved_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`);

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`renter_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
