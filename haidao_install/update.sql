-- ----------------------------
-- Table structure for hd_order_trade
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_trade`;
CREATE TABLE `hd_order_trade` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '����֧��ID',
  `order_sn` char(20) NOT NULL DEFAULT '' COMMENT '��������',
  `trade_no` char(20) NOT NULL DEFAULT '' COMMENT '֧������',
  `total_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '֧�����',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '֧��״̬[-1:ȡ��,0:δ֧��,1:���֧��]',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '֧��ʱ��',
  `method` varchar(200) NOT NULL DEFAULT '' COMMENT '֧����ʽ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;