-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: rp
-- ------------------------------------------------------
-- Server version	5.6.25-log

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
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `uniqueid` varchar(32) NOT NULL,
  `armorclass` int(11) NOT NULL DEFAULT '790',
  `email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `players`
--

LOCK TABLES `players` WRITE;
/*!40000 ALTER TABLE `players` DISABLE KEYS */;
/*!40000 ALTER TABLE `players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `raids`
--

DROP TABLE IF EXISTS `raids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `raidtypeid` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `creationdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raids`
--

LOCK TABLES `raids` WRITE;
/*!40000 ALTER TABLE `raids` DISABLE KEYS */;
/*!40000 ALTER TABLE `raids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `raidtypes`
--

DROP TABLE IF EXISTS `raidtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raidtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `players` int(11) NOT NULL,
  `difficulty` varchar(12) NOT NULL,
  `enabled` bit(1) NOT NULL DEFAULT b'1',
  `armorclass` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raidtypes`
--

LOCK TABLES `raidtypes` WRITE;
/*!40000 ALTER TABLE `raidtypes` DISABLE KEYS */;
INSERT INTO `raidtypes` VALUES (1,'Dryade',10,'Normal','',790),(2,'Dryade',20,'Normal','',790),(3,'Dryade',10,'Schwer','',862),(4,'Dryade',20,'Schwer','',862),(5,'Kranheim',10,'Normal','',934),(6,'Kranheim',20,'Normal','',934),(7,'Kranheim',10,'Schwer','',1006),(8,'Kranheim',20,'Schwer','',1006);
/*!40000 ALTER TABLE `raidtypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `raidid` int(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `participation` tinyint(2) NOT NULL,
  `text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrations`
--

LOCK TABLES `registrations` WRITE;
/*!40000 ALTER TABLE `registrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `v_raids`
--

DROP TABLE IF EXISTS `v_raids`;
/*!50001 DROP VIEW IF EXISTS `v_raids`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_raids` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `players`,
 1 AS `difficulty`,
 1 AS `armorclass`,
 1 AS `datetime`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_registrations`
--

DROP TABLE IF EXISTS `v_registrations`;
/*!50001 DROP VIEW IF EXISTS `v_registrations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_registrations` AS SELECT 
 1 AS `raidid`,
 1 AS `playername`,
 1 AS `participation`,
 1 AS `comment`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_raids`
--

/*!50001 DROP VIEW IF EXISTS `v_raids`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_raids` AS select `raids`.`id` AS `id`,`raidtypes`.`name` AS `name`,`raidtypes`.`players` AS `players`,`raidtypes`.`difficulty` AS `difficulty`,`raidtypes`.`armorclass` AS `armorclass`,`raids`.`datetime` AS `datetime` from (`raids` left join `raidtypes` on((`raids`.`raidtypeid` = `raidtypes`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_registrations`
--

/*!50001 DROP VIEW IF EXISTS `v_registrations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_registrations` AS select `registrations`.`raidid` AS `raidid`,`players`.`name` AS `playername`,`registrations`.`participation` AS `participation`,`registrations`.`text` AS `comment` from (`registrations` left join `players` on((`registrations`.`playerid` = `players`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-12-15 16:16:01
