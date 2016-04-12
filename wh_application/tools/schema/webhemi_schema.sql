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
-- Table structure for table `webhemi_application`
--

DROP TABLE IF EXISTS `webhemi_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_application` (
  `id_application` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `is_read_only` TINYINT(1) NOT NULL DEFAULT 0,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `meta_data` TEXT DEFAULT '',
  PRIMARY KEY (`id_application`),
  UNIQUE KEY `unq_application_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_application`
--

LOCK TABLES `webhemi_application` WRITE;
/*!40000 ALTER TABLE `webhemi_application` DISABLE KEYS */;
INSERT INTO `webhemi_application` VALUES
  (1, 'Admin',   1, 'Administrative area.', ''),
  (2, 'Website', 1, 'The default application for the `www` subdomain.', '');
/*!40000 ALTER TABLE `webhemi_application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_acl_role`
--

DROP TABLE IF EXISTS `webhemi_acl_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_acl_role` (
  `id_acl_role` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `is_read_only` TINYINT(1) NOT NULL DEFAULT 0,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_acl_role`),
  UNIQUE KEY `unq_acl_role_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_acl_role`
--

LOCK TABLES `webhemi_acl_role` WRITE;
/*!40000 ALTER TABLE `webhemi_acl_role` DISABLE KEYS */;
INSERT INTO `webhemi_acl_role` VALUES
  (1, 'admin',     1, 'An admin is the GOD of the application.'),
  (2, 'publisher', 1, 'A publisher can moderate all content and decide what, where and when can be published.'),
  (3, 'editor',    1, 'An editor can create contents and supervise the members\' activities.'),
  (4, 'moderator', 1, 'A moderator can supervise the members\' activities.'),
  (5, 'member',    1, 'A visitor with account. Basic access and activities.'),
  (6, 'guest',     1, 'A visitor of the site, who doesn\'t have any privileges.');
/*!40000 ALTER TABLE `webhemi_acl_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_acl_resource`
--

DROP TABLE IF EXISTS `webhemi_acl_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_acl_resource` (
  `id_acl_resource` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `is_read_only` TINYINT(1) NOT NULL DEFAULT 0,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_acl_resource`),
  UNIQUE KEY `unq_alc_resource_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_acl_resource`
--

LOCK TABLES `webhemi_acl_resource` WRITE;
/*!40000 ALTER TABLE `webhemi_acl_resource` DISABLE KEYS */;
INSERT INTO `webhemi_acl_resource` VALUES
  (1,  'admin:index',                   1, ''),
  (2,  'admin:login',                   1, ''),
  (3,  'admin:logout',                  1, ''),
  (4,  'admin:about',                   1, ''),
  (5,  'admin:control-panel',           1, ''),
  (6,  'application:index',             1, ''),
  (7,  'application:add',               1, ''),
  (8,  'application:edit',              1, ''),
  (9,  'application:enable',            1, ''),
  (10, 'application:disable',           1, ''),
  (11, 'application:delete',            1, ''),
  (12, 'user-management:user-list',     1, ''),
  (13, 'user-management:user-view',     1, ''),
  (14, 'user-management:user-profile',  1, ''),
  (15, 'user-management:user-add',      1, ''),
  (16, 'user-management:user-edit',     1, ''),
  (17, 'user-management:user-enable',   1, ''),
  (18, 'user-management:user-disable',  1, ''),
  (19, 'user-management:user-activate', 1, ''),
  (20, 'user-management:user-delete',   1, ''),
  (21, 'user:login',                    1, ''),
  (22, 'user:logout',                   1, ''),
  (23, 'user:user-view',                1, ''),
  (24, 'user:user-profile',             1, ''),
  (25, 'user:user-profile-edit',        1, ''),
  (26, 'website:index',                 1, ''),
  (27, 'website:view',                  1, '');
/*!40000 ALTER TABLE `webhemi_acl_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_acl`
--

DROP TABLE IF EXISTS `webhemi_acl_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_acl_rule` (
  `id_acl_rule` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_acl_role` INT(10) UNSIGNED NOT NULL,
  `fk_acl_resource` INT(10) UNSIGNED NOT NULL,
  `is_allowed` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acl_rule`),
  KEY `idx_acl_rule_fk_acl_role` (`fk_acl_role`),
  KEY `idx_acl_rule_fk_acl_resource` (`fk_acl_resource`),
  CONSTRAINT `fk_acl_rule_fk_acl_role` FOREIGN KEY (`fk_acl_role`) REFERENCES `webhemi_acl_role` (`id_acl_role`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_acl_rule_fk_acl_resource ` FOREIGN KEY (`fk_acl_resource`) REFERENCES `webhemi_acl_resource` (`id_acl_resource`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Dumping data for table `webhemi_acl`
--

LOCK TABLES `webhemi_acl_rule` WRITE;
/*!40000 ALTER TABLE `webhemi_acl_rule` DISABLE KEYS */;
INSERT INTO `webhemi_acl_rule` VALUES
  (NULL, 1, 1, 1), (NULL, 1, 2, 1), (NULL, 1, 3, 1), (NULL, 1, 4, 1), (NULL, 1, 5, 1), (NULL, 1, 6, 1), (NULL, 1, 7, 1), (NULL, 1, 8, 1), (NULL, 1, 9, 1), (NULL, 1, 10, 1), (NULL, 1, 11, 1), (NULL, 1, 12, 1), (NULL, 1, 13, 1), (NULL, 1, 14, 1), (NULL, 1, 15, 1), (NULL, 1, 16, 1), (NULL, 1, 17, 1), (NULL, 1, 18, 1), (NULL, 1, 19, 1), (NULL, 1, 20, 1), (NULL, 1, 21, 1), (NULL, 1, 22, 1), (NULL, 1, 23, 1), (NULL, 1, 24, 1), (NULL, 1, 25, 1), (NULL, 1, 26, 1), (NULL, 1, 27, 1),
  (NULL, 2, 1, 1), (NULL, 2, 2, 1), (NULL, 2, 3, 1), (NULL, 2, 4, 1), (NULL, 2, 5, 1), (NULL, 2, 6, 0), (NULL, 2, 7, 0), (NULL, 2, 8, 0), (NULL, 2, 9, 0), (NULL, 2, 10, 0), (NULL, 2, 11, 0), (NULL, 2, 12, 1), (NULL, 2, 13, 1), (NULL, 2, 14, 1), (NULL, 2, 15, 0), (NULL, 2, 16, 1), (NULL, 2, 17, 0), (NULL, 2, 18, 0), (NULL, 2, 19, 0), (NULL, 2, 20, 0), (NULL, 2, 21, 1), (NULL, 2, 22, 1), (NULL, 2, 23, 1), (NULL, 2, 24, 1), (NULL, 2, 25, 1), (NULL, 2, 26, 1), (NULL, 2, 27, 1),
  (NULL, 3, 1, 1), (NULL, 3, 2, 1), (NULL, 3, 3, 1), (NULL, 3, 4, 1), (NULL, 3, 5, 1), (NULL, 3, 6, 0), (NULL, 3, 7, 0), (NULL, 3, 8, 0), (NULL, 3, 9, 0), (NULL, 3, 10, 0), (NULL, 3, 11, 0), (NULL, 3, 12, 1), (NULL, 3, 13, 1), (NULL, 3, 14, 1), (NULL, 3, 15, 0), (NULL, 3, 16, 1), (NULL, 3, 17, 0), (NULL, 3, 18, 0), (NULL, 3, 19, 0), (NULL, 3, 20, 0), (NULL, 3, 21, 1), (NULL, 3, 22, 1), (NULL, 3, 23, 1), (NULL, 3, 24, 1), (NULL, 3, 25, 1), (NULL, 3, 26, 1), (NULL, 3, 27, 1),
  (NULL, 4, 1, 1), (NULL, 4, 2, 1), (NULL, 4, 3, 1), (NULL, 4, 4, 1), (NULL, 4, 5, 1), (NULL, 4, 6, 0), (NULL, 4, 7, 0), (NULL, 4, 8, 0), (NULL, 4, 9, 0), (NULL, 4, 10, 0), (NULL, 4, 11, 0), (NULL, 4, 12, 1), (NULL, 4, 13, 1), (NULL, 4, 14, 1), (NULL, 4, 15, 0), (NULL, 4, 16, 1), (NULL, 4, 17, 0), (NULL, 4, 18, 0), (NULL, 4, 19, 0), (NULL, 4, 20, 0), (NULL, 4, 21, 1), (NULL, 4, 22, 1), (NULL, 4, 23, 1), (NULL, 4, 24, 1), (NULL, 4, 25, 1), (NULL, 4, 26, 1), (NULL, 4, 27, 1),
  (NULL, 5, 1, 1), (NULL, 5, 2, 1), (NULL, 5, 3, 1), (NULL, 5, 4, 1), (NULL, 5, 5, 1), (NULL, 5, 6, 0), (NULL, 5, 7, 0), (NULL, 5, 8, 0), (NULL, 5, 9, 0), (NULL, 5, 10, 0), (NULL, 5, 11, 0), (NULL, 5, 12, 1), (NULL, 5, 13, 1), (NULL, 5, 14, 1), (NULL, 5, 15, 0), (NULL, 5, 16, 1), (NULL, 5, 17, 0), (NULL, 5, 18, 0), (NULL, 5, 19, 0), (NULL, 5, 20, 0), (NULL, 5, 21, 1), (NULL, 5, 22, 1), (NULL, 5, 23, 1), (NULL, 5, 24, 1), (NULL, 5, 25, 1), (NULL, 5, 26, 1), (NULL, 5, 27, 1),
  (NULL, 6, 1, 0), (NULL, 6, 2, 1), (NULL, 6, 3, 0), (NULL, 6, 4, 0), (NULL, 6, 5, 0), (NULL, 6, 6, 0), (NULL, 6, 7, 0), (NULL, 6, 8, 0), (NULL, 6, 9, 0), (NULL, 6, 10, 0), (NULL, 6, 11, 0), (NULL, 6, 12, 0), (NULL, 6, 13, 0), (NULL, 6, 14, 0), (NULL, 6, 15, 0), (NULL, 6, 16, 0), (NULL, 6, 17, 0), (NULL, 6, 18, 0), (NULL, 6, 19, 0), (NULL, 6, 20, 0), (NULL, 6, 21, 1), (NULL, 6, 22, 0), (NULL, 6, 23, 1), (NULL, 6, 24, 0), (NULL, 6, 25, 0), (NULL, 6, 26, 1), (NULL, 6, 27, 1);
/*!40000 ALTER TABLE `webhemi_acl_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_acl_lock`
--

DROP TABLE IF EXISTS `webhemi_client_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_client_lock` (
  `id_client_lock` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_ip` varchar(15) NOT NULL,
  `tryings` int(10) unsigned NOT NULL DEFAULT '0',
  `time_lock` datetime DEFAULT NULL,
  PRIMARY KEY (`id_client_lock`),
  KEY `idx_client_lock_client_ip` (`client_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_user`
--

DROP TABLE IF EXISTS `webhemi_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user` (
  `id_user` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(60) NOT NULL,
  `hash` VARCHAR(32) DEFAULT '',
  `last_ip` VARCHAR(15) DEFAULT NULL,
  `register_ip` VARCHAR(15) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT '0',
  `is_enabled` TINYINT(1) NOT NULL DEFAULT '0',
  `time_login` DATETIME DEFAULT NULL,
  `time_register` DATETIME NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `unq_user_username` (`username`),
  UNIQUE KEY `unq_user_email` (`email`),
  KEY `idx_user_password` (`password`),
  KEY `idx_user_is_active` (`is_active`),
  KEY `idx_user_is_enabled` (`is_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user`
--

LOCK TABLES `webhemi_user` WRITE;
/*!40000 ALTER TABLE `webhemi_user` DISABLE KEYS */;
INSERT INTO `webhemi_user` VALUES
  (1,'admin','admin@foo.org','$2y$09$dmrDfcYZt9jORA4vx9MKpeyRt0ilCH/gxSbSHcfBtGaghMJ30tKzS','',NULL,'127.0.0.1',1,1,NOW(),NOW());
/*!40000 ALTER TABLE `webhemi_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_meta`
--

DROP TABLE IF EXISTS `webhemi_user_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_meta` (
  `fk_user` INT(10) UNSIGNED NOT NULL,
  `meta_key` VARCHAR(255) NOT NULL,
  `meta_data` LONGTEXT NOT NULL,
  PRIMARY KEY (`fk_user`,`meta_key`),
  CONSTRAINT `fk_user_meta_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `webhemi_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_meta`
--

LOCK TABLES `webhemi_user_meta` WRITE;
/*!40000 ALTER TABLE `webhemi_user_meta` DISABLE KEYS */;
INSERT INTO `webhemi_user_meta` VALUES
  (1,'avatar',''),
  (1,'details',''),
  (1,'displayEmail','0'),
  (1,'displayName','Admin'),
  (1,'headLine',''),
  (1,'instantMessengers',''),
  (1,'location',''),
  (1,'phoneNumbers',''),
  (1,'socialNetworks',''),
  (1,'websites','');
/*!40000 ALTER TABLE `webhemi_user_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_acl`
--

DROP TABLE IF EXISTS `webhemi_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_role` (
  `fk_user` INT(10) UNSIGNED NOT NULL,
  `fk_application` INT(10) UNSIGNED NOT NULL,
  `fk_acl_role` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`fk_user`,`fk_application`),
  KEY `idx_user_acl_fk_user` (`fk_user`),
  KEY `idx_user_acl_fk_application` (`fk_application`),
  KEY `idx_user_acl_fk_acl_role` (`fk_acl_role`),
  CONSTRAINT `fk_user_acl_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `webhemi_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_acl_fk_application` FOREIGN KEY (`fk_application`) REFERENCES `webhemi_application` (`id_application`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_acl_fk_acl_role` FOREIGN KEY (`fk_acl_role`) REFERENCES `webhemi_acl_role` (`id_acl_role`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_acl`
--

LOCK TABLES `webhemi_user_role` WRITE;
/*!40000 ALTER TABLE `webhemi_user_role` DISABLE KEYS */;
INSERT INTO `webhemi_user_role` VALUES
  (1,1,1),
  (1,2,1);
/*!40000 ALTER TABLE `webhemi_user_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
