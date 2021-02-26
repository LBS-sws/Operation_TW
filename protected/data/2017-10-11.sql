/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-10-11 14:26:22
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
  `order_status` varchar(255) DEFAULT 'pending',
  `city` varchar(50) DEFAULT NULL COMMENT '物品申請人所在城市',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COMMENT='訂單內的物品表';

-- ----------------------------
-- Table structure for opr_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `opr_warehouse`;
CREATE TABLE `opr_warehouse` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(20) DEFAULT NULL COMMENT '物品編號',
  `classify_id` varchar(11) DEFAULT NULL COMMENT '物品分類的id',
  `name` varchar(30) NOT NULL COMMENT '物品名字',
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='倉庫表';
