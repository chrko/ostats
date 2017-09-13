DROP TABLE IF EXISTS `player_log_banned`;
CREATE TABLE `player_log_banned` (
    `server_id`       VARCHAR(10)
                      CHARACTER SET ascii NOT NULL,
    `id`              INT(10) UNSIGNED    NOT NULL,
    `prev_update_int` BIGINT              NOT NULL DEFAULT 0,
    `prev_update`     TIMESTAMP AS (FROM_UNIXTIME(`prev_update_int`)) VIRTUAL,
    `seen_int`        BIGINT              NOT NULL DEFAULT 0,
    `seen`            TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL,
    `banned`          TINYINT(1)          NOT NULL,
    PRIMARY KEY (`server_id`, `id`, `seen_int`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
