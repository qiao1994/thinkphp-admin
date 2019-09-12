-- MySQL dump 10.13  Distrib 5.7.25, for linux-glibc2.12 (x86_64)
--
-- Host: localhost    Database: db1
-- ------------------------------------------------------
-- Server version	5.7.25-log

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
-- Table structure for table `db_action`
--

DROP TABLE IF EXISTS `db_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(500) NOT NULL COMMENT 'MODULE',
  `controller` varchar(500) NOT NULL COMMENT 'CONTROLLER',
  `action` varchar(500) NOT NULL COMMENT 'ACTION',
  `remark` text NOT NULL COMMENT '备注',
  `file` varchar(500) NOT NULL COMMENT '文件位置',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_action`
--

LOCK TABLES `db_action` WRITE;
/*!40000 ALTER TABLE `db_action` DISABLE KEYS */;
INSERT INTO `db_action` VALUES (1,'Home','IndexController','index','index()','./App/Home/Controller/IndexController.class.php',1517929501,0),(6,'Admin','IndexController','index','index()','./App/Admin/Controller/IndexController.class.php',1517929501,0),(7,'Admin','IndexController','identifyCode','identifyCode()','./App/Admin/Controller/IndexController.class.php',1517929501,0),(34,'Admin','IndexController','logout','logout()','./App/Admin/Controller/IndexController.class.php',1517929934,0),(35,'Common','UserModel','login','login($role_id = 1)','./App/Common/Model/UserModel.class.php',1517929934,0),(36,'Home','AboutController','index','index()','./App/Home/Controller/AboutController.class.php',1523758472,0),(37,'Home','CommonController','authorValidate','authorValidate()','./App/Home/Controller/CommonController.class.php',1523758472,0),(38,'Home','CommonController','identifyCode','identifyCode()','./App/Home/Controller/CommonController.class.php',1523758472,0),(39,'Home','CommonController','login','login($redirectUrl = \'\')','./App/Home/Controller/CommonController.class.php',1523758472,0),(40,'Home','CommonController','register','register($roleId = 2, $redirectUrl = \'\')','./App/Home/Controller/CommonController.class.php',1523758472,0),(41,'Home','CommonController','logout','logout($redirectUrl = \'\')','./App/Home/Controller/CommonController.class.php',1523758472,0),(42,'Home','CommonController','page','page()','./App/Home/Controller/CommonController.class.php',1523758472,0),(43,'Home','CommonController','handleSearchMap','handleSearchMap($searchMap = \'\')','./App/Home/Controller/CommonController.class.php',1523758472,0),(44,'Home','CommonController','getModelNameByTableName','getModelNameByTableName($tableName)','./App/Home/Controller/CommonController.class.php',1523758472,0);
/*!40000 ALTER TABLE `db_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_author`
--

DROP TABLE IF EXISTS `db_author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色[role,id,name]',
  `module` varchar(500) CHARACTER SET latin1 NOT NULL COMMENT '模块',
  `controller` varchar(500) NOT NULL COMMENT '控制器',
  `action` varchar(500) NOT NULL COMMENT '方法',
  `rule` text NOT NULL COMMENT '规则',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_author`
--

LOCK TABLES `db_author` WRITE;
/*!40000 ALTER TABLE `db_author` DISABLE KEYS */;
INSERT INTO `db_author` VALUES (1,1,'*','*','*','return true;',1498130836,1498133671),(2,1,'Admin','Author','update','if ($_GET[\'id\'] == 1) { //这里可以做常规的任何操作，会在具体的controller中执行,权限是从小到大匹配的(如果action有规则命中，不会继续匹配；否则会继续往controller[action为*]，module[controller为*])\r\n    return false;\r\n} else {\r\n    return true;\r\n}',1498133431,1498297659),(3,1,'Admin','System','update','return true;',0,1517471062),(4,0,'Home','*','*','return true;',1522151130,0);
/*!40000 ALTER TABLE `db_author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_case`
--

DROP TABLE IF EXISTS `db_case`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` varchar(500) NOT NULL COMMENT '标签',
  `title` varchar(500) NOT NULL COMMENT '标题',
  `image` varchar(500) NOT NULL COMMENT '图片',
  `summary` text NOT NULL COMMENT '摘要',
  `content` text NOT NULL COMMENT '内容',
  `browse` int(11) NOT NULL COMMENT '浏览量',
  `real_browse` int(11) NOT NULL COMMENT '真实浏览量',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_case`
--

LOCK TABLES `db_case` WRITE;
/*!40000 ALTER TABLE `db_case` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_case` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_category`
--

DROP TABLE IF EXISTS `db_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(500) NOT NULL COMMENT '分类',
  `sup_id` int(11) NOT NULL DEFAULT '0' COMMENT '上级分类',
  `name` varchar(500) NOT NULL COMMENT '名称',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_category`
--

LOCK TABLES `db_category` WRITE;
/*!40000 ALTER TABLE `db_category` DISABLE KEYS */;
INSERT INTO `db_category` VALUES (1,'Product',0,'网站建设',1522310882,1522311371),(2,'Product',0,'UI设计',1522311397,0),(3,'Product',1,'PC网站建设',1523696224,0),(4,'Product',1,'手机网站建设',1523696237,0),(5,'Product',2,'网页设计',1523696247,0),(6,'Product',2,'APP设计',1523696254,0),(7,'News',0,'公司新闻',0,0),(8,'News',0,'行业资讯',0,0);
/*!40000 ALTER TABLE `db_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_news`
--

DROP TABLE IF EXISTS `db_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT '分类',
  `title` varchar(500) NOT NULL COMMENT '标题',
  `image` varchar(500) NOT NULL COMMENT '图片',
  `tag_id` varchar(500) NOT NULL COMMENT '标签',
  `summary` text NOT NULL COMMENT '摘要',
  `content` text NOT NULL COMMENT '内容',
  `browse` int(11) NOT NULL COMMENT '浏览量',
  `real_browse` int(11) NOT NULL COMMENT '真实浏览量',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_news`
--

LOCK TABLES `db_news` WRITE;
/*!40000 ALTER TABLE `db_news` DISABLE KEYS */;
INSERT INTO `db_news` VALUES (3,8,'网站建设的一般流程','5ad35eae0ecb1.png','1','开发一个网站需要做哪些事情？一般来说，需要这些流程：需求沟通—原型设计—DB设计/UI设计—后台搭建/前端实现—接口对接—测试上线。','<p>开发一个网站需要做哪些事情？一般来说，需要这些流程：需求沟通—原型设计—DB设计/UI设计—后台搭建/前端实现—接口对接—测试上线。</p>',2485,570,1523801774,1523802199),(4,7,'模板建站和定制开发的区别','5ad36236b4a68.png','1','模板建站的优点是成本低，缺点是系统一般臃肿复杂，漏洞多，上线后需要面对的问题很多。定制开发是根据需求量身定制，缺点是成本高，开发周期长，但是一般更加稳定易用，后续使用中的问题也会更少。','<p>模板建站的优点是成本低，缺点是系统一般臃肿复杂，漏洞多，上线后需要面对的问题很多。定制开发是根据需求量身定制，缺点是成本高，开发周期长，但是一般更加稳定易用，后续使用中的问题也会更少。</p>',2598,563,1523802380,1523803590),(5,7,'UI设计的本质','5ad362241a0d0.jpg','2','为什么要有UI设计，UI设计的本质诉求是什么？UI设计的目的是让应用更加易用，让受众更加快速深刻的理解意图，找到需要的功能。','&lt;p&gt;为什么要有UI设计，UI设计的本质诉求是什么？UI设计的目的是让应用更加易用，让受众更加快速深刻的理解意图，找到需要的功能。&lt;/p&gt;',2647,522,1523802660,0);
/*!40000 ALTER TABLE `db_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_product`
--

DROP TABLE IF EXISTS `db_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` varchar(500) NOT NULL COMMENT '分类',
  `title` varchar(500) NOT NULL COMMENT '标题',
  `image` varchar(500) NOT NULL COMMENT '图片',
  `banner` varchar(500) NOT NULL COMMENT '滑动图',
  `tag_id` varchar(500) NOT NULL COMMENT '标签',
  `summary` text NOT NULL COMMENT '摘要',
  `content` text NOT NULL COMMENT '内容',
  `browse` int(11) NOT NULL COMMENT '浏览量',
  `real_browse` int(11) NOT NULL COMMENT '真实浏览量',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_product`
--

LOCK TABLES `db_product` WRITE;
/*!40000 ALTER TABLE `db_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_role`
--

DROP TABLE IF EXISTS `db_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL COMMENT '名称',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='角色';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_role`
--

LOCK TABLES `db_role` WRITE;
/*!40000 ALTER TABLE `db_role` DISABLE KEYS */;
INSERT INTO `db_role` VALUES (1,'管理员',1498130470,1498187811),(2,'用户',1498130476,1498187843);
/*!40000 ALTER TABLE `db_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_system`
--

DROP TABLE IF EXISTS `db_system`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL COMMENT '系统名称',
  `description` varchar(250) NOT NULL COMMENT '系统描述',
  `keyword` varchar(250) NOT NULL COMMENT '系统关键字',
  `logo` varchar(255) NOT NULL COMMENT '系统logo路径',
  `qrcode` varchar(500) NOT NULL COMMENT '微信二维码',
  `qq` varchar(500) NOT NULL COMMENT 'QQ',
  `tel` varchar(500) NOT NULL COMMENT '电话',
  `address` varchar(500) NOT NULL COMMENT '地址',
  `copyright1` varchar(500) NOT NULL COMMENT '版权',
  `copyright2` varchar(500) NOT NULL COMMENT '版权2',
  `url` text NOT NULL COMMENT '域名',
  `about` text NOT NULL COMMENT '关于我们',
  `menu` text NOT NULL COMMENT '菜单信息',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_system`
--

LOCK TABLES `db_system` WRITE;
/*!40000 ALTER TABLE `db_system` DISABLE KEYS */;
INSERT INTO `db_system` VALUES (1,'XXX','XXX','XXX','5d7a06085222e.png','5d7a060852919.png','XXX','XXX','XXX','Powered by XXX','XXX','XXX','<p>XXX</p>','{\r\n    \"System\":\r\n    {\r\n        \"icon\": \"icon-dashboard\",\r\n        \"action\":\r\n        {\r\n            \"index\":\r\n            {\r\n                \"title\": \"\\u7cfb\\u7edf\\u6982\\u51b5\",\r\n                \"href\": \"\\/Admin\\/System\\/index.html\"\r\n            },\r\n            \"update\":\r\n            {\r\n                \"title\": \"\\u7cfb\\u7edf\\u8bbe\\u7f6e\",\r\n                \"href\": \"\\/Admin\\/System\\/update\\/id\\/1.html\"\r\n            }\r\n        }\r\n    },\r\n    \"Action\": [],\r\n    \"Role\": [],\r\n    \"Author\": [],\r\n    \"User\": [],\r\n    \"Tag\": [],\r\n    \"Category\": [],\r\n    \"News\": [],\r\n    \"Product\": [],\r\n    \"Case\": []\r\n}',0,1568278024);
/*!40000 ALTER TABLE `db_system` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_tag`
--

DROP TABLE IF EXISTS `db_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_tag`
--

LOCK TABLES `db_tag` WRITE;
/*!40000 ALTER TABLE `db_tag` DISABLE KEYS */;
INSERT INTO `db_tag` VALUES (1,'网站开发',1522311039,0),(2,'UI设计',1522311048,0);
/*!40000 ALTER TABLE `db_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_user`
--

DROP TABLE IF EXISTS `db_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `name` varchar(500) NOT NULL,
  `password` varchar(60) NOT NULL COMMENT '密码',
  `role_id` int(100) NOT NULL COMMENT '用户角色',
  `state` varchar(4) NOT NULL DEFAULT '正常',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1040 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_user`
--

LOCK TABLES `db_user` WRITE;
/*!40000 ALTER TABLE `db_user` DISABLE KEYS */;
INSERT INTO `db_user` VALUES (1,'admin','管理员','$2y$10$3rbDxEwq/SjCxPVeWl.1buppwdsf28zyp0oIrx5DzIJI33TbhEfi.',1,'正常',1463233041,1517470641);
/*!40000 ALTER TABLE `db_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-09-12 16:54:57
