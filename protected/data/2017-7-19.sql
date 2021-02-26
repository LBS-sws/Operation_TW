/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-07-19 15:40:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_classify
-- ----------------------------
DROP TABLE IF EXISTS `opr_classify`;
CREATE TABLE `opr_classify` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '小分類的名字',
  `level` varchar(10) DEFAULT NULL COMMENT '級別',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='物品小分類表（暫時只給總部管理員使用）';

-- ----------------------------
-- Records of opr_classify
-- ----------------------------
INSERT INTO `opr_classify` VALUES ('7', '清潔及消毒劑  ', '', 'test', null, '2017-07-18 16:00:18', null);

-- ----------------------------
-- Table structure for opr_email
-- ----------------------------
DROP TABLE IF EXISTS `opr_email`;
CREATE TABLE `opr_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '中央郵件的名字',
  `email` varchar(30) NOT NULL COMMENT '中央郵箱',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='中央郵箱';

-- ----------------------------
-- Records of opr_email
-- ----------------------------
INSERT INTO `opr_email` VALUES ('1', 'Mona', 'mona@lbsgroup.com.cn', 'test', 'test', '2017-07-19 10:58:50', '2017-07-19 10:50:58');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='物品表(只含有進口物品）';

-- ----------------------------
-- Records of opr_goods_do
-- ----------------------------

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Records of opr_goods_fa
-- ----------------------------

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
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Records of opr_goods_im
-- ----------------------------
INSERT INTO `opr_goods_im` VALUES ('3', 'VETO', '7', '常用(濃縮)', '5加侖', '桶', '44.27', '21', '22', '30.5', '30.2', '39.3', '0', '1', '99999', '1', '台灣', 'test', 'test', '2017-07-18 17:22:34', '2017-07-18 17:34:22');
INSERT INTO `opr_goods_im` VALUES ('4', 'HZ', '7', '手部消毒劑', '50包/箱', '包', '2.60', '10.5', '11.5', '41.5', '23', '32', '0', '1', '99999', '1', '台灣', 'test', 'test', '2017-07-18 17:18:53', '2017-07-18 17:53:18');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='物品的混合限制表';

-- ----------------------------
-- Records of opr_goods_rules
-- ----------------------------

-- ----------------------------
-- Table structure for opr_order
-- ----------------------------
DROP TABLE IF EXISTS `opr_order`;
CREATE TABLE `opr_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '訂單id',
  `order_code` varchar(10) DEFAULT NULL COMMENT '訂單號（自動生成）',
  `order_user` varchar(30) NOT NULL COMMENT '訂購的用戶',
  `order_class` varchar(30) DEFAULT NULL COMMENT '訂單類型（Import：進口貨、Domestic：國產貨、Fast：快速貨）',
  `activity_id` varchar(10) DEFAULT NULL COMMENT '訂單所屬的活動的id',
  `technician` varchar(10) DEFAULT NULL COMMENT '技術員',
  `status` varchar(30) NOT NULL COMMENT '訂單狀態（pending / sent / approve / reject / cancelled）',
  `city` varchar(30) DEFAULT NULL COMMENT '訂單歸屬城市',
  `remark` varchar(255) DEFAULT NULL COMMENT '備註',
  `judge` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '是否是總部訂單。1：是，0：否',
  `lcu_email` varchar(40) DEFAULT NULL COMMENT '地區郵箱',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='訂單表';

-- ----------------------------
-- Records of opr_order
-- ----------------------------

-- ----------------------------
-- Table structure for opr_order_activity
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_activity`;
CREATE TABLE `opr_order_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主鍵',
  `activity_code` varchar(50) DEFAULT NULL COMMENT '活動的編號',
  `activity_title` varchar(50) NOT NULL COMMENT '活動的標題',
  `start_time` date NOT NULL COMMENT '收集開始時間',
  `end_time` date NOT NULL COMMENT '收集結束時間',
  `order_class` varchar(30) DEFAULT NULL COMMENT '訂單類型:Import/Domestic/Fast',
  `num` int(10) unsigned DEFAULT '1' COMMENT '訂單的數量限制',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='訂單權限表';

-- ----------------------------
-- Records of opr_order_activity
-- ----------------------------

-- ----------------------------
-- Table structure for opr_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_goods`;
CREATE TABLE `opr_order_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL COMMENT '物品id',
  `order_id` int(10) unsigned NOT NULL COMMENT '訂單id',
  `goods_num` int(10) NOT NULL COMMENT '物品數量',
  `confirm_num` int(10) DEFAULT NULL COMMENT '實際數量',
  `note` varchar(255) DEFAULT NULL COMMENT '要求備註',
  `remark` varchar(255) DEFAULT NULL COMMENT '總部備註',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='訂單內的物品表';

-- ----------------------------
-- Records of opr_order_goods
-- ----------------------------

-- ----------------------------
-- Table structure for opr_order_status
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_status`;
CREATE TABLE `opr_order_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '訂單狀態id',
  `order_id` int(10) unsigned NOT NULL,
  `status` varchar(30) NOT NULL COMMENT '訂單狀態（pending / sent / approve / reject / cancelled）',
  `r_remark` varchar(255) DEFAULT NULL COMMENT '備註',
  `time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '訂單狀態時間',
  `lcu` varchar(255) NOT NULL COMMENT '操作人員',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='訂單的狀態表';

-- ----------------------------
-- Records of opr_order_status
-- ----------------------------

-- ----------------------------
-- Table structure for opr_stickies
-- ----------------------------
DROP TABLE IF EXISTS `opr_stickies`;
CREATE TABLE `opr_stickies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '標籤的名稱',
  `content` varchar(255) NOT NULL COMMENT '標籤內容',
  `index` int(10) unsigned DEFAULT NULL COMMENT '級別',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='標籤表（暫時只給國內貨的物品使用）';

-- ----------------------------
-- Records of opr_stickies
-- ----------------------------

-- ----------------------------
-- Table structure for opr_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `opr_warehouse`;
CREATE TABLE `opr_warehouse` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(10) DEFAULT NULL COMMENT '物品編號',
  `classify_id` varchar(11) DEFAULT NULL COMMENT '物品分類的id',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='倉庫表';

-- ----------------------------
-- Records of opr_warehouse
-- ----------------------------
