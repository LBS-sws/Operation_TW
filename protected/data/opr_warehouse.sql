/*
Navicat MySQL Data Transfer

Source Server         : vm1
Source Server Version : 50626
Source Host           : 192.168.1.10:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2017-07-10 15:18:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `opr_warehouse`;
CREATE TABLE `opr_warehouse` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(10) DEFAULT NULL COMMENT '物品編號',
  `name` varchar(30) NOT NULL COMMENT '物品名字',
  `type` varchar(30) NOT NULL COMMENT '物品規格',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `inventory` int(10) unsigned DEFAULT '0' COMMENT '庫存',
  `city` varchar(30) DEFAULT NULL COMMENT '地區',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='倉庫表';

-- ----------------------------
-- Records of opr_warehouse
-- ----------------------------
