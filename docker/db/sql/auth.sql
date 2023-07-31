/* UserとDatabaseの作成 */
CREATE DATABASE in_auth;
CREATE USER 'in_auth'@'%' identified by '***REMOVED***';
GRANT ALL ON in_auth.* TO 'in_auth'@'%';

/* 切り替え */
USE in_auth;

/* テーブルの作成 */

CREATE TABLE `account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) DEFAULT NULL,
  `pwd` varchar(255) DEFAULT NULL,
  `batlogin` tinyint DEFAULT '0',
  `iass` tinyint DEFAULT '0',
  `ircv` tinyint DEFAULT '0',
  `admin` tinyint DEFAULT '0',
  `cu_admin` tinyint DEFAULT '0',
  `matchResult` tinyint DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE `newuser` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nkey` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `terminal` (
  `id` int DEFAULT NULL,
  `terminalkey` varchar(16) NOT NULL,
  `terminaltime` int DEFAULT NULL,
  `sessionkey` varchar(64) DEFAULT NULL,
  `sessiontime` int DEFAULT NULL,
  `hua` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`terminalkey`)
);

/* 初期ユーザーの作成 */
INSERT INTO `account` VALUES (1,'***REMOVED***','***REMOVED***',0,1,0,1,1,1);
