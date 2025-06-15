-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Jun 15, 2025 at 10:32 AM
-- Server version: 11.5.2-MariaDB-ubu2404
-- PHP Version: 8.2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id_category` int(11) NOT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id_category`, `category`) VALUES
(1, 'men'),
(2, 'women');

-- --------------------------------------------------------

--
-- Table structure for table `limit_access`
--

CREATE TABLE `limit_access` (
  `id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `restriction_time` int(11) NOT NULL,
  `failed_tries` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `id_category` int(11) NOT NULL,
  `image` varchar(150) DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `id_category`, `image`, `price`) VALUES
(1, 'T-shirt', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras euismod felis nec nulla tristique scelerisque. Quisque blandit feugiat ullamcorper. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc hendrerit lorem vel tempor lacinia. Proin blandit blandit maximus. Donec blandit orci a massa luctus posuere. Maecenas sem metus, vestibulum quis maximus et, accumsan eget turpis. Phasellus imperdiet ac eros ut ultricies. Etiam scelerisque sodales sapien, et posuere risus dapibus ut. Donec ac libero facilisis, malesuada orci eu, faucibus risus.', 1, '/uploads/images/1704318259-t-shirt01.png', 13.50),
(2, 'T-shirt 02', 'Pellentesque ut mi euismod, pretium leo vel, euismod ante. Nam aliquam nulla ut imperdiet congue. Suspendisse massa mi, lobortis sit amet congue a, bibendum nec mi. Fusce dictum justo eu gravida pretium. Ut et orci mauris. Mauris finibus nunc a augue porttitor mollis. Etiam eleifend diam a augue vestibulum, nec auctor sem iaculis. Nullam scelerisque interdum vehicula. Nam non leo auctor, facilisis dui quis, laoreet nisi.', 1, '/uploads/images/1704378008-t-shirt02.png', 5.50),
(3, 'T-shirt 03', 'Praesent enim magna, sagittis ac bibendum vitae, pulvinar ac turpis. Sed quis nunc semper, accumsan quam ut, rutrum eros. Vestibulum diam velit, dignissim ac magna sagittis, efficitur feugiat mauris. In bibendum cursus eros, et dictum sapien. Donec laoreet suscipit imperdiet.', 1, '/uploads/images/1704378050-t-shirt03.png', 12.00),
(4, 'T-shirt 04', 'Pellentesque a scelerisque massa. Vestibulum viverra blandit lacus, porta bibendum velit fermentum non. Donec gravida lorem ac tellus condimentum pulvinar. Vivamus nunc mauris, eleifend rutrum ultrices sed, pulvinar sed metus. Sed viverra lacus pretium eros gravida euismod.', 1, '/uploads/images/1704378073-t-shirt04.png', 9.35),
(5, 'Dress night', 'Integer vestibulum eros quis velit tempor, venenatis blandit quam tincidunt. Vivamus ornare elit sit amet elit rhoncus, non condimentum elit varius. Praesent magna sapien, pulvinar quis massa et, mattis tempor sapien. Suspendisse convallis luctus porta. Proin a porta massa, eu dignissim lacus.', 2, '/uploads/images/1704398525-women-dress_01.png', 150.00),
(6, 'Women Dress 02', 'Curabitur efficitur luctus felis vel feugiat. Nulla id condimentum urna. Sed efficitur turpis sapien, dignissim ultricies orci vulputate nec. Cras mi sem, dictum in nisi ut, ornare tempor enim. Quisque tincidunt hendrerit sem, non ultrices nisl efficitur sed. Curabitur mattis lobortis risus, sed posuere orci lacinia sit amet.', 2, '/uploads/images/1704562861-women-dress_02.png', 200.00),
(7, 'Women Dress 03', 'Ut vitae tempor ipsum. Quisque et leo a risus pharetra vulputate. Aliquam sit amet nulla eu felis suscipit iaculis. Proin est justo, tincidunt vitae leo eget, fermentum maximus arcu.', 2, '/uploads/images/1704398915-women-dress_03.png', 250.00),
(8, 'Women Dress 04', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using &#039;Content here, content here&#039;, making it look like readable English.', 2, '/uploads/images/1704399093-women-dress_04.png', 350.00);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id_role` tinyint(11) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `role`) VALUES
(1, 'ROLE_ADMIN'),
(2, 'ROLE_USER');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_role` tinyint(4) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `email`, `password`, `id_role`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$10$ogfCYy6rVto2lawPtHCONuYgHsVDjYvBMqk6KXY/EdTkGddW7kmJ.', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id_category`);

--
-- Indexes for table `limit_access`
--
ALTER TABLE `limit_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_fk_category` (`id_category`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_role` (`id_role`),
  ADD KEY `idx_id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `limit_access`
--
ALTER TABLE `limit_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` tinyint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_fk_category` FOREIGN KEY (`id_category`) REFERENCES `category` (`id_category`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
