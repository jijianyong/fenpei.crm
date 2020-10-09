-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2020-03-02 13:59:08
-- 服务器版本： 5.5.62-log
-- PHP 版本： 7.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `interface.tianqukj.com`
--

-- --------------------------------------------------------

--
-- 表的结构 `tq_admin`
--

CREATE TABLE `tq_admin` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号码',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(255) NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) NOT NULL DEFAULT '80' COMMENT '头像',
  `email` varchar(100) DEFAULT '' COMMENT '电子邮箱',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `loginfailure` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录时间',
  `createtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `token` varchar(255) NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `tq_admin`
--

INSERT INTO `tq_admin` (`id`, `name`, `mobile`, `nickname`, `password`, `salt`, `avatar`, `email`, `remark`, `loginfailure`, `logintime`, `createtime`, `updatetime`, `token`, `status`) VALUES
(1, 'admin', '', '超级管理员', '11d1a32b4daae20f20268efc2e4c2638', 'FsXxSe', '80', '', '', 0, 1583128666, 1575857727, 1583128666, '6e528f0b5ed34e28919b08b6a853d1d5', 0),
(2, 'lijie', '', '李杰', '0ee2183844d3c03ae3f913579154fc63', 'L56YEv', '80', '', '', 0, 1577324760, 1575857727, 1577409790, 'a294795605e140d68c8ed6201357f5d4', 0),
(3, 'xiemingwei', '', '谢明伟', '233b2b352e94f48825424ef3f6143e83', 'oZ5LGy', '80', '', '', 0, 1578032471, 1577409828, 1578032471, '0d2ed4693ec0455088c1295cb2057a46', 0);

-- --------------------------------------------------------

--
-- 表的结构 `tq_admin_log`
--

CREATE TABLE `tq_admin_log` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text NOT NULL COMMENT '提交内容',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '操作时间',
  `updatetime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员日志表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `tq_admin_log`
--

INSERT INTO `tq_admin_log` (`id`, `admin_id`, `url`, `title`, `content`, `ip`, `useragent`, `createtime`, `updatetime`) VALUES
(1, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"5b281a6389ec45a68c7bee05ad000061\",\"captcha_token\":\"a3742c4931e648989021847cb7ef90e9\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577331134, 0),
(2, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"c4d8b242b46044c4bb96e369910ef75d\",\"captcha_token\":\"d646c8d762294b1cb0f589551bef79f9\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332357, 0),
(3, 1, '/sales/order/del?orderid=1', '删除订单', '{\"orderid\":\"1\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332385, 0),
(4, 1, '/index/logout', 'index/logout', '[]', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332414, 0),
(5, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"28116f6b05b54a4d9c00553c21a2619e\",\"captcha_token\":\"184ca7316b884f0aacc03f1ba10095b9\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332439, 0),
(6, 1, '/setting/admin/del?id=1', 'setting/admin/del', '{\"id\":\"1\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332449, 0),
(7, 1, '/setting/admin/edit?id=1&name=admin&password=2020tianqu&repassword=2020tianqu&nickname=%E5%91%A8%E6%9D%B0%E4%BC%A6', '编辑管理员', '{\"id\":\"1\",\"name\":\"admin\",\"nickname\":\"\\u5468\\u6770\\u4f26\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332495, 0),
(8, 1, '/index/logout', 'index/logout', '[]', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332498, 0),
(9, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"4654dad2b2ec41cc956cb91e455d3d38\",\"captcha_token\":\"7be67f36bbf24c35ac625ac12ab5e3d5\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332508, 0),
(10, 1, '/setting/admin/edit?id=1&name=admin&password=&repassword=&nickname=%E8%B6%85%E7%BA%A7%E7%AE%A1%E7%90%86%E5%91%98', '编辑管理员', '{\"id\":\"1\",\"name\":\"admin\",\"nickname\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332528, 0),
(11, 1, '/index/logout', 'index/logout', '[]', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332547, 0),
(12, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"62f736bea12746b3b8fea468130c103f\",\"captcha_token\":\"dc1757849bd04cb189ea5769ea8cf454\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577332727, 0),
(13, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"7c2dfb7c720e4ab8b0c7707cebe10263\",\"captcha_token\":\"bf5d25164a1e45b5ba957bcb4ad7ebf4\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36', 1577333054, 0),
(14, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"a2c558831cec4c6e8012e7032e4d468d\",\"captcha_token\":\"369d94b799534205ae5985eaf64bdc66\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577409772, 0),
(15, 1, '/setting/admin/edit?id=2&name=lijie&password=&repassword=&nickname=%E6%9D%8E%E6%9D%B0', '编辑管理员', '{\"id\":\"2\",\"name\":\"lijie\",\"nickname\":\"\\u674e\\u6770\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577409790, 0),
(16, 1, '/setting/admin/add?id=&name=xiemingwei&password=xmw6688.&repassword=xmw6688.&nickname=%E8%B0%A2%E6%98%8E%E4%BC%9F', '添加管理员', '{\"id\":\"\",\"name\":\"xiemingwei\",\"nickname\":\"\\u8c22\\u660e\\u4f1f\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577409828, 0),
(17, 0, '/index/login', 'index/login', '{\"username\":\"admin\",\"captcha\":\"68e1b07c0ab443c08256f184b1e7059d\",\"captcha_token\":\"f93d17f53877446e9adbcc33c6d2e8a8\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36', 1577429158, 0),
(18, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"47739d7444b0456b894324b07fa7fcf5\",\"captcha_token\":\"1cc54551b6c1407aa957c39175d2b690\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36', 1577429169, 0),
(19, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"dd3e7f3f54a84c9a8e5c48aa3617def8\",\"captcha_token\":\"bd9e3eabb7084088a62860c77f3f61a2\"}', '119.145.83.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577441303, 0),
(20, 0, '/index/login', 'index/login', '{\"username\":\"xiemingwei\",\"captcha\":\"8f3448c1169542b0ac9cbd3ec66a9060\",\"captcha_token\":\"4db48c4d7cae496d8c3f949211d8d1fb\"}', '119.145.82.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577763770, 0),
(21, 0, '/index/login', 'index/login', '{\"username\":\"xiemingwei\",\"captcha\":\"c1e7cd9683b147879db3242cccc3d588\",\"captcha_token\":\"2dcff430ff4c4c7b9ba797af5fd7e168\"}', '119.145.82.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577763780, 0),
(22, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"187368464a0041e287a8969d8e9659e7\",\"captcha_token\":\"bbb81658e5804ba2b4aba88f0f168127\"}', '119.145.82.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577763792, 0),
(23, 1, '/setting/admin/edit?id=3&name=xiemingwei&password=Xmw6688..&repassword=Xmw6688..&nickname=%E8%B0%A2%E6%98%8E%E4%BC%9F', '编辑管理员', '{\"id\":\"3\",\"name\":\"xiemingwei\",\"nickname\":\"\\u8c22\\u660e\\u4f1f\"}', '119.145.82.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577763816, 0),
(24, 1, '/index/logout', 'index/logout', '[]', '119.145.82.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577763820, 0),
(25, 0, '/index/login', 'Login', '{\"username\":\"xiemingwei\",\"captcha\":\"2b5ff736fba84fb1afc60a39f4153119\",\"captcha_token\":\"0b0aa1cb8bae4083a0132493abbb85d8\"}', '119.145.82.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1577763826, 0),
(26, 0, '/index/login', 'Login', '{\"username\":\"xiemingwei\",\"captcha\":\"8fe5597175f044c892e7fa15e24a9bf6\",\"captcha_token\":\"b82a3a0a94e84750b784fe905ccb2c9d\"}', '119.130.230.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1578032471, 0),
(27, 0, '/index/login', 'index/login', '{\"username\":\"admin\",\"captcha\":\"91177552c8ac4ec488d5dc3636efa036\",\"captcha_token\":\"db32668589d54fa88709458e30a250f1\"}', '119.130.231.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1578907146, 0),
(28, 0, '/index/login', 'index/login', '{\"username\":\"admin\",\"captcha\":\"36635fdf7cf042a9bf9be9d60c13b0f6\",\"captcha_token\":\"f233db9902d64a7ab3180be6ff5e0304\"}', '119.130.231.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1578907159, 0),
(29, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"a747ea5b43f14676a6fab1946fd14589\",\"captcha_token\":\"af81ac244ab547538614fa12ec92ba72\"}', '119.130.231.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', 1578907181, 0),
(30, 0, '/index/login', 'Login', '{\"username\":\"admin\",\"captcha\":\"e2592031b1f94af094469204ecedead7\",\"captcha_token\":\"363f1dee8ef7492fb5a945bba8435c20\"}', '119.130.231.187', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36', 1583128666, 0);

-- --------------------------------------------------------

--
-- 表的结构 `tq_admin_login_log`
--

CREATE TABLE `tq_admin_login_log` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `uid` int(11) UNSIGNED DEFAULT '0' COMMENT '管理员ID',
  `ip` varchar(255) DEFAULT '' COMMENT 'IP',
  `browser` varchar(255) DEFAULT '' COMMENT '浏览器',
  `useragent` varchar(1000) DEFAULT '',
  `date` varchar(255) DEFAULT '' COMMENT '日期',
  `time` int(11) UNSIGNED DEFAULT '0' COMMENT '登陆时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `tq_admin_login_log`
--

INSERT INTO `tq_admin_login_log` (`id`, `uid`, `ip`, `browser`, `useragent`, `date`, `time`) VALUES
(1, 1, '119.145.83.205', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2019-12-26', 1577331134),
(2, 1, '119.145.83.205', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2019-12-26', 1577332357),
(3, 1, '119.145.83.205', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2019-12-26', 1577332439),
(4, 1, '119.145.83.205', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2019-12-26', 1577332508),
(5, 1, '119.145.83.205', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2019-12-26', 1577332727),
(6, 1, '119.145.83.205', 'Chrome', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36', '2019-12-26', 1577333054),
(7, 1, '119.145.83.205', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2019-12-27', 1577409772),
(8, 1, '119.145.83.205', 'Chrome', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36', '2019-12-27', 1577429169),
(9, 1, '119.145.83.205', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2019-12-27', 1577441303),
(10, 1, '119.145.82.123', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2019-12-31', 1577763792),
(11, 3, '119.145.82.123', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2019-12-31', 1577763826),
(12, 3, '119.130.230.149', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2020-01-03', 1578032471),
(13, 1, '119.130.231.250', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36', '2020-01-13', 1578907181),
(14, 1, '119.130.231.187', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36', '2020-03-02', 1583128666);

-- --------------------------------------------------------

--
-- 表的结构 `tq_auth_group`
--

CREATE TABLE `tq_auth_group` (
  `id` int(10) UNSIGNED NOT NULL,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(10) DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分组表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `tq_auth_group`
--

INSERT INTO `tq_auth_group` (`id`, `pid`, `name`, `rules`, `createtime`, `updatetime`, `status`) VALUES
(1, 0, '超级管理员', '*', 1490883540, 149088354, 0);

-- --------------------------------------------------------

--
-- 表的结构 `tq_auth_group_access`
--

CREATE TABLE `tq_auth_group_access` (
  `uid` int(11) UNSIGNED NOT NULL COMMENT '会员ID',
  `group_id` int(11) UNSIGNED NOT NULL COMMENT '级别ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限分组表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `tq_auth_group_access`
--

INSERT INTO `tq_auth_group_access` (`uid`, `group_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `tq_auth_rule`
--

CREATE TABLE `tq_auth_rule` (
  `id` int(11) UNSIGNED NOT NULL,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `createtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `weigh` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节点表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `tq_sales_order`
--

CREATE TABLE `tq_sales_order` (
  `id` int(11) UNSIGNED NOT NULL,
  `order_no` varchar(255) DEFAULT '' COMMENT '订单号',
  `express_name` varchar(255) DEFAULT '' COMMENT '收货人名称',
  `express_moblie` varchar(255) DEFAULT '' COMMENT '收货人电话',
  `express_address` text COMMENT '收货人地址',
  `express_company` varchar(255) DEFAULT '' COMMENT '物流公司',
  `product_spec` varchar(255) DEFAULT '' COMMENT '产品规格',
  `product_quantity` int(11) DEFAULT '0' COMMENT '产品数量',
  `remark` text COMMENT '备注',
  `updatetime` int(11) DEFAULT '0' COMMENT '修改时间',
  `createtime` int(11) DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转储表的索引
--

--
-- 表的索引 `tq_admin`
--
ALTER TABLE `tq_admin`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `username` (`name`) USING BTREE;

--
-- 表的索引 `tq_admin_log`
--
ALTER TABLE `tq_admin_log`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `tq_admin_login_log`
--
ALTER TABLE `tq_admin_login_log`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `tq_auth_group`
--
ALTER TABLE `tq_auth_group`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `tq_auth_group_access`
--
ALTER TABLE `tq_auth_group_access`
  ADD UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `group_id` (`group_id`) USING BTREE;

--
-- 表的索引 `tq_auth_rule`
--
ALTER TABLE `tq_auth_rule`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `name` (`name`) USING BTREE,
  ADD KEY `pid` (`pid`) USING BTREE,
  ADD KEY `weigh` (`weigh`) USING BTREE;

--
-- 表的索引 `tq_sales_order`
--
ALTER TABLE `tq_sales_order`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `tq_admin`
--
ALTER TABLE `tq_admin`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `tq_admin_log`
--
ALTER TABLE `tq_admin_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=31;

--
-- 使用表AUTO_INCREMENT `tq_admin_login_log`
--
ALTER TABLE `tq_admin_login_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `tq_auth_group`
--
ALTER TABLE `tq_auth_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `tq_auth_rule`
--
ALTER TABLE `tq_auth_rule`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `tq_sales_order`
--
ALTER TABLE `tq_sales_order`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
