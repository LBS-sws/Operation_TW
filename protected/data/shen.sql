/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-07-24 17:15:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_classify
-- ----------------------------
DROP TABLE IF EXISTS `opr_classify`;
CREATE TABLE `opr_classify` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '小分類的名字',
  `class_type` varchar(40) NOT NULL DEFAULT 'Import' COMMENT '所屬類型',
  `level` varchar(10) DEFAULT NULL COMMENT '級別',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='物品小分類表（暫時只給總部管理員使用）';

-- ----------------------------
-- Records of opr_classify
-- ----------------------------
INSERT INTO `opr_classify` VALUES ('7', '清潔及消毒劑  ', 'Import', '2', 'test', 'test', '2017-07-24 11:35:44', '2017-07-24 11:44:35');
INSERT INTO `opr_classify` VALUES ('8', '清潔', 'Fast', '', 'test', null, '2017-07-24 11:14:44', null);
INSERT INTO `opr_classify` VALUES ('9', '清潔', 'Domestic', '', 'test', null, '2017-07-24 11:17:46', null);
INSERT INTO `opr_classify` VALUES ('10', '清潔', 'Warehouse', '', 'test', null, '2017-07-24 14:00:23', null);
