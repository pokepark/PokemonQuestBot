
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
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `questlist` WRITE;
/*!40000 ALTER TABLE `questlist` DISABLE KEYS */;
INSERT INTO `questlist` VALUES (1, '6', '3', '6034');
INSERT INTO `questlist` VALUES (2, '6', '10', '6021');
INSERT INTO `questlist` VALUES (3, '6', '10', '6020');
INSERT INTO `questlist` VALUES (4, '6', '5', '6016');
INSERT INTO `questlist` VALUES (5, '9', '10', '9003');
INSERT INTO `questlist` VALUES (6, '9', '5', '9004');
INSERT INTO `questlist` VALUES (7, '6', '10', '6001');
INSERT INTO `questlist` VALUES (8, '6', '5', '6002');
INSERT INTO `questlist` VALUES (9, '6', '1', '6012');
INSERT INTO `questlist` VALUES (10, '9', '5', '9002');
INSERT INTO `questlist` VALUES (11, '6', '10', '6002');
INSERT INTO `questlist` VALUES (12, '6', '10', '6022');
INSERT INTO `questlist` VALUES (13, '6', '1', '6105');
INSERT INTO `questlist` VALUES (14, '4', '1', '6003');
INSERT INTO `questlist` VALUES (15, '4', '1', '6001');
INSERT INTO `questlist` VALUES (16, '10', '5', '10001');
INSERT INTO `questlist` VALUES (17, '2', '10', '2002');
INSERT INTO `questlist` VALUES (18, '11', '3', '6001');
INSERT INTO `questlist` VALUES (19, '12', '1', '6001');
INSERT INTO `questlist` VALUES (20, '3', '5', '3001');
INSERT INTO `questlist` VALUES (21, '11', '2', '11001');
INSERT INTO `questlist` VALUES (22, '1', '1', '1001');
INSERT INTO `questlist` VALUES (23, '1', '3', '1001');
INSERT INTO `questlist` VALUES (24, '1', '5', '1001');
INSERT INTO `questlist` VALUES (25, '8', '5', '8001');
INSERT INTO `questlist` VALUES (26, '7', '1', '7001');
INSERT INTO `questlist` VALUES (27, '7', '5', '7001');
INSERT INTO `questlist` VALUES (28, '8', '1', '8001');
INSERT INTO `questlist` VALUES (29, '8', '1', '8002');
INSERT INTO `questlist` VALUES (30, '9', '7', '9001');
INSERT INTO `questlist` VALUES (31, '8', '3', '8001');
INSERT INTO `questlist` VALUES (32, '7', '1', '8002');
INSERT INTO `questlist` VALUES (33, '9', '1', '9001');
INSERT INTO `questlist` VALUES (34, '8', '1', '8003');
INSERT INTO `questlist` VALUES (35, '8', '3', '8002');
INSERT INTO `questlist` VALUES (36, '5', '5', '5013');
INSERT INTO `questlist` VALUES (37, '5', '5', '5001');
INSERT INTO `questlist` VALUES (38, '5', '3', '5010');
INSERT INTO `questlist` VALUES (39, '5', '3', '5011');
INSERT INTO `questlist` VALUES (40, '5', '3', '5021');
INSERT INTO `questlist` VALUES (41, '5', '2', '5004');
INSERT INTO `questlist` VALUES (42, '5', '5', '5031');
INSERT INTO `questlist` VALUES (43, '5', '3', '5002');
INSERT INTO `questlist` VALUES (44, '5', '1', '5020');
INSERT INTO `questlist` VALUES (45, '5', '3', '5010');
INSERT INTO `questlist` VALUES (46, '5', '3', '5011');
INSERT INTO `questlist` VALUES (47, '6', '7', '6035');
INSERT INTO `questlist` VALUES (48, '8', '5', '8002');
INSERT INTO `questlist` VALUES (49, '6', '10', '6121');
INSERT INTO `questlist` VALUES (50, '1', '2', '1001');
INSERT INTO `questlist` VALUES (51, '4', '3', '6122');

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
INSERT INTO `rewardlist` VALUES (1, 1, 1);
INSERT INTO `rewardlist` VALUES (2, 2, 4);
INSERT INTO `rewardlist` VALUES (3, 2, 5);
INSERT INTO `rewardlist` VALUES (4, 2, 6);
INSERT INTO `rewardlist` VALUES (5, 2, 10);
INSERT INTO `rewardlist` VALUES (6, 3, 200);
INSERT INTO `rewardlist` VALUES (7, 3, 400);
INSERT INTO `rewardlist` VALUES (8, 3, 500);
INSERT INTO `rewardlist` VALUES (9, 3, 750);
INSERT INTO `rewardlist` VALUES (10, 3, 1000);
INSERT INTO `rewardlist` VALUES (11, 3, 1500);
INSERT INTO `rewardlist` VALUES (12, 3, 2000);
INSERT INTO `rewardlist` VALUES (13, 3, 3000);
INSERT INTO `rewardlist` VALUES (14, 4, 1);
INSERT INTO `rewardlist` VALUES (15, 4, 3);
INSERT INTO `rewardlist` VALUES (16, 5, 1);
INSERT INTO `rewardlist` VALUES (17, 6, 1);
INSERT INTO `rewardlist` VALUES (18, 7, 1);
INSERT INTO `rewardlist` VALUES (19, 7, 2);
INSERT INTO `rewardlist` VALUES (20, 7, 3);
INSERT INTO `rewardlist` VALUES (21, 7, 5);
INSERT INTO `rewardlist` VALUES (22, 7, 6);
INSERT INTO `rewardlist` VALUES (23, 7, 9);
INSERT INTO `rewardlist` VALUES (24, 7, 10);
INSERT INTO `rewardlist` VALUES (25, 8, 2);
INSERT INTO `rewardlist` VALUES (26, 9, 1);
INSERT INTO `rewardlist` VALUES (27, 10, 1);
INSERT INTO `rewardlist` VALUES (28, 10, 3);
INSERT INTO `rewardlist` VALUES (29, 10, 4);
INSERT INTO `rewardlist` VALUES (30, 10, 5);
INSERT INTO `rewardlist` VALUES (31, 10, 6);
INSERT INTO `rewardlist` VALUES (32, 10, 10);
INSERT INTO `rewardlist` VALUES (33, 11, 1);
INSERT INTO `rewardlist` VALUES (34, 11, 3);
INSERT INTO `rewardlist` VALUES (35, 12, 1);
INSERT INTO `rewardlist` VALUES (36, 12, 2);
INSERT INTO `rewardlist` VALUES (37, 12, 3);
INSERT INTO `rewardlist` VALUES (38, 12, 4);
INSERT INTO `rewardlist` VALUES (39, 12, 5);
INSERT INTO `rewardlist` VALUES (40, 12, 6);
INSERT INTO `rewardlist` VALUES (41, 12, 10);
INSERT INTO `rewardlist` VALUES (42, 13, 1);
INSERT INTO `rewardlist` VALUES (43, 13, 3);
INSERT INTO `rewardlist` VALUES (44, 14, 1);
INSERT INTO `rewardlist` VALUES (45, 15, 1);
INSERT INTO `rewardlist` VALUES (46, 16, 1);
INSERT INTO `rewardlist` VALUES (47, 17, 1);
INSERT INTO `rewardlist` VALUES (48, 18, 1);
INSERT INTO `rewardlist` VALUES (49, 19, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `encounterlist` WRITE;
/*!40000 ALTER TABLE `encounterlist` DISABLE KEYS */;
INSERT INTO `encounterlist` VALUES (1, '1', '10');
INSERT INTO `encounterlist` VALUES (2, '2', '204');
INSERT INTO `encounterlist` VALUES (3, '3', '42');
INSERT INTO `encounterlist` VALUES (4, '4', '327');
INSERT INTO `encounterlist` VALUES (5, '5', '36');
INSERT INTO `encounterlist` VALUES (6, '6', '40');
INSERT INTO `encounterlist` VALUES (7, '7', '290');
INSERT INTO `encounterlist` VALUES (8, '8', '127');
INSERT INTO `encounterlist` VALUES (9, '9', '102');
INSERT INTO `encounterlist` VALUES (10, '10', '126');
INSERT INTO `encounterlist` VALUES (11, '11', '113');
INSERT INTO `encounterlist` VALUES (12, '13', '133');
INSERT INTO `encounterlist` VALUES (13, '15', '100');
INSERT INTO `encounterlist` VALUES (14, '16', '92');
INSERT INTO `encounterlist` VALUES (15, '17', '95');
INSERT INTO `encounterlist` VALUES (16, '19', '246');
INSERT INTO `encounterlist` VALUES (17, '20', '129');
INSERT INTO `encounterlist` VALUES (18, '21', '37,61');
INSERT INTO `encounterlist` VALUES (19, '22', '147');
INSERT INTO `encounterlist` VALUES (20, '24', '56');
INSERT INTO `encounterlist` VALUES (21, '25', '66');
INSERT INTO `encounterlist` VALUES (22, '26', '1,4,7');
INSERT INTO `encounterlist` VALUES (23, '27', '124');
INSERT INTO `encounterlist` VALUES (24, '29', '125');
INSERT INTO `encounterlist` VALUES (25, '32', '1,4,7');
/* NOT AVAILABLE SINCE LETS GO EVENT INSERT INTO `encounterlist` VALUES (26, '48', '33'); */

/* START Let's Go */
INSERT INTO `encounterlist` VALUES (NULL, '48', '142');
INSERT INTO `encounterlist` VALUES (NULL, '49', '123');
INSERT INTO `encounterlist` VALUES (NULL, '50', '132');
/* END Let's Go */

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
INSERT INTO `quick_questlist` VALUES (1,4,1);
INSERT INTO `quick_questlist` VALUES (2,7,1);
INSERT INTO `quick_questlist` VALUES (3,8,1);
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

