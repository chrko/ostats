DROP TABLE IF EXISTS `tasks`;
-- statement divider --
CREATE TABLE `tasks` (
  `id`           BIGINT UNSIGNED     NOT NULL     AUTO_INCREMENT,
  `due_time_int` BIGINT UNSIGNED     NOT NULL     DEFAULT 0,
  `job_type`     VARCHAR(25)
                 CHARACTER SET ascii NOT NULL,
  `slug`         VARCHAR(50)
                 CHARACTER SET ascii NOT NULL,
  `job`          BLOB                NOT NULL,
  `running`      TINYINT(1) UNSIGNED              DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_type` (`job_type`, `slug`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
