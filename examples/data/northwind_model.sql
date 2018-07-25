-- MySQL dump 10.16  Distrib 10.2.11-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: northwind_model
-- ------------------------------------------------------
-- Server version	10.2.11-MariaDB-10.2.11+maria~xenial-log

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
-- Table structure for table `appstring`
--

CREATE DATABASE IF NOT EXISTS `northwind_model`;
use northwind_model;

DROP TABLE IF EXISTS `appstring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appstring` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stringData` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appstring`
--

LOCK TABLES `appstring` WRITE;
/*!40000 ALTER TABLE `appstring` DISABLE KEYS */;
INSERT INTO `appstring` VALUES (2,'Northwind Traders'),(3,'Cannot remove posted inventory!'),(4,'Back ordered product filled for Order #|'),(5,'Discounted price below cost!'),(6,'Insufficient inventory.'),(7,'Insufficient inventory. Do you want to create a purchase order?'),(8,'Purchase order were successfully created for | product'),(9,'There are no product below their respective reorder levels'),(10,'Must specify customer name!'),(11,'Restocking will generate purchase order for all product below desired inventory levels.  Do you want to continue?'),(12,'Cannot create purchase order.  No supplier listed for specified product'),(13,'Discounted price is below cost!'),(14,'Do you want to continue?'),(15,'Order is already invoiced. Do you want to print the invoice?'),(16,'Order does not contain any line items'),(17,'Cannot create invoice!  Inventory has not been allocated for each specified product.'),(18,'Sorry, there are no sales in the specified time period'),(19,'Product successfully restocked.'),(21,'Product does not need restocking! Product is already at desired inventory level.'),(22,'Product restocking failed!'),(23,'Invalid login specified!'),(24,'Must first select reported!'),(25,'Changing supplier will remove purchase line items, continue?'),(26,'Purchase order were successfully submitted for | product.  Do you want to view the restocking report?'),(27,'There was an error attempting to restock inventory levels.'),(28,'| product(s) were successfully restocked.  Do you want to view the restocking report?'),(29,'You cannot remove purchase line items already posted to inventory!'),(30,'There was an error removing one or more purchase line items.'),(31,'You cannot modify quantity for purchased product already received or posted to inventory.'),(32,'You cannot modify price for purchased product already received or posted to inventory.'),(33,'Product has been successfully posted to inventory.'),(34,'Sorry, product cannot be successfully posted to inventory.'),(35,'There are order with this product on back order.  Would you like to fill them now?'),(36,'Cannot post product to inventory without specifying received date!'),(37,'Do you want to post received product to inventory?'),(38,'Initialize purchase, order, and inventory data?'),(39,'Must first specify employee name!'),(40,'Specified user must be logged in to approve purchase!'),(41,'Purchase order must contain completed line items before it can be approved'),(42,'Sorry, you do not have permission to approve purchases.'),(43,'Purchase successfully approved'),(44,'Purchase cannot be approved'),(45,'Purchase successfully submitted for approval'),(46,'Purchase cannot be submitted for approval'),(47,'Sorry, purchase order does not contain line items'),(48,'Do you want to cancel this order?'),(49,'Canceling an order will permanently delete the order.  Are you sure you want to cancel?'),(100,'Your order was successfully canceled.'),(101,'Cannot cancel an order that has items received and posted to inventory.'),(102,'There was an error trying to cancel this order.'),(103,'The invoice for this order has not yet been created.'),(104,'Shipping information is not complete.  Please specify all shipping information and try again.'),(105,'Cannot mark as shipped.  Order must first be invoiced!'),(106,'Cannot cancel an order that has already shipped!'),(107,'Must first specify salesperson!'),(108,'Order is now marked closed.'),(109,'Order must first be marked shipped before closing.'),(110,'Must first specify payment information!'),(111,'There was an error attempting to restock inventory levels.  | product(s) were successfully restocked.'),(112,'You must supply a Unit Cost.'),(113,'Fill back ordered product, Order #|'),(114,'Purchase generated based on Order #|');
/*!40000 ALTER TABLE `appstring` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `emailAddress` varchar(50) DEFAULT NULL,
  `jobTitle` varchar(50) DEFAULT NULL,
  `businessPhone` varchar(25) DEFAULT NULL,
  `homePhone` varchar(25) DEFAULT NULL,
  `mobilePhone` varchar(25) DEFAULT NULL,
  `faxNumber` varchar(25) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `stateProvince` varchar(50) DEFAULT NULL,
  `zipPostalCode` varchar(15) DEFAULT NULL,
  `countryRegion` varchar(50) DEFAULT NULL,
  `webPage` longtext DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `attachments` longblob DEFAULT NULL,
  `random_date` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city` (`city`),
  KEY `company` (`companyName`),
  KEY `firstName` (`firstName`),
  KEY `lastName` (`lastName`),
  KEY `zipPostalCode` (`zipPostalCode`),
  KEY `stateProvince` (`stateProvince`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (1,'Company A','Bedecs','Anna',NULL,'Owner','(123)555-0100',NULL,NULL,'(123)555-0101','123 1st Street','Seattle','WA','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Company B','Gratacos Solsona','Antonio',NULL,'Owner','(123)555-0100',NULL,NULL,'(123)555-0101','123 2nd Street','Boston','MA','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'Company C','Axen','Thomas',NULL,'Purchasing Representative','(123)555-0100',NULL,NULL,'(123)555-0101','123 3rd Street','Los Angelas','CA','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Company D','Lee','Christina',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','123 4th Street','New York','NY','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'Company E','O’Donnell','Martin',NULL,'Owner','(123)555-0100',NULL,NULL,'(123)555-0101','123 5th Street','Minneapolis','MN','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,'Company F','Pérez-Olaeta','Francisco',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','123 6th Street','Milwaukee','WI','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(7,'Company G','Xie','Ming-Yang',NULL,'Owner','(123)555-0100',NULL,NULL,'(123)555-0101','123 7th Street','Boise','ID','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(8,'Company H','Andersen','Elizabeth',NULL,'Purchasing Representative','(123)555-0100',NULL,NULL,'(123)555-0101','123 8th Street','Portland','OR','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(9,'Company I','Mortensen','Sven',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','123 9th Street','Salt Lake City','UT','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(10,'Company J','Wacker','Roland',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','123 10th Street','Chicago','IL','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(11,'Company K','Krschne','Peter',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','123 11th Street','Miami','FL','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(12,'Company L','Edwards','John',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','123 12th Street','Las Vegas','NV','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(13,'Company M','Ludick','Andre',NULL,'Purchasing Representative','(123)555-0100',NULL,NULL,'(123)555-0101','456 13th Street','Memphis','TN','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(14,'Company N','Grilo','Carlos',NULL,'Purchasing Representative','(123)555-0100',NULL,NULL,'(123)555-0101','456 14th Street','Denver','CO','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(15,'Company O','Kupkova','Helena',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','456 15th Street','Honolulu','HI','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(16,'Company P','Goldschmidt','Daniel',NULL,'Purchasing Representative','(123)555-0100',NULL,NULL,'(123)555-0101','456 16th Street','San Francisco','CA','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(17,'Company Q','Bagel','Jean Philippe',NULL,'Owner','(123)555-0100',NULL,NULL,'(123)555-0101','456 17th Street','Seattle','WA','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(18,'Company R','Autier Miconi','Catherine',NULL,'Purchasing Representative','(123)555-0100',NULL,NULL,'(123)555-0101','456 18th Street','Boston','MA','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(19,'Company S','Eggerer','Alexander',NULL,'Accounting Assistant','(123)555-0100',NULL,NULL,'(123)555-0101','789 19th Street','Los Angelas','CA','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(20,'Company T','Li','George',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','789 20th Street','New York','NY','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(21,'Company U','Tham','Bernard',NULL,'Accounting Manager','(123)555-0100',NULL,NULL,'(123)555-0101','789 21th Street','Minneapolis','MN','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(22,'Company V','Ramos','Luciana',NULL,'Purchasing Assistant','(123)555-0100',NULL,NULL,'(123)555-0101','789 22th Street','Milwaukee','WI','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(23,'Company W','Entin','Michael',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','789 23th Street','Portland','OR','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(24,'Company X','Hasselberg','Jonas',NULL,'Owner','(123)555-0100',NULL,NULL,'(123)555-0101','789 24th Street','Salt Lake City','UT','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(25,'Company Y','Rodman','John',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','789 25th Street','Chicago','IL','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(26,'Company Z','Liu','Run',NULL,'Accounting Assistant','(123)555-0100',NULL,NULL,'(123)555-0101','789 26th Street','Miami','FL','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(27,'Company AA','Toh','Karen',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','789 27th Street','Las Vegas','NV','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(28,'Company BB','Raghav','Amritansh',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','789 28th Street','Memphis','TN','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(29,'Company CC','Lee','Soo Jung',NULL,'Purchasing Manager','(123)555-0100',NULL,NULL,'(123)555-0101','789 29th Street','Denver','CO','99999','USA',NULL,NULL,'','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `emailAddress` varchar(50) DEFAULT NULL,
  `jobTitle` varchar(50) DEFAULT NULL,
  `businessPhone` varchar(25) DEFAULT NULL,
  `homePhone` varchar(25) DEFAULT NULL,
  `mobilePhone` varchar(25) DEFAULT NULL,
  `faxNumber` varchar(25) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `stateProvince` varchar(50) DEFAULT NULL,
  `zipPostalCode` varchar(15) DEFAULT NULL,
  `countryRegion` varchar(50) DEFAULT NULL,
  `webPage` longtext DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `attachments` longblob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `city` (`city`),
  KEY `company` (`company`),
  KEY `firstName` (`firstName`),
  KEY `lastName` (`lastName`),
  KEY `zipPostalCode` (`zipPostalCode`),
  KEY `stateProvince` (`stateProvince`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
INSERT INTO `employee` VALUES (1,'Northwind Traders','Freehafer','Nancy','nancy@northwindtraders.com','Sales Representative','(123)555-0100','(123)555-0102',NULL,'(123)555-0103','123 1st Avenue','Seattle','WA','99999','USA','#http://northwindtraders.com#',NULL,''),(2,'Northwind Traders','Cencini','Andrew','andrew@northwindtraders.com','Vice President, Sales','(123)555-0100','(123)555-0102',NULL,'(123)555-0103','123 2nd Avenue','Bellevue','WA','99999','USA','http://northwindtraders.com#http://northwindtraders.com/#','Joined the company as a sales representative, was promoted to sales manager and was then named vice president of sales.',''),(3,'Northwind Traders','Kotas','Jan','jan@northwindtraders.com','Sales Representative','(123)555-0100','(123)555-0102',NULL,'(123)555-0103','123 3rd Avenue','Redmond','WA','99999','USA','http://northwindtraders.com#http://northwindtraders.com/#','Was hired as a sales associate and was promoted to sales representative.',''),(4,'Northwind Traders','Sergienko','Mariya','mariya@northwindtraders.com','Sales Representative','(123)555-0100','(123)555-0102',NULL,'(123)555-0103','123 4th Avenue','Kirkland','WA','99999','USA','http://northwindtraders.com#http://northwindtraders.com/#',NULL,''),(5,'Northwind Traders','Thorpe','Steven','steven@northwindtraders.com','Sales Manager','(123)555-0100','(123)555-0102',NULL,'(123)555-0103','123 5th Avenue','Seattle','WA','99999','USA','http://northwindtraders.com#http://northwindtraders.com/#','Joined the company as a sales representative and was promoted to sales manager.  Fluent in French.',''),(6,'Northwind Traders','Neipper','Michael','michael@northwindtraders.com','Sales Representative','(123)555-0100','(123)555-0102',NULL,'(123)555-0103','123 6th Avenue','Redmond','WA','99999','USA','http://northwindtraders.com#http://northwindtraders.com/#','Fluent in Japanese and can read and write French, Portuguese, and Spanish.',''),(7,'Northwind Traders','Zare','Robert','robert@northwindtraders.com','Sales Representative','(123)555-0100','(123)555-0102',NULL,'(123)555-0103','123 7th Avenue','Seattle','WA','99999','USA','http://northwindtraders.com#http://northwindtraders.com/#',NULL,''),(8,'Northwind Traders','Giussani','Laura','laura@northwindtraders.com','Sales Coordinator','(123)555-0100','(123)555-0102',NULL,'(123)555-0103','123 8th Avenue','Redmond','WA','99999','USA','http://northwindtraders.com#http://northwindtraders.com/#','Reads and writes French.',''),(9,'Northwind Traders','Hellung-Larsen','Anne','anne@northwindtraders.com','Sales Representative','(123)555-0100','(123)555-0102',NULL,'(123)555-0103','123 9th Avenue','Seattle','WA','99999','USA','http://northwindtraders.com#http://northwindtraders.com/#','Fluent in French and German.','');
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employeePrivilege`
--

DROP TABLE IF EXISTS `employeePrivilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employeePrivilege` (
  `employee_id` int(11) NOT NULL,
  `privilege_id` int(11) NOT NULL,
  PRIMARY KEY (`employee_id`,`privilege_id`),
  KEY `employee_id` (`employee_id`),
  KEY `privilege_id` (`privilege_id`),
  CONSTRAINT `fkEmployeePrivilegeEmployees1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fkEmployeePrivilegePrivilege1` FOREIGN KEY (`privilege_id`) REFERENCES `privilege` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employeePrivilege`
--

LOCK TABLES `employeePrivilege` WRITE;
/*!40000 ALTER TABLE `employeePrivilege` DISABLE KEYS */;
INSERT INTO `employeePrivilege` VALUES (2,2);
/*!40000 ALTER TABLE `employeePrivilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventoryTransactionType`
--

DROP TABLE IF EXISTS `inventoryTransactionType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventoryTransactionType` (
  `id` tinyint(4) NOT NULL,
  `typeName` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventoryTransactionType`
--

LOCK TABLES `inventoryTransactionType` WRITE;
/*!40000 ALTER TABLE `inventoryTransactionType` DISABLE KEYS */;
INSERT INTO `inventoryTransactionType` VALUES (1,'Purchased'),(2,'Sold'),(3,'On Hold'),(4,'Waste');
/*!40000 ALTER TABLE `inventoryTransactionType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderDetailStat`
--

DROP TABLE IF EXISTS `orderDetailStat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderDetailStat` (
  `id` int(11) NOT NULL,
  `statusName` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderDetailStat`
--

LOCK TABLES `orderDetailStat` WRITE;
/*!40000 ALTER TABLE `orderDetailStat` DISABLE KEYS */;
INSERT INTO `orderDetailStat` VALUES (0,'None'),(1,'Allocated'),(2,'Invoiced'),(3,'Shipped'),(4,'On Order'),(5,'No Stock');
/*!40000 ALTER TABLE `orderDetailStat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderStat`
--

DROP TABLE IF EXISTS `orderStat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderStat` (
  `id` tinyint(4) NOT NULL,
  `statusName` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderStat`
--

LOCK TABLES `orderStat` WRITE;
/*!40000 ALTER TABLE `orderStat` DISABLE KEYS */;
INSERT INTO `orderStat` VALUES (0,'New'),(1,'Invoiced'),(2,'Shipped'),(3,'Closed');
/*!40000 ALTER TABLE `orderStat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderTaxStat`
--

DROP TABLE IF EXISTS `orderTaxStat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderTaxStat` (
  `id` tinyint(4) NOT NULL,
  `taxStatName` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderTaxStat`
--

LOCK TABLES `orderTaxStat` WRITE;
/*!40000 ALTER TABLE `orderTaxStat` DISABLE KEYS */;
INSERT INTO `orderTaxStat` VALUES (0,'Tax Exempt'),(1,'Taxable');
/*!40000 ALTER TABLE `orderTaxStat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `privilege`
--

DROP TABLE IF EXISTS `privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `privilege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `privilegeName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `privilege`
--

LOCK TABLES `privilege` WRITE;
/*!40000 ALTER TABLE `privilege` DISABLE KEYS */;
INSERT INTO `privilege` VALUES (2,'Purchase Approvals');
/*!40000 ALTER TABLE `privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `supplier_ids` longtext DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productCode` varchar(25) DEFAULT NULL,
  `productName` varchar(50) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `standardCost` decimal(19,4) DEFAULT 0.0000,
  `listPrice` decimal(19,4) NOT NULL DEFAULT 0.0000,
  `reorderLevel` int(11) DEFAULT NULL,
  `targetLevel` int(11) DEFAULT NULL,
  `quantityPerUnit` varchar(50) DEFAULT NULL,
  `discontinued` tinyint(1) NOT NULL DEFAULT 0,
  `minimumReorderQuantity` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `attachments` longblob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `productCode` (`productCode`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES ('4',1,'NWTB-1','Northwind Traders Chai',NULL,13.5000,18.0000,10,40,'10 boxes x 20 bags',0,10,'Beverages',''),('10',3,'NWTCO-3','Northwind Traders Syrup',NULL,7.5000,10.0000,25,100,'12 - 550 ml bottles',0,25,'Condiments',''),('10',4,'NWTCO-4','Northwind Traders Cajun Seasoning',NULL,16.5000,22.0000,10,40,'48 - 6 oz jars',0,10,'Condiments',''),('10',5,'NWTO-5','Northwind Traders Olive Oil',NULL,16.0125,21.3500,10,40,'36 boxes',0,10,'Oil',''),('2;6',6,'NWTJP-6','Northwind Traders Boysenberry Spread',NULL,18.7500,25.0000,25,100,'12 - 8 oz jars',0,25,'Jams, Preserves',''),('2',7,'NWTDFN-7','Northwind Traders Dried Pears',NULL,22.5000,30.0000,10,40,'12 - 1 lb pkgs.',0,10,'Dried Fruit & Nuts',''),('8',8,'NWTS-8','Northwind Traders Curry Sauce',NULL,30.0000,40.0000,10,40,'12 - 12 oz jars',0,10,'Sauces',''),('2;6',14,'NWTDFN-14','Northwind Traders Walnuts',NULL,17.4375,23.2500,10,40,'40 - 100 g pkgs.',0,10,'Dried Fruit & Nuts',''),('6',17,'NWTCFV-17','Northwind Traders Fruit Cocktail',NULL,29.2500,39.0000,10,40,'15.25 OZ',0,10,'Canned Fruit & Vegetables',''),('1',19,'NWTBGM-19','Northwind Traders Chocolate Biscuits Mix',NULL,6.9000,9.2000,5,20,'10 boxes x 12 pieces',0,5,'Baked Goods & Mixes',''),('2;6',20,'NWTJP-6','Northwind Traders Marmalade',NULL,60.7500,81.0000,10,40,'30 gift boxes',0,10,'Jams, Preserves',''),('1',21,'NWTBGM-21','Northwind Traders Scones',NULL,7.5000,10.0000,5,20,'24 pkgs. x 4 pieces',0,5,'Baked Goods & Mixes',''),('4',34,'NWTB-34','Northwind Traders Beer',NULL,10.5000,14.0000,15,60,'24 - 12 oz bottles',0,15,'Beverages',''),('7',40,'NWTCM-40','Northwind Traders Crab Meat',NULL,13.8000,18.4000,30,120,'24 - 4 oz tins',0,30,'Canned Meat',''),('6',41,'NWTSO-41','Northwind Traders Clam Chowder',NULL,7.2375,9.6500,10,40,'12 - 12 oz cans',0,10,'Soups',''),('3;4',43,'NWTB-43','Northwind Traders Coffee',NULL,34.5000,46.0000,25,100,'16 - 500 g tins',0,25,'Beverages',''),('10',48,'NWTCA-48','Northwind Traders Chocolate',NULL,9.5625,12.7500,25,100,'10 pkgs',0,25,'Candy',''),('2',51,'NWTDFN-51','Northwind Traders Dried Apples',NULL,39.7500,53.0000,10,40,'50 - 300 g pkgs.',0,10,'Dried Fruit & Nuts',''),('1',52,'NWTG-52','Northwind Traders Long Grain Rice',NULL,5.2500,7.0000,25,100,'16 - 2 kg boxes',0,25,'Grains',''),('1',56,'NWTP-56','Northwind Traders Gnocchi',NULL,28.5000,38.0000,30,120,'24 - 250 g pkgs.',0,30,'Pasta',''),('1',57,'NWTP-57','Northwind Traders Ravioli',NULL,14.6250,19.5000,20,80,'24 - 250 g pkgs.',0,20,'Pasta',''),('8',65,'NWTS-65','Northwind Traders Hot Pepper Sauce',NULL,15.7875,21.0500,10,40,'32 - 8 oz bottles',0,10,'Sauces',''),('8',66,'NWTS-66','Northwind Traders Tomato Sauce',NULL,12.7500,17.0000,20,80,'24 - 8 oz jars',0,20,'Sauces',''),('5',72,'NWTD-72','Northwind Traders Mozzarella',NULL,26.1000,34.8000,10,40,'24 - 200 g pkgs.',0,10,'Dairy product',''),('2;6',74,'NWTDFN-74','Northwind Traders Almonds',NULL,7.5000,10.0000,5,20,'5 kg pkg.',0,5,'Dried Fruit & Nuts',''),('10',77,'NWTCO-77','Northwind Traders Mustard',NULL,9.7500,13.0000,15,60,'12 boxes',0,15,'Condiments',''),('2',80,'NWTDFN-80','Northwind Traders Dried Plums',NULL,3.0000,3.5000,50,75,'1 lb bag',0,25,'Dried Fruit & Nuts',''),('3',81,'NWTB-81','Northwind Traders Green Tea',NULL,2.0000,2.9900,100,125,'20 bags per box',0,25,'Beverages',''),('1',82,'NWTC-82','Northwind Traders Granola',NULL,2.0000,4.0000,20,100,NULL,0,NULL,'Cereal',''),('9',83,'NWTCS-83','Northwind Traders Potato Chips',NULL,0.5000,1.8000,30,200,NULL,0,NULL,'Chips, Snacks',''),('1',85,'NWTBGM-85','Northwind Traders Brownie Mix',NULL,9.0000,12.4900,10,20,'3 boxes',0,5,'Baked Goods & Mixes',''),('1',86,'NWTBGM-86','Northwind Traders Cake Mix',NULL,10.5000,15.9900,10,20,'4 boxes',0,5,'Baked Goods & Mixes',''),('7',87,'NWTB-87','Northwind Traders Tea',NULL,2.0000,4.0000,20,50,'100 count per box',0,NULL,'Beverages',''),('6',88,'NWTCFV-88','Northwind Traders Pears',NULL,1.0000,1.3000,10,40,'15.25 OZ',0,NULL,'Canned Fruit & Vegetables',''),('6',89,'NWTCFV-89','Northwind Traders Peaches',NULL,1.0000,1.5000,10,40,'15.25 OZ',0,NULL,'Canned Fruit & Vegetables',''),('6',90,'NWTCFV-90','Northwind Traders Pineapple',NULL,1.0000,1.8000,10,40,'15.25 OZ',0,NULL,'Canned Fruit & Vegetables',''),('6',91,'NWTCFV-91','Northwind Traders Cherry Pie Filling',NULL,1.0000,2.0000,10,40,'15.25 OZ',0,NULL,'Canned Fruit & Vegetables',''),('6',92,'NWTCFV-92','Northwind Traders Green Beans',NULL,1.0000,1.2000,10,40,'14.5 OZ',0,NULL,'Canned Fruit & Vegetables',''),('6',93,'NWTCFV-93','Northwind Traders Corn',NULL,1.0000,1.2000,10,40,'14.5 OZ',0,NULL,'Canned Fruit & Vegetables',''),('6',94,'NWTCFV-94','Northwind Traders Peas',NULL,1.0000,1.5000,10,40,'14.5 OZ',0,NULL,'Canned Fruit & Vegetables',''),('7',95,'NWTCM-95','Northwind Traders Tuna Fish',NULL,0.5000,2.0000,30,50,'5 oz',0,NULL,'Canned Meat',''),('7',96,'NWTCM-96','Northwind Traders Smoked Salmon',NULL,2.0000,4.0000,30,50,'5 oz',0,NULL,'Canned Meat',''),('1',97,'NWTC-82','Northwind Traders Hot Cereal',NULL,3.0000,5.0000,50,200,NULL,0,NULL,'Cereal',''),('6',98,'NWTSO-98','Northwind Traders Vegetable Soup',NULL,1.0000,1.8900,100,200,NULL,0,NULL,'Soups',''),('6',99,'NWTSO-99','Northwind Traders Chicken Soup',NULL,1.0000,1.9500,100,200,NULL,0,NULL,'Soups','');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchaseOrderStat`
--

DROP TABLE IF EXISTS `purchaseOrderStat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchaseOrderStat` (
  `id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchaseOrderStat`
--

LOCK TABLES `purchaseOrderStat` WRITE;
/*!40000 ALTER TABLE `purchaseOrderStat` DISABLE KEYS */;
INSERT INTO `purchaseOrderStat` VALUES (0,'New'),(1,'Submitted'),(2,'Approved'),(3,'Closed');
/*!40000 ALTER TABLE `purchaseOrderStat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salesReport`
--

DROP TABLE IF EXISTS `salesReport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salesReport` (
  `groupBy` varchar(50) NOT NULL,
  `display` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `filterRowSource` longtext DEFAULT NULL,
  `default` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`groupBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salesReport`
--

LOCK TABLES `salesReport` WRITE;
/*!40000 ALTER TABLE `salesReport` DISABLE KEYS */;
INSERT INTO `salesReport` VALUES ('Category','Category','Sales By Category','SELECT DISTINCT [Category] FROM [product] ORDER BY [Category];',0),('countryRegion','Country/Region','Sales By Country','SELECT DISTINCT [countryRegion] FROM [customer Extended] ORDER BY [countryRegion];',0),('Customer ID','Customer','Sales By Customer','SELECT DISTINCT [Company] FROM [customer Extended] ORDER BY [Company];',0),('employee_id','Employee','Sales By Employee','SELECT DISTINCT [Employee Name] FROM [employee Extended] ORDER BY [Employee Name];',0),('Product ID','Product','Sales by Product','SELECT DISTINCT [Product Name] FROM [product] ORDER BY [Product Name];',1);
/*!40000 ALTER TABLE `salesReport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipper`
--

DROP TABLE IF EXISTS `shipper`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `emailAddress` varchar(50) DEFAULT NULL,
  `jobTitle` varchar(50) DEFAULT NULL,
  `businessPhone` varchar(25) DEFAULT NULL,
  `homePhone` varchar(25) DEFAULT NULL,
  `mobilePhone` varchar(25) DEFAULT NULL,
  `faxNumber` varchar(25) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `stateProvince` varchar(50) DEFAULT NULL,
  `zipPostalCode` varchar(15) DEFAULT NULL,
  `countryRegion` varchar(50) DEFAULT NULL,
  `webPage` longtext DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `attachments` longblob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `city` (`city`),
  KEY `company` (`company`),
  KEY `firstName` (`firstName`),
  KEY `lastName` (`lastName`),
  KEY `zipPostalCode` (`zipPostalCode`),
  KEY `stateProvince` (`stateProvince`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipper`
--

LOCK TABLES `shipper` WRITE;
/*!40000 ALTER TABLE `shipper` DISABLE KEYS */;
INSERT INTO `shipper` VALUES (1,'Shipping Company A',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123 Any Street','Memphis','TN','99999','USA',NULL,NULL,''),(2,'Shipping Company B',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123 Any Street','Memphis','TN','99999','USA',NULL,NULL,''),(3,'Shipping Company C',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123 Any Street','Memphis','TN','99999','USA',NULL,NULL,'');
/*!40000 ALTER TABLE `shipper` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `emailAddress` varchar(50) DEFAULT NULL,
  `jobTitle` varchar(50) DEFAULT NULL,
  `businessPhone` varchar(25) DEFAULT NULL,
  `homePhone` varchar(25) DEFAULT NULL,
  `mobilePhone` varchar(25) DEFAULT NULL,
  `faxNumber` varchar(25) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `stateProvince` varchar(50) DEFAULT NULL,
  `zipPostalCode` varchar(15) DEFAULT NULL,
  `countryRegion` varchar(50) DEFAULT NULL,
  `webPage` longtext DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `attachments` longblob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `city` (`city`),
  KEY `company` (`company`),
  KEY `firstName` (`firstName`),
  KEY `lastName` (`lastName`),
  KEY `zipPostalCode` (`zipPostalCode`),
  KEY `stateProvince` (`stateProvince`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier`
--

LOCK TABLES `supplier` WRITE;
/*!40000 ALTER TABLE `supplier` DISABLE KEYS */;
INSERT INTO `supplier` VALUES (1,'Supplier A','Andersen','Elizabeth A.',NULL,'Sales Manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,''),(2,'Supplier B','Weiler','Cornelia',NULL,'Sales Manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,''),(3,'Supplier C','Kelley','Madeleine',NULL,'Sales Representative',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,''),(4,'Supplier D','Sato','Naoki',NULL,'Marketing Manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,''),(5,'Supplier E','Hernandez-Echevarria','Amaya',NULL,'Sales Manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,''),(6,'Supplier F','Hayakawa','Satomi',NULL,'Marketing Assistant',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,''),(7,'Supplier G','Glasson','Stuart',NULL,'Marketing Manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,''),(8,'Supplier H','Dunton','Bryn Paul',NULL,'Sales Representative',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,''),(9,'Supplier I','Sandberg','Mikael',NULL,'Sales Manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,''),(10,'Supplier J','Sousa','Luis',NULL,'Sales Manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'');
/*!40000 ALTER TABLE `supplier` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-01-01 15:15:49