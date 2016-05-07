DROP TABLE IF EXISTS `highscore_1_0`;
CREATE TABLE `highscore_1_0` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_1`;
CREATE TABLE `highscore_1_1` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_2`;
CREATE TABLE `highscore_1_2` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_3`;
CREATE TABLE `highscore_1_3` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `ships`     BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_4`;
CREATE TABLE `highscore_1_4` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_5`;
CREATE TABLE `highscore_1_5` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_6`;
CREATE TABLE `highscore_1_6` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_1_7`;
CREATE TABLE `highscore_1_7` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT                                NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_0`;
CREATE TABLE `highscore_2_0` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_1`;
CREATE TABLE `highscore_2_1` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_2`;
CREATE TABLE `highscore_2_2` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_3`;
CREATE TABLE `highscore_2_3` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_4`;
CREATE TABLE `highscore_2_4` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_5`;
CREATE TABLE `highscore_2_5` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_6`;
CREATE TABLE `highscore_2_6` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT UNSIGNED                       NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `highscore_2_7`;
CREATE TABLE `highscore_2_7` (
    `server_id` VARCHAR(10)                           NOT NULL,
    `id`        INT    UNSIGNED                       NOT NULL,
    `points`    BIGINT                                NOT NULL,
    `seen`      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
