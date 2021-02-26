/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-08-07 16:47:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_goods`;
CREATE TABLE `opr_order_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL COMMENT '物品id',
  `order_id` int(10) unsigned NOT NULL COMMENT '訂單id',
  `goods_num` varchar(10) NOT NULL COMMENT '物品數量',
  `confirm_num` varchar(10) DEFAULT NULL COMMENT '實際數量',
  `note` varchar(255) DEFAULT NULL COMMENT '要求備註',
  `remark` varchar(255) DEFAULT NULL COMMENT '總部備註',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='訂單內的物品表';

-- ----------------------------
-- Table structure for opr_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `opr_warehouse`;
CREATE TABLE `opr_warehouse` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(20) DEFAULT NULL COMMENT '物品編號',
  `classify_id` varchar(11) DEFAULT NULL COMMENT '物品分類的id',
  `name` varchar(30) NOT NULL COMMENT '物品名字',
  `type` varchar(30) NOT NULL COMMENT '物品規格',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `price` float(30,2) NOT NULL DEFAULT '0.00' COMMENT '單價',
  `inventory` varchar(10) DEFAULT '0' COMMENT '庫存',
  `city` varchar(30) DEFAULT NULL COMMENT '地區',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='倉庫表';
