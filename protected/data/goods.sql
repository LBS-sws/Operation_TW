/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-07-18 15:37:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_goods_do
-- ----------------------------
DROP TABLE IF EXISTS `opr_goods_do`;
CREATE TABLE `opr_goods_do` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(10) DEFAULT NULL COMMENT '物品編號',
  `classify_id` int(10) DEFAULT NULL COMMENT '小分類的id',
  `name` varchar(30) NOT NULL COMMENT '物品名字',
  `type` varchar(30) NOT NULL COMMENT '物品規格',
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
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='物品表(只含有進口物品）';

-- ----------------------------
-- Records of opr_goods_do
-- ----------------------------
INSERT INTO `opr_goods_do` VALUES ('1', 'FF1002', '2', 'V字形次王企鵝', '5包/箱', '包', '11.20', '0', '1', '55', '1', '香港', '2', 'test', null, '2017-07-18 09:14:47', null);
INSERT INTO `opr_goods_do` VALUES ('2', 'FF10021', '2', '國內貨', '12瓶/箱', '瓶', '55.00', '2', '1', '99999', '1', '江西', '', 'test', 'test', '2017-07-18 14:51:22', '2017-07-18 14:22:51');
INSERT INTO `opr_goods_do` VALUES ('3', 'FF10022', '2', '國內貨2', '12瓶/箱子', '瓶', '55', '2', '1', '99999', '1', '江西', '2', 'test', null, '2017-07-18 14:51:51', null);
INSERT INTO `opr_goods_do` VALUES ('4', 'FF10011', '2', '洗手液 - 黑色', '5包/箱', '包', '55', '1', '1', '99999', '1', '江西', '', 'test', null, '2017-07-18 15:38:19', null);
INSERT INTO `opr_goods_do` VALUES ('5', 'FF10012', '2', '洗手液 - 白色', '5包/箱', '包', '11.2', '1', '1', '99999', '1', '江西', '2', 'test', null, '2017-07-18 15:11:20', null);
INSERT INTO `opr_goods_do` VALUES ('6', 'FF10013', '2', '洗手液 - 无色', '5包/箱', '包', '55.00', '1', '1', '99999', '1', '江西', '2', 'test', null, '2017-07-18 15:44:20', null);

-- ----------------------------
-- Table structure for opr_goods_fa
-- ----------------------------
DROP TABLE IF EXISTS `opr_goods_fa`;
CREATE TABLE `opr_goods_fa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(10) DEFAULT NULL COMMENT '物品編號',
  `classify_id` int(10) DEFAULT NULL COMMENT '小分類的id',
  `name` varchar(30) NOT NULL COMMENT '物品名字',
  `type` varchar(30) NOT NULL COMMENT '物品規格',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `price` varchar(30) DEFAULT NULL COMMENT '物品單價',
  `rules_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '混合規則id',
  `multiple` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '數量倍率',
  `big_num` int(10) unsigned DEFAULT '0' COMMENT '總部數量限制',
  `small_num` int(10) DEFAULT '0' COMMENT '區域數量限制',
  `origin` varchar(30) DEFAULT NULL COMMENT '來源地',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Records of opr_goods_fa
-- ----------------------------
INSERT INTO `opr_goods_fa` VALUES ('1', 'KL1001', '2', '口罩', '100個/箱', '個', '3.50', '0', '1', '50', '5', '美國', 'test', null, '2017-07-17 18:08:04', null);

-- ----------------------------
-- Table structure for opr_goods_im
-- ----------------------------
DROP TABLE IF EXISTS `opr_goods_im`;
CREATE TABLE `opr_goods_im` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(10) DEFAULT NULL COMMENT '物品編號',
  `classify_id` int(10) DEFAULT NULL COMMENT '物品分類的id',
  `name` varchar(30) NOT NULL COMMENT '物品名字',
  `type` varchar(30) NOT NULL COMMENT '物品規格',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `price` varchar(30) DEFAULT NULL COMMENT '物品單價',
  `net_weight` varchar(30) DEFAULT NULL COMMENT '净重',
  `gross_weight` varchar(30) DEFAULT NULL COMMENT '毛重',
  `len` int(11) DEFAULT NULL COMMENT '長',
  `width` int(11) DEFAULT NULL COMMENT '寬',
  `height` int(11) DEFAULT NULL COMMENT '高',
  `rules_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '混合規則id',
  `multiple` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '數量倍率',
  `big_num` int(10) unsigned DEFAULT '0' COMMENT '總部數量限制',
  `small_num` int(10) DEFAULT '0' COMMENT '區域數量限制',
  `origin` varchar(30) DEFAULT NULL COMMENT '來源地',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Records of opr_goods_im
-- ----------------------------
INSERT INTO `opr_goods_im` VALUES ('1', 'ER100010', '2', '进口货8880', '50包/箱', '包', '100.50', '60.4', '50.2', '12', '321', '123', '0', '50', '99999', '1', '美国', 'test', null, '2017-07-18 14:33:44', null);
INSERT INTO `opr_goods_im` VALUES ('2', 'ER10001', '2', '进口货888', '5包/箱', '包', '122.5', '21.4', '50.2', '543', '43', '213', '2', '1', '99999', '1', '美国', 'test', null, '2017-07-18 14:11:45', null);

-- ----------------------------
-- Table structure for opr_goods_rules
-- ----------------------------
DROP TABLE IF EXISTS `opr_goods_rules`;
CREATE TABLE `opr_goods_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '規則的名字',
  `multiple` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '倍數',
  `max` int(10) unsigned NOT NULL DEFAULT '99999' COMMENT '最大數量',
  `min` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '最小數量',
  `lcu` varchar(20) DEFAULT NULL,
  `luu` varchar(20) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='物品的混合限制表';

-- ----------------------------
-- Records of opr_goods_rules
-- ----------------------------
INSERT INTO `opr_goods_rules` VALUES ('1', '洗手液', '16', '60', '10', 'test', null, '2017-07-18 11:17:50', null);
INSERT INTO `opr_goods_rules` VALUES ('2', '香精', '9', '40', '20', 'test', null, '2017-07-18 11:51:50', null);
