CREATE TABLE `trade_base` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `trade_no` char(20) DEFAULT NULL,
  `gid` char(14) DEFAULT NULL,
  `seller_uid` int(10) DEFAULT NULL,
  `buyer_uid` int(10) DEFAULT NULL,
  `goods_price` int(11) DEFAULT NULL COMMENT '商品价格',
  `pay_price` int(10) DEFAULT NULL COMMENT '订单总价',
  `pay_channel` tinyint(1) DEFAULT '0' COMMENT ' 1,支付宝支付。2，银联支付。5，银联全渠道。6，微信支付。7，微信web支付。8，微信小程序JSAPI支付。9，新版阿里app支付。（版本号设计为向前兼容）',
  `product_bid` int(11) DEFAULT NULL,
  `product_brandname_e` varchar(100) DEFAULT NULL,
  `product_name` varchar(512) DEFAULT NULL,
  `product_cover_image` varchar(256) DEFAULT NULL,
  `trade_status` tinyint(2) DEFAULT '1' COMMENT '订单状态 1:待接单 2:待付款 3:待发货 4:待签收 5:交易成功 6:商家拒绝接单 7:商家发货失败 8:退款成功 9:买家已取消订单 10:订单已关闭',
  `mobilephone` varchar(15) DEFAULT NULL,
  `pay_time` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seller_nickname` varchar(50) DEFAULT NULL,
  `buyer_nickname` varchar(50) DEFAULT NULL,
  `goods_imgs` mediumtext COMMENT '商品图片',
  `is_stock` tinyint(2) NOT NULL DEFAULT '2' COMMENT '是否备货：1，已备货；2，未备货（默认）',
  `first_level_type_id` int(10) DEFAULT '0' COMMENT '商品一级类目id',
  `first_level_type` varchar(20) DEFAULT '' COMMENT '类目名称',
  `buyer_mobilephone` varchar(20) DEFAULT NULL COMMENT '买家手机号，区别于收件人手机号mobilephone',
  `trade_price` int(10) DEFAULT NULL COMMENT '订单总价',
  `cancel_code` int(10) DEFAULT '0' COMMENT '订单取消原因:1,我不想买了;2,信息填写错误，重新下单;3,买手缺货;4,已与买手协商，取消订单;5,超过支付金额;6,其他原因',
  `delivery_failed_time` timestamp NULL DEFAULT NULL COMMENT '发货失败时间',
  `refunded_time` timestamp NULL DEFAULT NULL COMMENT '退款时间',
  `cancelled_time` timestamp NULL DEFAULT NULL COMMENT '订单取消时间',
  `record_status` tinyint(1) DEFAULT '1' COMMENT '订单软删除标志',
  `refund_fee` int(11) DEFAULT '0' COMMENT '订单已退金额总和',
  PRIMARY KEY (`id`),
  UNIQUE KEY `trade_no` (`trade_no`),
  KEY `gid` (`gid`),
  KEY `buyer_uid` (`buyer_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单详情表';



CREATE TABLE `user_account_info_main` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) NOT NULL COMMENT '用户昵称',
  `avatarimage` varchar(200) NOT NULL DEFAULT '' COMMENT '用户头像地址',
  `location` varchar(20) NOT NULL DEFAULT '' COMMENT '用户所在地',
  `gender` varchar(4) NOT NULL DEFAULT '' COMMENT '性别',
  `access_token` varchar(40) DEFAULT NULL COMMENT '身份验证token',
  `createtime` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `username` varchar(64) DEFAULT NULL,
  `password` char(32) DEFAULT NULL,
  `mobile_phone` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `title` tinyint(2) DEFAULT '0' COMMENT '用来标识用户身份：0,普通用户;1,注册买手;2,认证买手;3,达人;4,买手店;5,设计师',
  `show_status` tinyint(1) DEFAULT '1' COMMENT '用户是否有效：1：有效，0：无效',
  `last_login_time` timestamp NULL DEFAULT NULL COMMENT '用户最近一次登录的时间',
  `is_vip` tinyint(1) DEFAULT '0' COMMENT '是否是vip',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mobile_phone` (`mobile_phone`),
  KEY `nickname` (`nickname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户信息中心表';


CREATE TABLE `work_order` (
  `work_order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '工单id',
  `title` varchar(500) DEFAULT NULL COMMENT '标题',
  `first_level_type_id` int(11) DEFAULT NULL COMMENT '一级类型id',
  `first_level_type` varchar(50) DEFAULT NULL COMMENT '一级类型',
  `second_level_type_id` int(11) DEFAULT NULL COMMENT '二级类型id',
  `second_level_type` varchar(50) DEFAULT NULL COMMENT '二级类型',
  `status` tinyint(2) DEFAULT NULL COMMENT '状态（1：未受理； 2：受理中；3：已关闭；4：已解决）',
  `organizer_uid` int(10) DEFAULT NULL COMMENT '发起人uid',
  `organizer_name` varchar(50) DEFAULT NULL COMMENT '发起人',
  `assigns_uid` int(10) DEFAULT NULL COMMENT '受理人uid',
  `assigns_name` varchar(50) DEFAULT NULL COMMENT '受理人',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `trade_no` varchar(45) DEFAULT NULL COMMENT '订单号',
  `buyer_uid` int(10) DEFAULT NULL COMMENT '买家uid',
  `buyer_name` varchar(50) DEFAULT NULL COMMENT '买家昵称',
  `seller_uid` int(10) DEFAULT NULL COMMENT '商家uid',
  `seller_name` varchar(50) DEFAULT NULL COMMENT '商家昵称',
  `description` text COMMENT '描述',
  PRIMARY KEY (`work_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='工单表';


CREATE TABLE `refund_work_order` (
  `work_order_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '退款工单号',
  `trade_no` varchar(20) DEFAULT NULL COMMENT '订单号',
  `pay_price` int(10) DEFAULT NULL COMMENT '订单支付金额',
  `buyer_uid` int(11) DEFAULT NULL COMMENT '买家uid',
  `buyer_nickname` varchar(50) DEFAULT NULL COMMENT '买家昵称',
  `seller_uid` int(11) DEFAULT NULL COMMENT '卖家uid',
  `seller_nickname` varchar(50) DEFAULT NULL COMMENT '商家昵称',
  `product_name` varchar(512) DEFAULT NULL COMMENT '商品名称',
  `refund_reason_type` tinyint(1) DEFAULT NULL COMMENT '退款原因(1:商家原因; 2:买家原因; 3:拼团失败)',
  `refund_fee` int(10) DEFAULT NULL COMMENT '退款金额',
  `description` text COMMENT '描述',
  `status` tinyint(1) DEFAULT NULL COMMENT '1:待审核;  2:客服审核通过; -2:客服审核拒绝; 3:业务审核通过; -3:业务审核拒绝; -4:退款失败; 0:退款成功',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `finish_time` timestamp NULL DEFAULT NULL COMMENT '工单完成时间',
  `operator_uid` int(11) DEFAULT NULL COMMENT '操作人uid(创建退款工单人的uid)',
  `refund_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '退款原因描述',
  `refund_specific_reason` tinyint(2) DEFAULT NULL COMMENT '退款具体原因（0：拼团失败; 1：未按约定时间发货; 2： 虚假发货; 3： 商品缺货; 4： 商品涨价; 5： 成交不卖; 6： 违背承诺; 7： 描述不符; 8： 商家发错货; 9： 商品包装或配件不全; 10： 商品在运输途中丢失;  11： 收到商品少件或破损; 12： 收到假货; 13： 快递一直未收到; 14： 退邮费/退差价; 15： 商品被卡在海关;  16： 质量问题; 17： 商品有瑕疵未发货; 18： 商家退店; 19： 不接受发货时间过长/不想等; 20： 尺码不适; 21： 买家不喜欢;  22： 买家拍错了; 23： 收到的商品和想象的不一致 24：空包裹 25：退税费 26：商品有瑕疵 ）',
  `trade_goods_status` tinyint(2) DEFAULT NULL COMMENT '货物状态 1:未发货 2:已收到货 3:未收到货 4:已发货 5:买家已寄回 6:商家已收货',
  PRIMARY KEY (`work_order_id`),
  KEY `create_time` (`create_time`),
  KEY `trade_no` (`trade_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='退款工单表';



CREATE TABLE `goods_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` char(14) NOT NULL,
  `sales` int(10) DEFAULT '0' COMMENT '商品销量',
  `seller_uid` int(10) DEFAULT NULL,
  `price` int(10) DEFAULT NULL COMMENT 'price=original_price+shipping_rate+customs_duties',
  `product_bid` int(11) DEFAULT NULL,
  `product_brandname_e` varchar(100) DEFAULT NULL,
  `product_name` varchar(512) DEFAULT NULL,
  `product_cover_image` varchar(256) DEFAULT NULL,
  `goods_description` mediumtext,
  `sell_country` varchar(80) DEFAULT NULL,
  `show_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1：上架，0：下架，-1：预发布，-2：删除，-3：审核未通过',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `product_desc` mediumtext,
  `goods_stock` int(4) unsigned DEFAULT '0' COMMENT '库存',
  `seller_nickname` varchar(32) DEFAULT '' COMMENT '商家昵称',
  `goods_imgs` mediumtext COMMENT '商家自定义商品图片.JSON',
  `goods_price` int(11) DEFAULT '0' COMMENT '商品原价',
  `favorite_user_count` int(11) unsigned DEFAULT '0' COMMENT '收藏用户数',
  `refused_reason` varchar(512) DEFAULT NULL COMMENT '审核未通过原因或cms管理员下架商品、删除商品的原因。',
  `audit_uid` int(11) DEFAULT NULL COMMENT '最后一次审核人uid',
  `audit_time` timestamp NULL DEFAULT NULL COMMENT '最后一次审核时间',
  `goods_return_support` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否支持七天无理由退货，1:是，0:否',
  `undercarriage_time` timestamp NULL DEFAULT NULL COMMENT '下架时间',
  `is_ad` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为广告商品',
  PRIMARY KEY (`id`),
  KEY `product_bid` (`product_bid`),
  KEY `audit_uid` (`audit_uid`),
  KEY `update_time` (`update_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品信息表';














