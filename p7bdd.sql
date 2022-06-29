-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 29, 2022 at 12:12 PM
-- Server version: 5.7.24
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `p7bdd`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `brand`, `model`, `price`) VALUES
(1, 'apple', 'iphone 12', 1200),
(2, 'samsung', 'galaxy', 500),
(3, 'apple', 'iphone 10', 1000),
(4, 'samsung', 'galaxy A9', 850);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `id_user_id` int(11) NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `id_user_id`, `user_name`, `password`) VALUES
(2, 7, 'tim', '$2y$10$sl/6RjGT.h7nogKTcf1NdOujKrIQXQlhR.PUq15B/Uyaw7xRxFtwW'),
(5, 7, 'billy', '$2y$10$gN8Ziq80cs9bbKKI83ldYeGcyc5c.1ltBO0CkM9l4C0lcv6u669cG'),
(6, 7, 'lucie', '$2y$10$aNPXqeN6n6g8/QOGa9K2zOpZditS5.CL8BXsbzvhc5V8kmQX5YVdC'),
(13, 4, 'maela', '$2y$10$abzfJfQCGlHKsvWZLFOtUuRzVp4yy6oUX81OTAe.zQc2YTI2staxq'),
(16, 4, 'martin', '$2y$10$OIUdziRWfRjbd0B5xkVUz.Y3ivNphMyJopCBn0LRBSqvGWd0/9KYG');

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20220316091052', '2022-03-16 09:11:06', 42),
('DoctrineMigrations\\Version20220317153947', '2022-03-17 15:40:03', 48),
('DoctrineMigrations\\Version20220330134446', '2022-03-30 13:44:57', 94),
('DoctrineMigrations\\Version20220331075502', '2022-03-31 07:55:12', 104),
('DoctrineMigrations\\Version20220331080301', '2022-03-31 08:03:07', 190),
('DoctrineMigrations\\Version20220331140119', '2022-03-31 14:01:25', 55),
('DoctrineMigrations\\Version20220609125949', '2022-06-09 13:00:32', 49),
('DoctrineMigrations\\Version20220615074322', '2022-06-15 07:44:36', 109),
('DoctrineMigrations\\Version20220615135940', '2022-06-15 13:59:44', 177),
('DoctrineMigrations\\Version20220615141555', '2022-06-15 14:16:00', 171),
('DoctrineMigrations\\Version20220615143215', '2022-06-15 14:32:23', 45),
('DoctrineMigrations\\Version20220615143417', '2022-06-15 14:34:21', 60);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `roles`) VALUES
(4, 'some user', '$2y$13$kylP7xftPMN9DexwiOP03ON.DYqzLlGZ6LzXbl.YmUI3G0MYE6.ha', '[]'),
(7, 'main', '$2y$13$RMa.zVmStzqxV4DWlj9rz.Sdui9ocp9XyOVKm0JgJBL0JsHMmfMBC', '[]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_81398E0924A232CF` (`user_name`),
  ADD KEY `IDX_81398E0979F37AE5` (`id_user_id`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `FK_81398E0979F37AE5` FOREIGN KEY (`id_user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
