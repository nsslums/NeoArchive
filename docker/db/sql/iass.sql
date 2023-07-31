/* UserとDatabaseの作成 */
CREATE DATABASE in_iass;
CREATE USER 'in_iass'@'%' identified by '***REMOVED***';
GRANT ALL ON in_iass.* TO 'in_iass'@'%';

/* 切り替え */
USE in_iass;

/* テーブル作成 */

CREATE TABLE `anime` (
  `aid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(192) DEFAULT NULL,
  `time_edcb` int DEFAULT NULL,
  `time_basic` int DEFAULT NULL,
  `icon_id` int DEFAULT NULL,
  PRIMARY KEY (`aid`)
);

CREATE TABLE `news` (
  `nid` int NOT NULL AUTO_INCREMENT,
  `title` varchar(192) DEFAULT NULL,
  `content` varchar(3072) DEFAULT NULL,
  `time` int DEFAULT NULL,
  PRIMARY KEY (`nid`)
);

CREATE TABLE `play` (
  `pid` int NOT NULL AUTO_INCREMENT,
  `uid` int DEFAULT NULL,
  `aid` int DEFAULT NULL,
  `id` int DEFAULT NULL,
  `quality` varchar(16) DEFAULT NULL,
  `time` int DEFAULT NULL,
  `playtime` int DEFAULT NULL,
  PRIMARY KEY (`pid`)
);

CREATE TABLE `request` (
  `rid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(192) DEFAULT NULL,
  `status` int DEFAULT NULL,
  `time` int DEFAULT NULL,
  PRIMARY KEY (`rid`)
);
