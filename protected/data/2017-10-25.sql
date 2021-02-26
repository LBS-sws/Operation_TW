/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-10-25 16:24:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_goods_do
-- ----------------------------
DROP TABLE IF EXISTS `opr_goods_do`;
CREATE TABLE `opr_goods_do` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(100) DEFAULT NULL COMMENT '物品編號',
  `classify_id` int(10) DEFAULT NULL COMMENT '小分類的id',
  `name` varchar(200) NOT NULL COMMENT '物品名字',
  `type` varchar(30) DEFAULT NULL COMMENT '物品規格',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `price` varchar(30) DEFAULT NULL COMMENT '物品單價',
  `rules_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '混合規則id',
  `multiple` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '數量倍率',
  `big_num` int(10) unsigned DEFAULT '0' COMMENT '最大數量限制',
  `small_num` int(10) DEFAULT '0' COMMENT '最小數量限制',
  `origin` varchar(30) DEFAULT NULL COMMENT '來源地',
  `stickies_id` varchar(10) DEFAULT NULL COMMENT '標籤id',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8 COMMENT='物品表(只含有進口物品）';

-- ----------------------------
-- Table structure for opr_goods_fa
-- ----------------------------
DROP TABLE IF EXISTS `opr_goods_fa`;
CREATE TABLE `opr_goods_fa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(100) DEFAULT NULL COMMENT '物品編號',
  `classify_id` int(10) DEFAULT NULL COMMENT '小分類的id',
  `name` varchar(200) NOT NULL COMMENT '物品名字',
  `type` varchar(30) DEFAULT NULL COMMENT '物品規格',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `price` varchar(30) DEFAULT NULL COMMENT '物品單價',
  `rules_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '混合規則id',
  `multiple` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '數量倍率',
  `big_num` int(10) unsigned DEFAULT '0' COMMENT '總部數量限制',
  `small_num` int(10) DEFAULT '0' COMMENT '區域數量限制',
  `origin` varchar(30) DEFAULT NULL COMMENT '來源地',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Table structure for opr_goods_im
-- ----------------------------
DROP TABLE IF EXISTS `opr_goods_im`;
CREATE TABLE `opr_goods_im` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(100) DEFAULT NULL COMMENT '物品編號',
  `classify_id` int(10) DEFAULT NULL COMMENT '物品分類的id',
  `name` varchar(200) NOT NULL COMMENT '物品名字',
  `type` varchar(30) DEFAULT NULL COMMENT '物品規格',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `price` varchar(30) DEFAULT NULL COMMENT '物品單價',
  `net_weight` varchar(30) DEFAULT NULL COMMENT '净重',
  `gross_weight` varchar(30) DEFAULT NULL COMMENT '毛重',
  `len` varchar(11) DEFAULT NULL COMMENT '長',
  `width` varchar(11) DEFAULT NULL COMMENT '寬',
  `height` varchar(11) DEFAULT NULL COMMENT '高',
  `rules_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '混合規則id',
  `multiple` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '數量倍率',
  `big_num` int(10) unsigned DEFAULT '0' COMMENT '總部數量限制',
  `small_num` int(10) DEFAULT '0' COMMENT '區域數量限制',
  `origin` varchar(30) DEFAULT NULL COMMENT '來源地',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Table structure for opr_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `opr_warehouse`;
CREATE TABLE `opr_warehouse` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(100) DEFAULT NULL COMMENT '物品編號',
  `classify_id` varchar(11) DEFAULT NULL COMMENT '物品分類的id',
  `name` varchar(200) NOT NULL COMMENT '物品名字',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `price` float(30,2) NOT NULL DEFAULT '0.00' COMMENT '單價',
  `inventory` varchar(10) DEFAULT '0' COMMENT '庫存',
  `city` varchar(30) DEFAULT NULL COMMENT '地區',
  `costing` varchar(20) DEFAULT '0' COMMENT '成本',
  `decimal_num` varchar(10) DEFAULT '否' COMMENT '是否允許小數',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=267 DEFAULT CHARSET=utf8 COMMENT='倉庫表';
