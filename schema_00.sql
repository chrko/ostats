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

DROP TABLE IF EXISTS `player_log_vacation`;
CREATE TABLE `player_log_vacation` (
  `server_id` VARCHAR(10)      NOT NULL,
  `id`        INT(10) UNSIGNED NOT NULL,
  `seen`      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vacation`  TINYINT(1)       NOT NULL,
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

DROP TABLE IF EXISTS `planet_overall`;
CREATE TABLE `planet_overall` (
  `server_id`  VARCHAR(10)          NOT NULL,
  `id`         INT(10) UNSIGNED     NOT NULL,
  `name`       VARCHAR(40)          NOT NULL,
  `galaxy`     TINYINT(2) UNSIGNED  NOT NULL,
  `system`     SMALLINT(3) UNSIGNED NOT NULL,
  `position`   TINYINT(2) UNSIGNED  NOT NULL,
  `player_id`  INT(10) UNSIGNED     NOT NULL,
  `first_seen` TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen`  TIMESTAMP            NULL     DEFAULT NULL,
  PRIMARY KEY (`server_id`, `id`),
  KEY `system` (`server_id`, `galaxy`, `system`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS `planet_relocation`;
CREATE TABLE `planet_relocation` (
  `server_id`    VARCHAR(10)          NOT NULL,
  `id`           INT(10) UNSIGNED     NOT NULL,
  `seen`         TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `old_galaxy`   TINYINT(2) UNSIGNED  NOT NULL,
  `old_system`   SMALLINT(3) UNSIGNED NOT NULL,
  `old_position` TINYINT(2) UNSIGNED  NOT NULL,
  `new_galaxy`   TINYINT(2) UNSIGNED  NOT NULL,
  `new_system`   SMALLINT(3) UNSIGNED NOT NULL,
  `new_position` TINYINT(2) UNSIGNED  NOT NULL,
  PRIMARY KEY (`server_id`, `id`, `seen`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `moon_overall`;
CREATE TABLE `moon_overall` (
  `server_id`  VARCHAR(10)      NOT NULL,
  `id`         INT(10) UNSIGNED NOT NULL,
  `planet_id`  INT(10) UNSIGNED NOT NULL,
  `size`       INT(5) UNSIGNED  NOT NULL,
  `name`       VARCHAR(50)      NOT NULL,
  `first_seen` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen`  TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`server_id`, `id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_alliance`;
CREATE TABLE `highscore_alliance` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT       UNSIGNED                    NOT NULL,
    `type`      TINYINT                               NOT NULL,
    `points`    BIGINT    UNSIGNED                    NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_player`;
CREATE TABLE `highscore_player` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT       UNSIGNED                    NOT NULL,
    `type`      TINYINT                               NOT NULL,
    `points`    BIGINT    UNSIGNED                    NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id`        INT(10) UNSIGNED    NOT NULL     AUTO_INCREMENT,
  `due_time`  TIMESTAMP           NOT NULL     DEFAULT CURRENT_TIMESTAMP,
  `server_id` VARCHAR(10)         NOT NULL,
  `endpoint`  VARCHAR(20)         NOT NULL,
  `category`  TINYINT(1) UNSIGNED NOT NULL     DEFAULT 0,
  `type`      TINYINT(1) UNSIGNED NOT NULL     DEFAULT 0,
  `job`       BLOB                NOT NULL,
  `running`   TINYINT(1) UNSIGNED              DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_type` (`server_id`, `endpoint`, `category`, `type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
