DROP TABLE IF EXISTS `player_log_inactive`;
CREATE TABLE `player_log_inactive` (
  `server_id`       VARCHAR(10)
                    CHARACTER SET ascii NOT NULL,
  `id`              INT(10) UNSIGNED    NOT NULL,
  `seen_int`        BIGINT              NOT NULL DEFAULT 0,
  `inactive`        TINYINT(1)          NOT NULL COMMENT '1 inactive / 2 long inactive',
  PRIMARY KEY (`server_id`, `id`, `seen_int`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
