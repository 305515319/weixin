-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `myapp_user`;
CREATE TABLE `myapp_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `user_name` varchar(45) NOT NULL COMMENT '登录名，手机号或邮箱',
  `user_pass` char(32) NOT NULL COMMENT '登录密码',
  `noncestr` varchar(45) NOT NULL COMMENT '随机字符串',
  `group_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '所属组，1普通会员，2白银会员，3黄金会员，4钻石会员，5终身VIP',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0正常，1已锁定',
  `create_time` varchar(45) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='注册用户列表';

INSERT INTO `myapp_user` (`user_id`, `user_name`, `user_pass`, `noncestr`, `group_id`, `status`, `create_time`) VALUES
(1,	'qweqwe',	'4dbbcaeacb8eb71168b46dea870d3cd3',	'XzLNbV',	1,	0,	'2018-08-02 16:30:25');

DROP TABLE IF EXISTS `myapp_userinfo`;
CREATE TABLE `myapp_userinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `nickname` varchar(45) DEFAULT NULL COMMENT '昵称',
  `truename` varchar(45) DEFAULT NULL COMMENT '真实姓名',
  `qq` varchar(45) DEFAULT NULL COMMENT 'qq',
  `weixin` varchar(45) DEFAULT NULL COMMENT '微信',
  `phone` varchar(45) DEFAULT NULL COMMENT '联系电话',
  `email` varchar(45) DEFAULT NULL COMMENT '邮箱地址',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `myapp_userinfo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `myapp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户基本资料';


DROP TABLE IF EXISTS `myapp_weixin_gzh`;
CREATE TABLE `myapp_weixin_gzh` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `name` varchar(45) DEFAULT NULL COMMENT '公众号名称',
  `type` tinyint(1) unsigned DEFAULT NULL COMMENT '公众号类型，1订阅号，2订阅号（已认证），3服务号，4服务号（已认证）',
  `appid` varchar(50) NOT NULL COMMENT '公众号appid',
  `appsecret` varchar(50) NOT NULL COMMENT '公众号appsecret',
  `headpic` varchar(255) DEFAULT NULL COMMENT '公众号头像',
  `url` varchar(50) NOT NULL COMMENT '服务器URL',
  `token` varchar(45) NOT NULL COMMENT '令牌(Token)',
  `key` varchar(45) NOT NULL COMMENT '消息加解密密钥(EncodingAESKey)',
  `hello` tinyint(1) unsigned NOT NULL COMMENT '消息加密方式，1明文，2兼容，3安全',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `appid` (`appid`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `myapp_weixin_gzh_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `myapp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信公众号绑定列表';

INSERT INTO `myapp_weixin_gzh` (`id`, `user_id`, `name`, `type`, `appid`, `appsecret`, `headpic`, `url`, `token`, `key`, `hello`, `create_time`) VALUES
(1,	1,	'互联八零',	1,	'1111',	'1111',	'/data/images/1/jcRUnTpqtGXVPKGORWgravREgkixND.png',	'http://app.com',	'FlVQiJKAUcYpclfhHnctGBpKkrSCOxOg',	'119577f6be7e8f52a89a2507e33746ff',	1,	'2018-08-06 11:18:07');

-- 2018-08-06 04:21:26
