/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.5.2-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: my_database
-- ------------------------------------------------------
-- Server version	11.5.2-MariaDB-ubu2404

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id_category` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  PRIMARY KEY (`id_category`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES
(1,'men'),
(2,'women');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) NOT NULL,
  `uploader_name` varchar(255) NOT NULL,
  `uploader_email` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`document_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES
(10,'Factura.pdf','ddddd','dddssd@ddsd.com','','/uploads/documents','2025-07-25 14:53:05');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `limit_access`
--

DROP TABLE IF EXISTS `limit_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `limit_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL,
  `restriction_time` int(11) NOT NULL,
  `failed_tries` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `limit_access`
--

LOCK TABLES `limit_access` WRITE;
/*!40000 ALTER TABLE `limit_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `limit_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `id_category` int(11) NOT NULL,
  `image` varchar(150) DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `products_fk_category` (`id_category`),
  CONSTRAINT `products_fk_category` FOREIGN KEY (`id_category`) REFERENCES `category` (`id_category`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(1,'T-shirt','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras euismod felis nec nulla tristique scelerisque. Quisque blandit feugiat ullamcorper. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc hendrerit lorem vel tempor lacinia. Proin blandit blandit maximus. Donec blandit orci a massa luctus posuere. Maecenas sem metus, vestibulum quis maximus et, accumsan eget turpis. Phasellus imperdiet ac eros ut ultricies. Etiam scelerisque sodales sapien, et posuere risus dapibus ut. Donec ac libero facilisis, malesuada orci eu, faucibus risus.',1,'/uploads/images/1704318259-t-shirt01.png',13.50),
(2,'T-shirt 02','Pellentesque ut mi euismod, pretium leo vel, euismod ante. Nam aliquam nulla ut imperdiet congue. Suspendisse massa mi, lobortis sit amet congue a, bibendum nec mi. Fusce dictum justo eu gravida pretium. Ut et orci mauris. Mauris finibus nunc a augue porttitor mollis. Etiam eleifend diam a augue vestibulum, nec auctor sem iaculis. Nullam scelerisque interdum vehicula. Nam non leo auctor, facilisis dui quis, laoreet nisi.',1,'/uploads/images/1704378008-t-shirt02.png',5.50),
(3,'T-shirt 03','Praesent enim magna, sagittis ac bibendum vitae, pulvinar ac turpis. Sed quis nunc semper, accumsan quam ut, rutrum eros. Vestibulum diam velit, dignissim ac magna sagittis, efficitur feugiat mauris. In bibendum cursus eros, et dictum sapien. Donec laoreet suscipit imperdiet.',1,'/uploads/images/1704378050-t-shirt03.png',12.00),
(4,'T-shirt 04','Pellentesque a scelerisque massa. Vestibulum viverra blandit lacus, porta bibendum velit fermentum non. Donec gravida lorem ac tellus condimentum pulvinar. Vivamus nunc mauris, eleifend rutrum ultrices sed, pulvinar sed metus. Sed viverra lacus pretium eros gravida euismod.',1,'/uploads/images/1704378073-t-shirt04.png',9.35),
(5,'Dress night','Integer vestibulum eros quis velit tempor, venenatis blandit quam tincidunt. Vivamus ornare elit sit amet elit rhoncus, non condimentum elit varius. Praesent magna sapien, pulvinar quis massa et, mattis tempor sapien. Suspendisse convallis luctus porta. Proin a porta massa, eu dignissim lacus.',2,'/uploads/images/1704398525-women-dress_01.png',150.00),
(6,'Women Dress 02','Curabitur efficitur luctus felis vel feugiat. Nulla id condimentum urna. Sed efficitur turpis sapien, dignissim ultricies orci vulputate nec. Cras mi sem, dictum in nisi ut, ornare tempor enim. Quisque tincidunt hendrerit sem, non ultrices nisl efficitur sed. Curabitur mattis lobortis risus, sed posuere orci lacinia sit amet.',2,'/uploads/images/1704562861-women-dress_02.png',200.00),
(7,'Women Dress 03','Ut vitae tempor ipsum. Quisque et leo a risus pharetra vulputate. Aliquam sit amet nulla eu felis suscipit iaculis. Proin est justo, tincidunt vitae leo eget, fermentum maximus arcu.',2,'/uploads/images/1704398915-women-dress_03.png',250.00),
(8,'Women Dress 04','It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using &#039;Content here, content here&#039;, making it look like readable English.',2,'/uploads/images/1704399093-women-dress_04.png',350.00);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id_role` tinyint(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'ROLE_ADMIN'),
(2,'ROLE_USER');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_role` tinyint(4) NOT NULL DEFAULT 2,
  PRIMARY KEY (`id`),
  KEY `fk_user_role` (`id_role`),
  KEY `idx_id` (`id`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','admin@admin.com','$2y$10$ogfCYy6rVto2lawPtHCONuYgHsVDjYvBMqk6KXY/EdTkGddW7kmJ.',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-07-25 15:30:01
