
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `quick_questlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quick_questlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quest_id` int(10) unsigned NOT NULL,
  `reward_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `quick_questlist` WRITE;
/*!40000 ALTER TABLE `quick_questlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `quick_questlist` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `questlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quest_event` int(10) unsigned NOT NULL,
  `quest_type` int(10) unsigned NOT NULL,
  `quest_quantity` int(10) unsigned NOT NULL,
  `quest_action` int(10) unsigned NOT NULL,
  `quest_pokedex_ids` varchar(20) DEFAULT '0',
  `quest_poketypes` varchar(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `questlist` WRITE;
/*!40000 ALTER TABLE `questlist` DISABLE KEYS */;
INSERT INTO `questlist` VALUES (100,0,1,1,1001,'0','0');
INSERT INTO `questlist` VALUES (101,0,1,3,1001,'0','0');
INSERT INTO `questlist` VALUES (102,0,1,5,1001,'0','0');
INSERT INTO `questlist` VALUES (103,0,2,10,2002,'0','0');
INSERT INTO `questlist` VALUES (104,0,3,5,3001,'0','0');
INSERT INTO `questlist` VALUES (105,0,4,1,4001,'0','0');
INSERT INTO `questlist` VALUES (106,0,4,1,4002,'0','0');
INSERT INTO `questlist` VALUES (107,0,5,5,5001,'0','0');
INSERT INTO `questlist` VALUES (108,0,5,3,5010,'0','0');
INSERT INTO `questlist` VALUES (109,0,5,3,5011,'0','0');
INSERT INTO `questlist` VALUES (110,0,5,5,5013,'0','0');
INSERT INTO `questlist` VALUES (111,0,5,3,5021,'0','0');
INSERT INTO `questlist` VALUES (112,0,6,10,6001,'0','0');
INSERT INTO `questlist` VALUES (113,0,6,1,0,'0','4');
INSERT INTO `questlist` VALUES (114,0,6,5,0,'0','8');
INSERT INTO `questlist` VALUES (115,0,6,5,0,'0','18');
INSERT INTO `questlist` VALUES (116,0,6,10,0,'0','12');
INSERT INTO `questlist` VALUES (117,0,6,3,0,'0','5,14,1');
INSERT INTO `questlist` VALUES (118,0,6,5,0,'0','10,3,15');
INSERT INTO `questlist` VALUES (119,0,6,7,0,'0','6,18,9');
INSERT INTO `questlist` VALUES (120,0,6,5,6002,'0','0');
INSERT INTO `questlist` VALUES (121,0,9,5,9010,'0','0');
INSERT INTO `questlist` VALUES (122,0,9,5,9013,'0','0');
INSERT INTO `questlist` VALUES (123,0,9,10,9011,'0','0');
INSERT INTO `questlist` VALUES (124,0,7,1,7001,'0','0');
INSERT INTO `questlist` VALUES (125,0,7,5,7001,'0','0');
INSERT INTO `questlist` VALUES (126,0,7,1,7002,'0','0');
INSERT INTO `questlist` VALUES (127,0,8,1,8001,'0','0');
INSERT INTO `questlist` VALUES (128,0,8,3,8001,'0','0');
INSERT INTO `questlist` VALUES (129,0,8,5,8001,'0','0');
INSERT INTO `questlist` VALUES (130,0,8,1,8003,'0','0');
INSERT INTO `questlist` VALUES (131,0,8,1,8004,'0','0');
INSERT INTO `questlist` VALUES (132,0,8,5,8002,'0','0');
INSERT INTO `questlist` VALUES (133,0,9,7,9001,'0','0');
INSERT INTO `questlist` VALUES (134,0,10,5,10001,'0','0');
INSERT INTO `questlist` VALUES (135,0,11,10,11001,'0','0');
INSERT INTO `questlist` VALUES (136,0,11,3,11002,'0','0');
INSERT INTO `questlist` VALUES (137,0,12,1,12001,'0','0');
/*!40000 ALTER TABLE `questlist` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `encounterlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encounterlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quest_id` int(10) unsigned NOT NULL,
  `pokedex_ids` varchar(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `encounterlist` WRITE;
/*!40000 ALTER TABLE `encounterlist` DISABLE KEYS */;
INSERT INTO `encounterlist` VALUES (100,100,'102,325');
INSERT INTO `encounterlist` VALUES (101,101,'126');
INSERT INTO `encounterlist` VALUES (102,102,'113');
INSERT INTO `encounterlist` VALUES (103,104,'287');
INSERT INTO `encounterlist` VALUES (104,105,'16,133');
INSERT INTO `encounterlist` VALUES (105,106,'227');
INSERT INTO `encounterlist` VALUES (106,107,'74,100');
INSERT INTO `encounterlist` VALUES (107,108,'92,345,347');
INSERT INTO `encounterlist` VALUES (108,109,'95');
INSERT INTO `encounterlist` VALUES (109,110,'327');
INSERT INTO `encounterlist` VALUES (110,111,'246');
INSERT INTO `encounterlist` VALUES (111,112,'129,216');
INSERT INTO `encounterlist` VALUES (112,113,'147');
INSERT INTO `encounterlist` VALUES (113,114,'286');
INSERT INTO `encounterlist` VALUES (114,115,'86');
INSERT INTO `encounterlist` VALUES (115,116,'50');
INSERT INTO `encounterlist` VALUES (116,117,'311,312');
INSERT INTO `encounterlist` VALUES (117,118,'261');
INSERT INTO `encounterlist` VALUES (118,119,'58');
INSERT INTO `encounterlist` VALUES (119,120,'37,60');
INSERT INTO `encounterlist` VALUES (120,121,'70');
INSERT INTO `encounterlist` VALUES (121,122,'317');
INSERT INTO `encounterlist` VALUES (122,123,'294');
INSERT INTO `encounterlist` VALUES (123,124,'56,296');
INSERT INTO `encounterlist` VALUES (124,125,'66');
INSERT INTO `encounterlist` VALUES (125,126,'307');
INSERT INTO `encounterlist` VALUES (126,127,'1,4,7');
INSERT INTO `encounterlist` VALUES (127,128,'124');
INSERT INTO `encounterlist` VALUES (128,129,'88');
INSERT INTO `encounterlist` VALUES (129,130,'138,140');
INSERT INTO `encounterlist` VALUES (130,131,'302');
INSERT INTO `encounterlist` VALUES (131,132,'142');
INSERT INTO `encounterlist` VALUES (132,133,'125');
INSERT INTO `encounterlist` VALUES (133,134,'1,4,7');
INSERT INTO `encounterlist` VALUES (134,135,'103');
INSERT INTO `encounterlist` VALUES (135,136,'353');
INSERT INTO `encounterlist` VALUES (136,137,'425');
/*!40000 ALTER TABLE `encounterlist` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `rewardlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rewardlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reward_type` int(10) unsigned NOT NULL,
  `reward_quantity` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rewardlist` WRITE;
/*!40000 ALTER TABLE `rewardlist` DISABLE KEYS */;
INSERT INTO `rewardlist` VALUES (1,1,1);
INSERT INTO `rewardlist` VALUES (2,2,4);
INSERT INTO `rewardlist` VALUES (3,2,5);
INSERT INTO `rewardlist` VALUES (4,2,6);
INSERT INTO `rewardlist` VALUES (5,2,10);
INSERT INTO `rewardlist` VALUES (6,3,200);
INSERT INTO `rewardlist` VALUES (7,3,400);
INSERT INTO `rewardlist` VALUES (8,3,500);
INSERT INTO `rewardlist` VALUES (9,3,750);
INSERT INTO `rewardlist` VALUES (10,3,1000);
INSERT INTO `rewardlist` VALUES (11,3,1500);
INSERT INTO `rewardlist` VALUES (12,3,2000);
INSERT INTO `rewardlist` VALUES (13,3,3000);
INSERT INTO `rewardlist` VALUES (14,4,1);
INSERT INTO `rewardlist` VALUES (15,4,3);
INSERT INTO `rewardlist` VALUES (16,5,1);
INSERT INTO `rewardlist` VALUES (17,6,1);
INSERT INTO `rewardlist` VALUES (18,7,1);
INSERT INTO `rewardlist` VALUES (19,7,2);
INSERT INTO `rewardlist` VALUES (20,7,3);
INSERT INTO `rewardlist` VALUES (21,7,5);
INSERT INTO `rewardlist` VALUES (22,7,6);
INSERT INTO `rewardlist` VALUES (23,7,9);
INSERT INTO `rewardlist` VALUES (24,7,10);
INSERT INTO `rewardlist` VALUES (25,8,2);
INSERT INTO `rewardlist` VALUES (26,9,1);
INSERT INTO `rewardlist` VALUES (27,9,3);
INSERT INTO `rewardlist` VALUES (28,9,5);
INSERT INTO `rewardlist` VALUES (29,10,1);
INSERT INTO `rewardlist` VALUES (30,10,3);
INSERT INTO `rewardlist` VALUES (31,10,4);
INSERT INTO `rewardlist` VALUES (32,10,5);
INSERT INTO `rewardlist` VALUES (33,10,6);
INSERT INTO `rewardlist` VALUES (34,10,10);
INSERT INTO `rewardlist` VALUES (35,11,1);
INSERT INTO `rewardlist` VALUES (36,11,3);
INSERT INTO `rewardlist` VALUES (37,12,1);
INSERT INTO `rewardlist` VALUES (38,12,2);
INSERT INTO `rewardlist` VALUES (39,12,3);
INSERT INTO `rewardlist` VALUES (40,12,4);
INSERT INTO `rewardlist` VALUES (41,12,5);
INSERT INTO `rewardlist` VALUES (42,12,6);
INSERT INTO `rewardlist` VALUES (43,12,10);
INSERT INTO `rewardlist` VALUES (44,13,1);
INSERT INTO `rewardlist` VALUES (45,13,3);
INSERT INTO `rewardlist` VALUES (46,14,1);
INSERT INTO `rewardlist` VALUES (47,15,1);
INSERT INTO `rewardlist` VALUES (48,16,1);
INSERT INTO `rewardlist` VALUES (49,17,1);
INSERT INTO `rewardlist` VALUES (50,18,1);
INSERT INTO `rewardlist` VALUES (51,19,1);
/*!40000 ALTER TABLE `rewardlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

