-- ----------------------------
-- Table structure for hd_order_trade
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_trade`;
CREATE TABLE `hd_order_trade` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单支付ID',
  `order_sn` char(20) NOT NULL DEFAULT '' COMMENT '主订单号',
  `trade_no` char(20) NOT NULL DEFAULT '' COMMENT '支付单号',
  `total_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态[-1:取消,0:未支付,1:完成支付]',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `method` varchar(200) NOT NULL DEFAULT '' COMMENT '支付方式',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;