-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Host: rm-bp1e53w7pbsj7y216.mysql.rds.aliyuncs.com
-- Generation Time: 2017-10-16 14:49:51
-- 服务器版本： 5.6.16-log
-- PHP Version: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dreamsinks`
--

-- --------------------------------------------------------

--
-- 表的结构 `people_auth_assignment`
--

CREATE TABLE `people_auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `people_auth_item`
--

CREATE TABLE `people_auth_item` (
  `name` varchar(64) NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `people_auth_item_child`
--

CREATE TABLE `people_auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `people_auth_rule`
--

CREATE TABLE `people_auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `people_menu`
--

CREATE TABLE `people_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `route` varchar(256) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `data` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `people_money_exchange_bill`
--

CREATE TABLE `people_money_exchange_bill` (
  `id` int(30) NOT NULL COMMENT 'id',
  `user_id` varchar(30) NOT NULL COMMENT '用户ID',
  `order_id` varchar(50) NOT NULL COMMENT '订单号',
  `exchange_money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '兑换金额',
  `exchange_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '兑换类型1推荐人奖励',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '类型1微信营销108招2宝典',
  `remark` text COMMENT '备注',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT AS `创建时间`
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户奖励资金兑换明细表';

--
-- 触发器 `people_money_exchange_bill`
--
DELIMITER $$
CREATE TRIGGER `tongji_exchange_insert` AFTER INSERT ON `people_money_exchange_bill` FOR EACH ROW BEGIN 
delete from people_reward_tongji where user_id=new.user_id;
set @r = (select IFNULL(SUM(reward_money),0) from people_refree_settle_bill where yx_from=new.user_id);
set @e = (select IFNULL(SUM(exchange_money),0) from people_money_exchange_bill where user_id=new.user_id);
INSERT INTO people_reward_tongji(user_id,sum_reward_money,sum_exchange_money) VALUES(new.user_id,@r,@e); 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `people_op_logs`
--

CREATE TABLE `people_op_logs` (
  `id` int(11) NOT NULL,
  `classname` varchar(30) NOT NULL COMMENT '栏目名',
  `operating` varchar(30) NOT NULL COMMENT '操作名称',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT AS `操作时间`,
  `oper_name` varchar(30) DEFAULT NULL COMMENT AS `操作用户`,
  `sqlstr` text COMMENT '执行操作的SQL语句',
  `user_id` varchar(30) DEFAULT ''COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `people_op_roles`
--

CREATE TABLE `people_op_roles` (
  `role` int(10) NOT NULL COMMENT '角色id',
  `role_name` varchar(50) NOT NULL COMMENT '角色名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色配置表';

-- --------------------------------------------------------

--
-- 表的结构 `people_op_user`
--

CREATE TABLE `people_op_user` (
  `id` int(10) NOT NULL COMMENT '用户id',
  `user_name` varchar(20) NOT NULL DEFAULT ''COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `people_problem`
--

CREATE TABLE `people_problem` (
  `id` int(11) NOT NULL,
  `problem` varchar(255) DEFAULT NULL COMMENT AS `问题`,
  `opinion` varchar(255) DEFAULT NULL COMMENT AS `意见`,
  `weixin` varchar(255) DEFAULT NULL COMMENT AS `微信号`,
  `user_id` varchar(255) DEFAULT NULL COMMENT AS `用户id`,
  `tel` varchar(255) DEFAULT NULL COMMENT AS `电话`,
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '反馈时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `people_qa`
--

CREATE TABLE `people_qa` (
  `id` int(11) NOT NULL COMMENT '主键ID',
  `question` varchar(50) NOT NULL COMMENT '问题',
  `answer` text NOT NULL COMMENT '回答',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT AS `创建时间`
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='百问百答表';

-- --------------------------------------------------------

--
-- 表的结构 `people_user`
--

CREATE TABLE `people_user` (
  `id` int(11) NOT NULL COMMENT '自增ID',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `auth_key` varchar(32) NOT NULL COMMENT '自动登录key',
  `password_hash` varchar(255) NOT NULL COMMENT '加密密码',
  `password_reset_token` varchar(255) DEFAULT NULL COMMENT AS `重置密码token`,
  `email` varchar(255) NOT NULL COMMENT '邮箱',
  `role` smallint(6) NOT NULL DEFAULT '10' COMMENT '角色等级',
  `status` smallint(6) NOT NULL DEFAULT '10' COMMENT '状态',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `updated_at` int(11) NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

-- --------------------------------------------------------

--
-- 表的结构 `people_user_address`
--

CREATE TABLE `people_user_address` (
  `id` int(30) NOT NULL COMMENT 'id',
  `user_id` varchar(30) NOT NULL COMMENT '用户ID',
  `name` varchar(100) NOT NULL DEFAULT ''COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `people_user_wxcode_info`
--

CREATE TABLE `people_user_wxcode_info` (
  `id` int(20) NOT NULL COMMENT '主键自增',
  `user_id` varchar(30) NOT NULL DEFAULT '0'COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `refree_settle_bill`
--

CREATE TABLE `refree_settle_bill` (
  `id` int(20) NOT NULL COMMENT '主键，自增',
  `user_id` varchar(30) NOT NULL DEFAULT '0'COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `refree_tongji`
--

CREATE TABLE `refree_tongji` (
  `id` int(20) NOT NULL COMMENT '主键自增',
  `user_id` varchar(30) NOT NULL DEFAULT '0'COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `scan_qrcode_log`
--

CREATE TABLE `scan_qrcode_log` (
  `id` int(10) NOT NULL COMMENT '主键id',
  `openid` varchar(50) NOT NULL DEFAULT ''COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `tbl_city`
--

CREATE TABLE `tbl_city` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tixian_bills`
--

CREATE TABLE `tixian_bills` (
  `id` int(20) NOT NULL COMMENT '自增id',
  `user_id` varchar(30) NOT NULL DEFAULT '0'COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `user_base_info`
--

CREATE TABLE `user_base_info` (
  `id` int(20) NOT NULL COMMENT '主键自增',
  `user_id` varchar(30) NOT NULL DEFAULT '0'COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `user_order`
--

CREATE TABLE `user_order` (
  `id` int(30) NOT NULL COMMENT 'id',
  `user_id` varchar(30) NOT NULL COMMENT '用户ID',
  `out_trade_no` varchar(100) NOT NULL DEFAULT ''COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `user_wxcode_info`
--

CREATE TABLE `user_wxcode_info` (
  `id` int(20) NOT NULL COMMENT '主键自增',
  `user_id` varchar(30) NOT NULL DEFAULT '0'COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `weixin_pay`
--

CREATE TABLE `weixin_pay` (
  `id` int(20) NOT NULL COMMENT 'id',
  `appid` varchar(32) NOT NULL COMMENT '公众号ID',
  `mch_id` varchar(32) NOT NULL COMMENT '商户号ID',
  `device_info` varchar(32) NOT NULL COMMENT '终端设备号',
  `nonce_str` varchar(32) NOT NULL COMMENT '随机字符串',
  `sign` varchar(32) NOT NULL COMMENT '签名',
  `result_code` varchar(16) NOT NULL COMMENT '业务结果SUCCESS/FAIL',
  `openid` varchar(128) NOT NULL COMMENT 'openid',
  `is_subscribe` varchar(10) NOT NULL DEFAULT 'N' COMMENT '是否关注了公众号，Y-关注，N-未关注',
  `trade_type` enum('JSAPI','NATIVE','MICROPAY','APP') NOT NULL DEFAULT 'JSAPI' COMMENT '交易类型',
  `bank_type` varchar(16) NOT NULL COMMENT '银行类型',
  `total_fee` int(16) NOT NULL COMMENT '支付金额，单位为分',
  `coupon_fee` int(16) NOT NULL COMMENT '现金券支付金额，单位为分',
  `fee_type` varchar(8) NOT NULL DEFAULT 'CNY' COMMENT '货币类型，默认人民币CNY',
  `transaction_id` varchar(32) NOT NULL COMMENT '微信支付订单号',
  `out_trade_no` varchar(100) NOT NULL COMMENT '商户系统的订单号，与请求一致',
  `attach` varchar(128) DEFAULT NULL COMMENT AS `商户数据包，原样返回，空参数不传递`,
  `time_end` varchar(14) NOT NULL COMMENT '支付完成时间，格式为yyyyMMddhhmmss,时区为GMT+8',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '时间戳',
  `pay_type` tinyint(4) NOT NULL DEFAULT '0'COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `weixin_pay_bak`
--

CREATE TABLE `weixin_pay_bak` (
  `id` int(20) NOT NULL COMMENT 'id',
  `appid` varchar(32) NOT NULL COMMENT '公众号ID',
  `mch_id` varchar(32) NOT NULL COMMENT '商户号ID',
  `device_info` varchar(32) NOT NULL COMMENT '终端设备号',
  `nonce_str` varchar(32) NOT NULL COMMENT '随机字符串',
  `sign` varchar(32) NOT NULL COMMENT '签名',
  `result_code` varchar(16) NOT NULL COMMENT '业务结果SUCCESS/FAIL',
  `openid` varchar(128) NOT NULL COMMENT 'openid',
  `is_subscribe` varchar(10) NOT NULL DEFAULT 'N' COMMENT '是否关注了公众号，Y-关注，N-未关注',
  `trade_type` enum('JSAPI','NATIVE','MICROPAY','APP') NOT NULL DEFAULT 'JSAPI' COMMENT '交易类型',
  `bank_type` varchar(16) NOT NULL COMMENT '银行类型',
  `total_fee` int(16) NOT NULL COMMENT '支付金额，单位为分',
  `coupon_fee` int(16) NOT NULL COMMENT '现金券支付金额，单位为分',
  `fee_type` varchar(8) NOT NULL DEFAULT 'CNY' COMMENT '货币类型，默认人民币CNY',
  `transaction_id` varchar(32) NOT NULL COMMENT '微信支付订单号',
  `out_trade_no` varchar(100) NOT NULL COMMENT '商户系统的订单号，与请求一致',
  `attach` varchar(128) DEFAULT NULL COMMENT AS `商户数据包，原样返回，空参数不传递`,
  `time_end` varchar(14) NOT NULL COMMENT '支付完成时间，格式为yyyyMMddhhmmss,时区为GMT+8',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '时间戳',
  `pay_type` tinyint(4) NOT NULL DEFAULT '0'COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `weixin_post_log`
--

CREATE TABLE `weixin_post_log` (
  `id` int(20) NOT NULL COMMENT 'id',
  `fromuser` varchar(30) NOT NULL COMMENT 'from用户',
  `msgtype` varchar(10) NOT NULL COMMENT '类型',
  `event` varchar(15) DEFAULT NULL COMMENT AS `事件`,
  `eventkey` text COMMENT '事件key',
  `content` varchar(255) DEFAULT NULL COMMENT AS `内容`,
  `picUrl` varchar(100) DEFAULT NULL COMMENT AS `图片URL`,
  `format` varchar(20) DEFAULT NULL COMMENT AS `格式`,
  `mediaId` varchar(60) DEFAULT NULL COMMENT AS `媒体ID，接口可以用`,
  `location_x` varchar(10) DEFAULT NULL COMMENT AS `地理纬度`,
  `location_y` varchar(10) DEFAULT NULL COMMENT AS `地理经度`,
  `scale` varchar(10) DEFAULT NULL COMMENT AS `缩放大小`,
  `label` varchar(50) DEFAULT NULL COMMENT AS `地理位置信息`,
  `create_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信上行信息表';

-- --------------------------------------------------------

--
-- 表的结构 `weixin_template_msg`
--

CREATE TABLE `weixin_template_msg` (
  `msg_id` int(11) NOT NULL COMMENT '主键',
  `openid` char(128) NOT NULL DEFAULT ''COMMENT
) ;

-- --------------------------------------------------------

--
-- 表的结构 `xlt_data`
--

CREATE TABLE `xlt_data` (
  `id` int(11) NOT NULL COMMENT '主键ID',
  `user_id` varchar(30) CHARACTER SET utf8mb4 NOT NULL COMMENT '用户ID',
  `name` varchar(20) NOT NULL COMMENT '姓名',
  `phone` varchar(30) NOT NULL COMMENT '手机号码',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `people_auth_assignment`
--
ALTER TABLE `people_auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`);

--
-- Indexes for table `people_auth_item`
--
ALTER TABLE `people_auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `people_auth_item_child`
--
ALTER TABLE `people_auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Indexes for table `people_auth_rule`
--
ALTER TABLE `people_auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `people_menu`
--
ALTER TABLE `people_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent` (`parent`);

--
-- Indexes for table `people_money_exchange_bill`
--
ALTER TABLE `people_money_exchange_bill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `people_op_roles`
--
ALTER TABLE `people_op_roles`
  ADD PRIMARY KEY (`role`);

--
-- Indexes for table `people_problem`
--
ALTER TABLE `people_problem`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `people_qa`
--
ALTER TABLE `people_qa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `people_user`
--
ALTER TABLE `people_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_city`
--
ALTER TABLE `tbl_city`
  ADD PRIMARY KEY (`id`),
  ADD KEY `province_id` (`pid`);

--
-- Indexes for table `weixin_post_log`
--
ALTER TABLE `weixin_post_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fromuser` (`fromuser`,`msgtype`,`event`);

--
-- Indexes for table `xlt_data`
--
ALTER TABLE `xlt_data`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `people_menu`
--
ALTER TABLE `people_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `people_money_exchange_bill`
--
ALTER TABLE `people_money_exchange_bill`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT 'id';
--
-- 使用表AUTO_INCREMENT `people_op_logs`
--
ALTER TABLE `people_op_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `people_op_roles`
--
ALTER TABLE `people_op_roles`
  MODIFY `role` int(10) NOT NULL AUTO_INCREMENT COMMENT '角色id', AUTO_INCREMENT=9;
--
-- 使用表AUTO_INCREMENT `people_op_user`
--
ALTER TABLE `people_op_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户id';
--
-- 使用表AUTO_INCREMENT `people_problem`
--
ALTER TABLE `people_problem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `people_qa`
--
ALTER TABLE `people_qa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=29;
--
-- 使用表AUTO_INCREMENT `people_user`
--
ALTER TABLE `people_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID', AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `people_user_address`
--
ALTER TABLE `people_user_address`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT 'id';
--
-- 使用表AUTO_INCREMENT `people_user_wxcode_info`
--
ALTER TABLE `people_user_wxcode_info`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '主键自增';
--
-- 使用表AUTO_INCREMENT `refree_settle_bill`
--
ALTER TABLE `refree_settle_bill`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '主键，自增';
--
-- 使用表AUTO_INCREMENT `refree_tongji`
--
ALTER TABLE `refree_tongji`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '主键自增';
--
-- 使用表AUTO_INCREMENT `scan_qrcode_log`
--
ALTER TABLE `scan_qrcode_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id';
--
-- 使用表AUTO_INCREMENT `tbl_city`
--
ALTER TABLE `tbl_city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=420;
--
-- 使用表AUTO_INCREMENT `tixian_bills`
--
ALTER TABLE `tixian_bills`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 使用表AUTO_INCREMENT `user_base_info`
--
ALTER TABLE `user_base_info`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '主键自增';
--
-- 使用表AUTO_INCREMENT `user_order`
--
ALTER TABLE `user_order`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT COMMENT 'id';
--
-- 使用表AUTO_INCREMENT `user_wxcode_info`
--
ALTER TABLE `user_wxcode_info`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '主键自增';
--
-- 使用表AUTO_INCREMENT `weixin_pay`
--
ALTER TABLE `weixin_pay`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id';
--
-- 使用表AUTO_INCREMENT `weixin_pay_bak`
--
ALTER TABLE `weixin_pay_bak`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id';
--
-- 使用表AUTO_INCREMENT `weixin_post_log`
--
ALTER TABLE `weixin_post_log`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id', AUTO_INCREMENT=4284;
--
-- 使用表AUTO_INCREMENT `weixin_template_msg`
--
ALTER TABLE `weixin_template_msg`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键';
--
-- 使用表AUTO_INCREMENT `xlt_data`
--
ALTER TABLE `xlt_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=26;
--
-- 限制导出的表
--

--
-- 限制表 `people_auth_assignment`
--
ALTER TABLE `people_auth_assignment`
  ADD CONSTRAINT `people_auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `people_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `people_auth_item`
--
ALTER TABLE `people_auth_item`
  ADD CONSTRAINT `people_auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `people_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- 限制表 `people_auth_item_child`
--
ALTER TABLE `people_auth_item_child`
  ADD CONSTRAINT `people_auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `people_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `people_auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `people_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `people_menu`
--
ALTER TABLE `people_menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `people_menu` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
