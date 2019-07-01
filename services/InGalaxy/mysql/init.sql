-- MySQL dump 10.16  Distrib 10.2.25-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ingalaxy
-- ------------------------------------------------------
-- Server version	10.2.25-MariaDB-1:10.2.25+maria~bionic

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `dialogs`
--

DROP TABLE IF EXISTS `dialogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dialogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_owner` int(10) unsigned NOT NULL,
  `id_subject` int(10) unsigned NOT NULL,
  `slug` varchar(190) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dialogs_id_owner_foreign` (`id_owner`),
  KEY `dialogs_id_subject_foreign` (`id_subject`),
  CONSTRAINT `dialogs_id_owner_foreign` FOREIGN KEY (`id_owner`) REFERENCES `users` (`id`),
  CONSTRAINT `dialogs_id_subject_foreign` FOREIGN KEY (`id_subject`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dialogs`
--

LOCK TABLES `dialogs` WRITE;
/*!40000 ALTER TABLE `dialogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `dialogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `friends` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_owner` int(10) unsigned NOT NULL,
  `id_subject` int(10) unsigned NOT NULL,
  `approve` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `friends_id_owner_foreign` (`id_owner`),
  KEY `friends_id_subject_foreign` (`id_subject`),
  CONSTRAINT `friends_id_owner_foreign` FOREIGN KEY (`id_owner`) REFERENCES `users` (`id`),
  CONSTRAINT `friends_id_subject_foreign` FOREIGN KEY (`id_subject`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friends`
--

LOCK TABLES `friends` WRITE;
/*!40000 ALTER TABLE `friends` DISABLE KEYS */;
/*!40000 ALTER TABLE `friends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_owner` int(10) unsigned NOT NULL,
  `id_dialogs` int(10) unsigned NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(190) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id_owner_foreign` (`id_owner`),
  KEY `message_id_dialogs_foreign` (`id_dialogs`),
  CONSTRAINT `message_id_dialogs_foreign` FOREIGN KEY (`id_dialogs`) REFERENCES `dialogs` (`id`),
  CONSTRAINT `message_id_owner_foreign` FOREIGN KEY (`id_owner`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(190) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2018_12_22_051352_create_friends_table',1),(3,'2018_12_22_065751_create_posts_table',1),(4,'2018_12_22_070001_create_dialogs_table',1),(5,'2018_12_22_070158_create_message_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_owner` int(10) unsigned NOT NULL,
  `id_recipient` int(10) unsigned NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(190) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_id_owner_foreign` (`id_owner`),
  KEY `posts_id_recipient_foreign` (`id_recipient`),
  CONSTRAINT `posts_id_owner_foreign` FOREIGN KEY (`id_owner`) REFERENCES `users` (`id`),
  CONSTRAINT `posts_id_recipient_foreign` FOREIGN KEY (`id_recipient`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(190) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(190) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(190) COLLATE utf8_unicode_ci DEFAULT NULL,
  `surname` varchar(190) COLLATE utf8_unicode_ci DEFAULT NULL,
  `race` varchar(190) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(190) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` datetime DEFAULT NULL,
  `homeplace` varchar(190) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(190) COLLATE utf8_unicode_ci DEFAULT 'img/galaxy.jpg',
  `selfdescription` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_unique` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-06-23 12:15:43

