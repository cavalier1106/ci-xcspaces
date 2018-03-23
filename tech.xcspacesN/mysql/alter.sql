ALTER table xcspace_topic_clicks_details_323000 ENGINE = InnoDB;
ALTER TABLE `xcspace_users` ADD COLUMN `qqBind` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'QQ绑定:0未绑定,1绑定' AFTER wbBindTime;
ALTER TABLE `xcspace_users` ADD COLUMN `qqId` varchar(20) NOT NULL COMMENT 'qqId' AFTER qqBind;
ALTER TABLE `xcspace_users` ADD COLUMN `qqBindTime` int(11) DEFAULT NULL COMMENT 'QQ绑定时间' AFTER qqId;

ALTER TABLE xcspace_users DROP COLUMN `qqId`;
ALTER TABLE xcspace_users ADD COLUMN `qqOpenid` varchar(64) NOT NULL COMMENT 'QQ Openid' AFTER qqBind;