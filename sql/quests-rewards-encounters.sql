
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
DROP TABLE IF EXISTS `questlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quest_type` int(10) unsigned NOT NULL,
  `quest_quantity` int(10) unsigned NOT NULL,
  `quest_action` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `questlist` WRITE;
/*!40000 ALTER TABLE `questlist` DISABLE KEYS */;
INSERT INTO `questlist` VALUES (1,1,1,1);
INSERT INTO `questlist` VALUES (2,1,3,1);
INSERT INTO `questlist` VALUES (3,1,5,1);
INSERT INTO `questlist` VALUES (4,5,5,9);
INSERT INTO `questlist` VALUES (5,5,3,8);
INSERT INTO `questlist` VALUES (6,5,3,11);
INSERT INTO `questlist` VALUES (7,5,3,7);
INSERT INTO `questlist` VALUES (8,5,5,10);
INSERT INTO `questlist` VALUES (9,5,3,6);
INSERT INTO `questlist` VALUES (10,10,1,3);
INSERT INTO `questlist` VALUES (11,10,5,3);
INSERT INTO `questlist` VALUES (12,10,7,3);
INSERT INTO `questlist` VALUES (13,7,7,31);
INSERT INTO `questlist` VALUES (14,2,6,2);
INSERT INTO `questlist` VALUES (15,6,1,20);
INSERT INTO `questlist` VALUES (16,6,3,16);
INSERT INTO `questlist` VALUES (17,6,3,18);
INSERT INTO `questlist` VALUES (18,6,3,19);
INSERT INTO `questlist` VALUES (19,6,3,17);
INSERT INTO `questlist` VALUES (20,6,3,26);
INSERT INTO `questlist` VALUES (21,6,3,22);
INSERT INTO `questlist` VALUES (22,6,3,24);
INSERT INTO `questlist` VALUES (23,6,3,21);
INSERT INTO `questlist` VALUES (24,6,3,23);
INSERT INTO `questlist` VALUES (25,6,3,27);
INSERT INTO `questlist` VALUES (26,6,3,29);
INSERT INTO `questlist` VALUES (27,6,3,25);
INSERT INTO `questlist` VALUES (28,6,5,27);
INSERT INTO `questlist` VALUES (29,6,5,23);
INSERT INTO `questlist` VALUES (30,6,5,30);
INSERT INTO `questlist` VALUES (31,6,10,30);
INSERT INTO `questlist` VALUES (32,6,20,30);
INSERT INTO `questlist` VALUES (33,6,10,4);
INSERT INTO `questlist` VALUES (34,4,1,4);
INSERT INTO `questlist` VALUES (35,4,5,4);
INSERT INTO `questlist` VALUES (36,4,3,5);
INSERT INTO `questlist` VALUES (37,8,1,14);
INSERT INTO `questlist` VALUES (38,8,3,14);
INSERT INTO `questlist` VALUES (39,8,5,14);
INSERT INTO `questlist` VALUES (40,9,1,13);
INSERT INTO `questlist` VALUES (41,8,1,15);
INSERT INTO `questlist` VALUES (42,9,5,15);
INSERT INTO `questlist` VALUES (43,3,1,28);
INSERT INTO `questlist` VALUES (44,2,10,32);
/*!40000 ALTER TABLE `questlist` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `rewardlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rewardlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reward_type` int(10) unsigned NOT NULL,
  `reward_quantity` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rewardlist` WRITE;
/*!40000 ALTER TABLE `rewardlist` DISABLE KEYS */;
INSERT INTO `rewardlist` VALUES (1,1,1);
INSERT INTO `rewardlist` VALUES (2,2,4);
INSERT INTO `rewardlist` VALUES (3,2,5);
INSERT INTO `rewardlist` VALUES (4,2,6);
INSERT INTO `rewardlist` VALUES (5,2,10);
INSERT INTO `rewardlist` VALUES (6,3,1);
INSERT INTO `rewardlist` VALUES (7,3,2);
INSERT INTO `rewardlist` VALUES (8,3,3);
INSERT INTO `rewardlist` VALUES (9,3,5);
INSERT INTO `rewardlist` VALUES (10,3,6);
INSERT INTO `rewardlist` VALUES (11,3,10);
INSERT INTO `rewardlist` VALUES (12,4,400);
INSERT INTO `rewardlist` VALUES (13,4,500);
INSERT INTO `rewardlist` VALUES (14,4,750);
INSERT INTO `rewardlist` VALUES (15,4,1000);
INSERT INTO `rewardlist` VALUES (16,4,2000);
INSERT INTO `rewardlist` VALUES (17,4,3000);
INSERT INTO `rewardlist` VALUES (18,5,1);
INSERT INTO `rewardlist` VALUES (19,5,3);
INSERT INTO `rewardlist` VALUES (20,6,1);
INSERT INTO `rewardlist` VALUES (21,7,1);
INSERT INTO `rewardlist` VALUES (22,8,1);
INSERT INTO `rewardlist` VALUES (23,8,3);
INSERT INTO `rewardlist` VALUES (24,8,4);
INSERT INTO `rewardlist` VALUES (25,8,5);
INSERT INTO `rewardlist` VALUES (26,8,6);
INSERT INTO `rewardlist` VALUES (27,8,10);
INSERT INTO `rewardlist` VALUES (28,9,10);
INSERT INTO `rewardlist` VALUES (29,9,400);
INSERT INTO `rewardlist` VALUES (30,9,500);
INSERT INTO `rewardlist` VALUES (31,9,750);
INSERT INTO `rewardlist` VALUES (32,9,1000);
INSERT INTO `rewardlist` VALUES (33,9,2000);
INSERT INTO `rewardlist` VALUES (34,9,3000);
INSERT INTO `rewardlist` VALUES (35,10,1);
INSERT INTO `rewardlist` VALUES (36,10,3);
INSERT INTO `rewardlist` VALUES (37,10,4);
INSERT INTO `rewardlist` VALUES (38,10,5);
INSERT INTO `rewardlist` VALUES (39,10,6);
INSERT INTO `rewardlist` VALUES (40,10,10);
INSERT INTO `rewardlist` VALUES (41,4,200);
/*!40000 ALTER TABLE `rewardlist` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `encounterlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encounterlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quest_id` int(10) unsigned NOT NULL,
  `pokedex_ids` varchar(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `encounterlist` WRITE;
/*!40000 ALTER TABLE `encounterlist` DISABLE KEYS */;
INSERT INTO `encounterlist` VALUES (1,1,'102');
INSERT INTO `encounterlist` VALUES (2,2,'126');
INSERT INTO `encounterlist` VALUES (3,3,'113');
INSERT INTO `encounterlist` VALUES (4,4,'100,101');
INSERT INTO `encounterlist` VALUES (5,5,'101');
INSERT INTO `encounterlist` VALUES (6,6,'92');
INSERT INTO `encounterlist` VALUES (7,7,'95');
INSERT INTO `encounterlist` VALUES (8,8,'95,180');
INSERT INTO `encounterlist` VALUES (9,9,'246');
INSERT INTO `encounterlist` VALUES (10,10,'311');
INSERT INTO `encounterlist` VALUES (11,11,'1,4,7');
INSERT INTO `encounterlist` VALUES (12,12,'82');
INSERT INTO `encounterlist` VALUES (13,13,'125');
INSERT INTO `encounterlist` VALUES (14,14,'142');
INSERT INTO `encounterlist` VALUES (15,15,'147');
INSERT INTO `encounterlist` VALUES (16,16,'114');
INSERT INTO `encounterlist` VALUES (17,17,'23');
INSERT INTO `encounterlist` VALUES (18,18,'255');
INSERT INTO `encounterlist` VALUES (19,19,'58');
INSERT INTO `encounterlist` VALUES (20,20,'88');
INSERT INTO `encounterlist` VALUES (21,21,'127');
INSERT INTO `encounterlist` VALUES (22,22,'228');
INSERT INTO `encounterlist` VALUES (23,23,'25');
INSERT INTO `encounterlist` VALUES (24,24,'84');
INSERT INTO `encounterlist` VALUES (25,25,'77,219');
INSERT INTO `encounterlist` VALUES (26,26,'309');
INSERT INTO `encounterlist` VALUES (27,27,'322');
INSERT INTO `encounterlist` VALUES (28,28,'136');
INSERT INTO `encounterlist` VALUES (29,29,'207');
INSERT INTO `encounterlist` VALUES (30,30,'37,60');
INSERT INTO `encounterlist` VALUES (31,31,'4');
INSERT INTO `encounterlist` VALUES (32,32,'38');
INSERT INTO `encounterlist` VALUES (33,33,'129');
INSERT INTO `encounterlist` VALUES (34,34,'133');
INSERT INTO `encounterlist` VALUES (35,35,'4');
INSERT INTO `encounterlist` VALUES (36,36,'135');
INSERT INTO `encounterlist` VALUES (37,37,'312');
INSERT INTO `encounterlist` VALUES (38,38,'124');
INSERT INTO `encounterlist` VALUES (39,39,'56,66');
INSERT INTO `encounterlist` VALUES (40,40,'7');
INSERT INTO `encounterlist` VALUES (41,41,'312');
INSERT INTO `encounterlist` VALUES (42,42,'132,185');
INSERT INTO `encounterlist` VALUES (43,43,'333');
/*!40000 ALTER TABLE `encounterlist` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `quick_questlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quick_questlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quest_id` int(10) unsigned NOT NULL,
  `reward_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `quick_questlist` WRITE;
/*!40000 ALTER TABLE `quick_questlist` DISABLE KEYS */;
INSERT INTO `quick_questlist` VALUES (1,33,1);
/*!40000 ALTER TABLE `quick_questlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

