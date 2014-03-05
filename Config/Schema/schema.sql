USE test;
DROP TABLE IF EXISTS `viewed`;
CREATE TABLE `viewed` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `model` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
    `model_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `viewed` tinyint(1) NOT NULL,
    `modified` tinyint(1) NOT NULL,	
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8, COLLATE=utf8_spanish_ci, ENGINE=InnoDB;

