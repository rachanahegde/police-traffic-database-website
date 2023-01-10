CREATE DATABASE  IF NOT EXISTS `psxrh6_policedb` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `psxrh6_policedb`;
-- MySQL dump 10.13  Distrib 5.6.24, for Win32 (x86)
--
-- Host: mysql.cs.nott.ac.uk    Database: psxrh6_policedb
-- ------------------------------------------------------
-- Server version	5.5.60-MariaDB

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
-- Table structure for table `Audit`
--

DROP TABLE IF EXISTS `Audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Audit` (
  `Audit_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Table_ID` int(11) NOT NULL,
  `Action` varchar(45) NOT NULL,
  `Table_Column` varchar(45) DEFAULT NULL,
  `Row_ID` int(11) DEFAULT NULL,
  `Old_Value` varchar(45) DEFAULT NULL,
  `New_Value` varchar(45) DEFAULT NULL,
  `User_ID` int(11) NOT NULL,
  `Timestamp` datetime NOT NULL,
  PRIMARY KEY (`Audit_ID`),
  KEY `fk_table_id_idx` (`Table_ID`),
  KEY `fk_user_id_2_idx` (`User_ID`),
  CONSTRAINT `fk_user_id_2` FOREIGN KEY (`User_ID`) REFERENCES `Users` (`User_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_table_id` FOREIGN KEY (`Table_ID`) REFERENCES `Tables` (`Table_ID`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1011 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Audit`
--

LOCK TABLES `Audit` WRITE;
/*!40000 ALTER TABLE `Audit` DISABLE KEYS */;
INSERT INTO `Audit` VALUES (1005,7,'READ',NULL,1,NULL,NULL,1,'2022-12-23 14:10:46'),(1006,7,'READ',NULL,4,NULL,NULL,1,'2022-12-23 14:10:46'),(1007,2,'READ',NULL,1,NULL,NULL,1,'2022-12-23 14:10:46'),(1008,9,'READ',NULL,13,NULL,NULL,1,'2022-12-23 14:10:51'),(1009,6,'READ',NULL,4,NULL,NULL,1,'2022-12-23 14:10:51'),(1010,3,'READ',NULL,2,NULL,NULL,1,'2022-12-23 14:10:56');
/*!40000 ALTER TABLE `Audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Fines`
--

DROP TABLE IF EXISTS `Fines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Fines` (
  `Fine_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Incident_ID` int(11) NOT NULL,
  `Fine_Points` int(11) NOT NULL,
  `Fine_Amount` int(11) NOT NULL,
  PRIMARY KEY (`Fine_ID`),
  KEY `Incident_ID` (`Incident_ID`),
  CONSTRAINT `fk_fines` FOREIGN KEY (`Incident_ID`) REFERENCES `Incident` (`Incident_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Fines`
--

LOCK TABLES `Fines` WRITE;
/*!40000 ALTER TABLE `Fines` DISABLE KEYS */;
INSERT INTO `Fines` VALUES (1,3,6,2000),(2,2,0,50),(3,4,3,500);
/*!40000 ALTER TABLE `Fines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Incident`
--

DROP TABLE IF EXISTS `Incident`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Incident` (
  `Incident_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Vehicle_ID` int(11) NOT NULL,
  `People_ID` int(11) NOT NULL,
  `Incident_Date` date NOT NULL,
  `Incident_Time` time NOT NULL,
  `Incident_Report` varchar(500) NOT NULL,
  `Offence_ID` int(11) NOT NULL,
  `Officer_ID` int(11) NOT NULL,
  PRIMARY KEY (`Incident_ID`),
  KEY `fk_incident_vehicle` (`Vehicle_ID`),
  KEY `fk_incident_people` (`People_ID`),
  KEY `fk_officer_id_idx` (`Officer_ID`),
  KEY `fk_incident_offence_idx` (`Offence_ID`),
  CONSTRAINT `fk_incident_people` FOREIGN KEY (`People_ID`) REFERENCES `People` (`People_ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_incident_vehicle` FOREIGN KEY (`Vehicle_ID`) REFERENCES `Vehicle` (`Vehicle_ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_incident_offence` FOREIGN KEY (`Offence_ID`) REFERENCES `Offence` (`Offence_ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_officer_id` FOREIGN KEY (`Officer_ID`) REFERENCES `Officer` (`Officer_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Incident`
--

LOCK TABLES `Incident` WRITE;
/*!40000 ALTER TABLE `Incident` DISABLE KEYS */;
INSERT INTO `Incident` VALUES (1,15,4,'2017-12-01','00:00:00','40mph in a 30 limit',1,1),(2,20,8,'2017-11-01','00:00:00','Double parked',4,1),(3,13,4,'2017-09-17','00:00:00','110mph on motorway',1,1),(4,14,2,'2017-08-22','00:00:00','Failure to stop at a red light - travelling 25mph',8,2),(5,13,4,'2017-10-17','00:00:00','Not wearing a seatbelt on the M1',3,2);
/*!40000 ALTER TABLE `Incident` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Offence`
--

DROP TABLE IF EXISTS `Offence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Offence` (
  `Offence_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Offence_description` varchar(50) NOT NULL,
  `Offence_maxFine` int(11) NOT NULL,
  `Offence_maxPoints` int(11) NOT NULL,
  PRIMARY KEY (`Offence_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Offence`
--

LOCK TABLES `Offence` WRITE;
/*!40000 ALTER TABLE `Offence` DISABLE KEYS */;
INSERT INTO `Offence` VALUES (0,'No offence',0,0),(1,'Speeding',1000,3),(2,'Speeding on a motorway',2500,6),(3,'Seat belt offence',500,0),(4,'Illegal parking',500,0),(5,'Drunk driving',10000,11),(6,'Driving without a licence',10000,0),(8,'Traffic light offences',1000,3),(9,'Cycling on pavement',500,0),(10,'Failure to have control of vehicle',1000,3),(11,'Dangerous driving',1000,11),(12,'Careless driving',5000,6),(13,'Dangerous cycling',2500,0);
/*!40000 ALTER TABLE `Offence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Officer`
--

DROP TABLE IF EXISTS `Officer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Officer` (
  `Officer_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Officer_name` varchar(45) NOT NULL,
  PRIMARY KEY (`Officer_ID`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`Officer_ID`) REFERENCES `Users` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Officer`
--

LOCK TABLES `Officer` WRITE;
/*!40000 ALTER TABLE `Officer` DISABLE KEYS */;
INSERT INTO `Officer` VALUES (1,'Bob Mcnulty'),(2,'Alina Moreland');
/*!40000 ALTER TABLE `Officer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Ownership`
--

DROP TABLE IF EXISTS `Ownership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ownership` (
  `Ownership_ID` int(11) NOT NULL AUTO_INCREMENT,
  `People_ID` int(11) NOT NULL,
  `Vehicle_ID` int(11) NOT NULL,
  PRIMARY KEY (`Ownership_ID`),
  UNIQUE KEY `Vehicle_ID_UNIQUE` (`Vehicle_ID`),
  KEY `fk_person_idx` (`People_ID`),
  KEY `fk_vehicle_idx` (`Vehicle_ID`),
  CONSTRAINT `fk_person` FOREIGN KEY (`People_ID`) REFERENCES `People` (`People_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicle` FOREIGN KEY (`Vehicle_ID`) REFERENCES `Vehicle` (`Vehicle_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ownership`
--

LOCK TABLES `Ownership` WRITE;
/*!40000 ALTER TABLE `Ownership` DISABLE KEYS */;
INSERT INTO `Ownership` VALUES (1,3,12),(2,8,20),(3,4,15),(4,4,13),(5,1,16),(6,2,14),(7,5,17),(8,6,18),(9,7,21);
/*!40000 ALTER TABLE `Ownership` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `People`
--

DROP TABLE IF EXISTS `People`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `People` (
  `People_ID` int(11) NOT NULL AUTO_INCREMENT,
  `People_name` varchar(50) NOT NULL,
  `DOB` date DEFAULT NULL,
  `People_address` varchar(50) DEFAULT NULL,
  `People_licence` varchar(16) NOT NULL,
  PRIMARY KEY (`People_ID`),
  UNIQUE KEY `LICENCE_INDEX` (`People_licence`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `People`
--

LOCK TABLES `People` WRITE;
/*!40000 ALTER TABLE `People` DISABLE KEYS */;
INSERT INTO `People` VALUES (1,'James Smith',NULL,'23 Barnsdale Road, Leicester','SMITH92LDOFJJ829'),(2,'Jennifer Allen',NULL,'46 Bramcote Drive, Nottingham','ALLEN88K23KLR9B3'),(3,'John Myers',NULL,'323 Derby Road, Nottingham','MYERS99JDW8REWL3'),(4,'James Smith',NULL,'26 Devonshire Avenue, Nottingham','SMITHR004JFS20TR'),(5,'Terry Brown',NULL,'7 Clarke Rd, Nottingham','BROWND3PJJ39DLFG'),(6,'Mary Adams',NULL,'38 Thurman St, Nottingham','ADAMSH9O3JRHH107'),(7,'Neil Becker',NULL,'6 Fairfax Close, Nottingham','BECKE88UPR840F9R'),(8,'Angela Smith',NULL,'30 Avenue Road, Grantham','SMITH222LE9FJ5DS'),(9,'Xene Medora',NULL,'22 House Drive, West Bridgford','MEDORH914ANBB223');
/*!40000 ALTER TABLE `People` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Tables`
--

DROP TABLE IF EXISTS `Tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Tables` (
  `Table_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Table_Name` varchar(45) NOT NULL,
  PRIMARY KEY (`Table_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Tables`
--

LOCK TABLES `Tables` WRITE;
/*!40000 ALTER TABLE `Tables` DISABLE KEYS */;
INSERT INTO `Tables` VALUES (1,'Audit'),(2,'Fines'),(3,'Incident'),(4,'Offence'),(5,'Officer'),(6,'Ownership'),(7,'People'),(8,'Users'),(9,'Vehicle');
/*!40000 ALTER TABLE `Tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(45) NOT NULL,
  `Password` varchar(45) NOT NULL,
  PRIMARY KEY (`User_ID`),
  UNIQUE KEY `Username_UNIQUE` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES (1,'mcnulty','plod123'),(2,'moreland','fuzz42'),(3,'daniels','copper99');
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Vehicle`
--

DROP TABLE IF EXISTS `Vehicle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Vehicle` (
  `Vehicle_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Vehicle_type` varchar(20) NOT NULL,
  `Vehicle_colour` varchar(20) NOT NULL,
  `Vehicle_licence` varchar(7) NOT NULL,
  PRIMARY KEY (`Vehicle_ID`),
  UNIQUE KEY `Vehicle_licence_UNIQUE` (`Vehicle_licence`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Vehicle`
--

LOCK TABLES `Vehicle` WRITE;
/*!40000 ALTER TABLE `Vehicle` DISABLE KEYS */;
INSERT INTO `Vehicle` VALUES (12,'Ford Fiesta','Blue','LB15AJL'),(13,'Ferrari 458','Red','MY64PRE'),(14,'Vauxhall Astra','Silver','FD65WPQ'),(15,'Honda Civic','Green','FJ17AUG'),(16,'Toyota Prius','Silver','FP16KKE'),(17,'Ford Mondeo','Black','FP66KLM'),(18,'Ford Focus','White','DJ14SLE'),(20,'Nissan Pulsar','Red','NY64KWD'),(21,'Renault Scenic','Silver','BC16OEA'),(22,'Hyundai i30','Grey','AD223NG');
/*!40000 ALTER TABLE `Vehicle` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-12-23 14:12:43
