DROP TABLE IF EXISTS `highscore_1_0`;
CREATE TABLE `highscore_1_0` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_1`;
CREATE TABLE `highscore_1_1` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_2`;
CREATE TABLE `highscore_1_2` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_3`;
CREATE TABLE `highscore_1_3` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `ships`     BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_4`;
CREATE TABLE `highscore_1_4` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_5`;
CREATE TABLE `highscore_1_5` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_6`;
CREATE TABLE `highscore_1_6` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_7`;
CREATE TABLE `highscore_1_7` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT                                NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_0`;
CREATE TABLE `highscore_2_0` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_1`;
CREATE TABLE `highscore_2_1` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_2`;
CREATE TABLE `highscore_2_2` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_3`;
CREATE TABLE `highscore_2_3` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_4`;
CREATE TABLE `highscore_2_4` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_5`;
CREATE TABLE `highscore_2_5` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_6`;
CREATE TABLE `highscore_2_6` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_7`;
CREATE TABLE `highscore_2_7` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT                                NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = ARCHIVE
    DEFAULT CHARSET = utf8;

INSERT INTO `highscore_1_0` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_player` WHERE `type` = 0;
INSERT INTO `highscore_1_1` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_player` WHERE `type` = 1;
INSERT INTO `highscore_1_2` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_player` WHERE `type` = 2;
INSERT INTO `highscore_1_3` SELECT `server_id`, `id`, `points`, 0, `seen` FROM `highscore_player` WHERE `type` = 3;
INSERT INTO `highscore_1_4` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_player` WHERE `type` = 4;
INSERT INTO `highscore_1_5` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_player` WHERE `type` = 5;
INSERT INTO `highscore_1_6` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_player` WHERE `type` = 6;
INSERT INTO `highscore_2_0` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_alliance` WHERE `type` = 0;
INSERT INTO `highscore_2_1` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_alliance` WHERE `type` = 1;
INSERT INTO `highscore_2_2` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_alliance` WHERE `type` = 2;
INSERT INTO `highscore_2_3` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_alliance` WHERE `type` = 3;
INSERT INTO `highscore_2_4` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_alliance` WHERE `type` = 4;
INSERT INTO `highscore_2_5` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_alliance` WHERE `type` = 5;
INSERT INTO `highscore_2_6` SELECT `server_id`, `id`, `points`, `seen` FROM `highscore_alliance` WHERE `type` = 6;

DROP TABLE IF EXISTS `highscore_player`;
DROP TABLE IF EXISTS `highscore_alliance`;
