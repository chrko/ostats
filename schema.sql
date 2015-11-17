SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `alliance`;
CREATE TABLE `alliance` (
  `server_id`   VARCHAR(10)      NOT NULL,
  `id`          INT(10) UNSIGNED NOT NULL,
  `name`        VARCHAR(30)      NOT NULL,
  `tag`         VARCHAR(8)       NOT NULL,
  `homepage`    VARCHAR(2000)             DEFAULT NULL,
  `logo`        VARCHAR(2000)             DEFAULT NULL,
  `open`        TINYINT(1)       NOT NULL,
  `last_update` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_id`, `id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `alliance_overall`;
CREATE TABLE `alliance_overall` (
  `server_id`  VARCHAR(10)      NOT NULL,
  `id`         INT(10) UNSIGNED NOT NULL,
  `name`       VARCHAR(30)      NOT NULL,
  `tag`        VARCHAR(8)       NOT NULL,
  `homepage`   VARCHAR(2000)             DEFAULT NULL,
  `logo`       VARCHAR(2000)             DEFAULT NULL,
  `first_seen` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen`  TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`server_id`, `id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `alliance_log_name`;
CREATE TABLE `alliance_log_name` (
  `server_id`   VARCHAR(10)      NOT NULL,
  `alliance_id` INT(10) UNSIGNED NOT NULL,
  `seen`        TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `old_name`    VARCHAR(30)      NOT NULL,
  `new_name`    VARCHAR(30)      NOT NULL,
  PRIMARY KEY (`server_id`, `alliance_id`, `seen`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `alliance_log_tag`;
CREATE TABLE `alliance_log_tag` (
  `server_id`   VARCHAR(10)      NOT NULL,
  `alliance_id` INT(10) UNSIGNED NOT NULL,
  `seen`        TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `old_tag`     VARCHAR(8)       NOT NULL,
  `new_tag`     VARCHAR(8)       NOT NULL,
  PRIMARY KEY (`server_id`, `alliance_id`, `seen`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `alliance_log_homepage`;
CREATE TABLE `alliance_log_homepage` (
  `server_id`    VARCHAR(10)      NOT NULL,
  `alliance_id`  INT(10) UNSIGNED NOT NULL,
  `seen`         TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `old_homepage` VARCHAR(2000)             DEFAULT NULL,
  `new_homepage` VARCHAR(2000)             DEFAULT NULL,
  PRIMARY KEY (`server_id`, `alliance_id`, `seen`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `alliance_log_logo`;
CREATE TABLE `alliance_log_logo` (
  `server_id`   VARCHAR(10)      NOT NULL,
  `alliance_id` INT(10) UNSIGNED NOT NULL,
  `seen`        TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `old_logo`    VARCHAR(2000)             DEFAULT NULL,
  `new_logo`    VARCHAR(2000)             DEFAULT NULL,
  PRIMARY KEY (`server_id`, `alliance_id`, `seen`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `player`;
CREATE TABLE `player` (
  `server_id`     VARCHAR(10)      NOT NULL,
  `id`            INT(10) UNSIGNED NOT NULL,
  `name`          VARCHAR(50)      NOT NULL,
  `vacation`      TINYINT(1)       NOT NULL DEFAULT '0',
  `inactive`      TINYINT(1)       NOT NULL DEFAULT '0',
  `inactive_long` TINYINT(1)       NOT NULL DEFAULT '0',
  `banned`        TINYINT(1)       NOT NULL DEFAULT '0',
  `outlaw`        TINYINT(1)       NOT NULL DEFAULT '0',
  `admin`         TINYINT(1)       NOT NULL DEFAULT '0',
  `last_update`   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_id`, `id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `player_overall`;
CREATE TABLE `player_overall` (
  `server_id`     VARCHAR(10)      NOT NULL,
  `id`            INT(10) UNSIGNED NOT NULL,
  `name`          VARCHAR(50)      NOT NULL,
  `vacation`      TINYINT(1)       NOT NULL DEFAULT '0',
  `inactive`      TINYINT(1)       NOT NULL DEFAULT '0',
  `inactive_long` TINYINT(1)       NOT NULL DEFAULT '0',
  `banned`        TINYINT(1)       NOT NULL DEFAULT '0',
  `outlaw`        TINYINT(1)       NOT NULL DEFAULT '0',
  `admin`         TINYINT(1)       NOT NULL DEFAULT '0',
  `first_seen`    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen`     TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`server_id`, `id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `player_log_name`;
CREATE TABLE `player_log_name` (
  `server_id` VARCHAR(10)      NOT NULL,
  `id`        INT(10) UNSIGNED NOT NULL,
  `seen`      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `old_name`  VARCHAR(50)      NOT NULL,
  `new_name`  VARCHAR(50)      NOT NULL,
  PRIMARY KEY (`server_id`, `id`, `seen`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `alliance_member`;
CREATE TABLE `alliance_member` (
  `server_id`   VARCHAR(10)      NOT NULL,
  `alliance_id` INT(10) UNSIGNED NOT NULL,
  `player_id`   INT(10) UNSIGNED NOT NULL,
  `first_seen`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_id`, `alliance_id`, `player_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `alliance_member_log`;
CREATE TABLE `alliance_member_log` (
  `server_id`   VARCHAR(10)      NOT NULL,
  `alliance_id` INT(10) UNSIGNED NOT NULL,
  `player_id`   INT(10) UNSIGNED NOT NULL,
  `first_seen`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen`   TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`server_id`, `alliance_id`, `player_id`, `first_seen`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TRIGGER IF EXISTS `alliance_member_insert`;
CREATE TRIGGER `alliance_member_insert` AFTER INSERT ON `alliance_member`
FOR EACH ROW INSERT INTO `alliance_member_log` VALUES (
  NEW.`server_id`,
  NEW.`alliance_id`,
  NEW.`player_id`,
  NEW.`first_seen`,
  NULL
)
ON DUPLICATE KEY UPDATE `last_seen` = NULL;

DROP TRIGGER IF EXISTS `alliance_member_delete`;
CREATE TRIGGER `alliance_member_delete` AFTER DELETE ON `alliance_member`
FOR EACH ROW INSERT INTO `alliance_member_log` VALUES (
  OLD.`server_id`,
  OLD.`alliance_id`,
  OLD.`player_id`,
  OLD.`first_seen`,
  OLD.`last_update`
)
ON DUPLICATE KEY UPDATE
  `last_seen` = OLD.`last_update`;

DROP TABLE IF EXISTS `planet`;
CREATE TABLE `planet` (
  `server_id`   VARCHAR(10)          NOT NULL,
  `id`          INT(10) UNSIGNED     NOT NULL,
  `name`        VARCHAR(40)          NOT NULL,
  `galaxy`      TINYINT(2) UNSIGNED  NOT NULL,
  `system`      SMALLINT(3) UNSIGNED NOT NULL,
  `position`    TINYINT(2) UNSIGNED  NOT NULL,
  `player_id`   INT(10) UNSIGNED     NOT NULL,
  `first_seen`  TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_id`, `id`),
  KEY `system` (`server_id`, `galaxy`, `system`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS `moon`;
CREATE TABLE `moon` (
  `server_id`   VARCHAR(10)      NOT NULL,
  `id`          INT(10) UNSIGNED NOT NULL,
  `planet_id`   INT(10) UNSIGNED NOT NULL,
  `size`        INT(5) UNSIGNED  NOT NULL,
  `name`        VARCHAR(50)      NOT NULL,
  `first_seen`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_id`, `id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_alliance`;
CREATE TABLE `highscore_alliance` (
  `server_id`   VARCHAR(10)                           NOT NULL,
  `type`        TINYINT                               NOT NULL,
  `alliance_id` INT       UNSIGNED                    NOT NULL,
  `points`      BIGINT    UNSIGNED                    NOT NULL,
  `seen`        TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_id`, `type`, `alliance_id`, `points`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_player`;
CREATE TABLE `highscore_player` (
  `server_id` VARCHAR(10)                           NOT NULL,
  `type`      TINYINT                               NOT NULL,
  `player_id` INT       UNSIGNED                    NOT NULL,
  `points`    BIGINT    UNSIGNED                    NOT NULL,
  `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_id`, `type`, `player_id`, `points`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TRIGGER IF EXISTS `alliance_insert`;
CREATE TRIGGER `alliance_insert` AFTER INSERT ON `alliance`
FOR EACH ROW INSERT INTO `alliance_overall` VALUES (
  NEW.`server_id`,
  NEW.`id`,
  NEW.`name`,
  NEW.`tag`,
  NEW.`homepage`,
  NEW.`logo`,
  NEW.`last_update`,
  NULL
)
ON DUPLICATE KEY UPDATE
  `name`      = NEW.`name`,
  `tag`       = NEW.`tag`,
  `logo`      = NEW.`logo`,
  `last_seen` = NULL;

DROP TRIGGER IF EXISTS `alliance_delete`;
CREATE TRIGGER `alliance_delete` AFTER DELETE ON `alliance`
FOR EACH ROW UPDATE `alliance_overall`
SET `last_seen` = OLD.`last_update`
WHERE `id` = OLD.`id` AND `server_id` = OLD.`server_id`;

DROP TRIGGER IF EXISTS `alliance_update`;
DELIMITER $$
CREATE TRIGGER `alliance_update` AFTER UPDATE ON `alliance`
FOR EACH ROW BEGIN
  UPDATE `alliance_overall`
  SET
    `name` = NEW.`name`,
    `tag`  = NEW.`tag`,
    `logo` = NEW.`logo`
  WHERE `id` = NEW.`id` AND `server_id` = NEW.`server_id`;

  IF OLD.`name` <> NEW.`name`
  THEN
    INSERT INTO `alliance_log_name` VALUES (
      NEW.`server_id`,
      NEW.`id`,
      NEW.`last_update`,
      OLD.`name`,
      NEW.`name`
    )
    ON DUPLICATE KEY UPDATE
      `old_name` = OLD.`name`,
      `new_name` = NEW.`name`;
  END IF;

  IF OLD.`tag` <> NEW.`tag`
  THEN
    INSERT INTO `alliance_log_tag` VALUES (
      NEW.`server_id`,
      NEW.`id`,
      NEW.`last_update`,
      OLD.`tag`,
      NEW.`tag`
    )
    ON DUPLICATE KEY UPDATE
      `old_tag` = OLD.`tag`,
      `new_tag` = NEW.`tag`;
  END IF;

  IF OLD.`logo` <> NEW.`logo`
     OR (OLD.`logo` IS NULL AND NEW.`logo` IS NOT NULL)
     OR (OLD.`logo` IS NOT NULL AND NEW.`logo` IS NULL)
  THEN
    INSERT INTO `alliance_log_logo` VALUES (
      NEW.`server_id`,
      NEW.`id`,
      NEW.`last_update`,
      OLD.`logo`,
      NEW.`logo`
    )
    ON DUPLICATE KEY UPDATE
      `old_logo` = OLD.`logo`,
      `new_logo` = NEW.`logo`;
  END IF;

  IF OLD.`homepage` <> NEW.`homepage`
     OR (OLD.`homepage` IS NULL AND NEW.`homepage` IS NOT NULL)
     OR (OLD.`homepage` IS NOT NULL AND NEW.`homepage` IS NULL)
  THEN
    INSERT INTO `alliance_log_homepage` VALUES (
      NEW.`server_id`,
      NEW.`id`,
      NEW.`last_update`,
      OLD.`homepage`,
      NEW.`homepage`
    )
    ON DUPLICATE KEY UPDATE
      `old_homepage` = OLD.`homepage`,
      `new_homepage` = NEW.`homepage`;
  END IF;
END $$
DELIMITER ;


DROP TRIGGER IF EXISTS `player_insert`;
CREATE TRIGGER `player_insert` AFTER INSERT ON `player`
FOR EACH ROW INSERT INTO `player_overall`
VALUES (
  NEW.`server_id`,
  NEW.`id`,
  NEW.`name`,
  NEW.`vacation`,
  NEW.`inactive`,
  NEW.`inactive_long`,
  NEW.`banned`,
  NEW.`outlaw`,
  NEW.`admin`,
  NEW.`last_update`,
  NULL
)
ON DUPLICATE KEY UPDATE
  `name`          = NEW.`name`,
  `vacation`      = NEW.`vacation`,
  `inactive`      = NEW.`inactive`,
  `inactive_long` = NEW.`inactive_long`,
  `banned`        = NEW.`banned`,
  `outlaw`        = NEW.`outlaw`,
  `admin`         = NEW.`admin`,
  `last_seen`     = NULL;

DROP TRIGGER IF EXISTS `player_delete`;
CREATE TRIGGER `player_delete` AFTER DELETE ON `player`
FOR EACH ROW UPDATE `player_overall`
SET `last_seen` = OLD.`last_update`
WHERE `id` = OLD.`id` AND `server_id` = OLD.`server_id`;


DROP TRIGGER IF EXISTS `player_update`;
DELIMITER $$
CREATE TRIGGER `player_update` AFTER UPDATE ON `player`
FOR EACH ROW BEGIN
  UPDATE `player_overall`
  SET
    `name`          = NEW.`name`,
    `vacation`      = NEW.`vacation`,
    `inactive`      = NEW.`inactive`,
    `inactive_long` = NEW.`inactive_long`,
    `banned`        = NEW.`banned`,
    `outlaw`        = NEW.`outlaw`,
    `admin`         = NEW.`admin`
  WHERE `id` = NEW.`id` AND `server_id` = NEW.`server_id`;

  IF OLD.`name` <> NEW.`name`
  THEN
    INSERT IGNORE INTO `player_log_name` VALUES (
      NEW.`server_id`,
      NEW.`id`,
      NEW.`last_update`,
      OLD.`name`,
      NEW.`name`
    );
  END IF;
END $$
DELIMITER ;