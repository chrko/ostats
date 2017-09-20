DROP TABLE IF EXISTS `server_data`;
CREATE TABLE `server_data` (
  `server_id`                   VARCHAR(10)
                                CHARACTER SET ascii    NOT NULL,
  `name`                        VARCHAR(50)            NULL,
  `number`                      SMALLINT UNSIGNED      NOT NULL,
  `language`                    VARCHAR(5)             NOT NULL,
  `timezone`                    VARCHAR(50)            NOT NULL,
  `timezoneOffset`              SMALLINT(4)            NOT NULL
  COMMENT 'timezone offset in minutes',
  `domain`                      VARCHAR(100)           NOT NULL,
  `version`                     VARCHAR(50)            NOT NULL,
  `speed`                       DECIMAL(8, 4) UNSIGNED NOT NULL,
  `speedFleet`                  DECIMAL(8, 4) UNSIGNED NOT NULL,
  `galaxies`                    SMALLINT UNSIGNED      NOT NULL,
  `systems`                     SMALLINT UNSIGNED      NOT NULL,
  `acs`                         TINYINT(1) UNSIGNED    NOT NULL,
  `rapidFire`                   TINYINT(1) UNSIGNED    NOT NULL,
  `defToTF`                     TINYINT(1) UNSIGNED    NOT NULL,
  `debrisFactor`                DECIMAL(5, 4) UNSIGNED NOT NULL,
  `debrisFactorDef`             DECIMAL(5, 4) UNSIGNED NOT NULL,
  `repairFactor`                DECIMAL(5, 4) UNSIGNED NOT NULL,
  `newbieProtectionLimit`       MEDIUMINT UNSIGNED     NOT NULL,
  `newbieProtectionHigh`        MEDIUMINT UNSIGNED     NOT NULL,
  `topScore`                    BIGINT UNSIGNED        NOT NULL,
  `bonusFields`                 SMALLINT UNSIGNED      NOT NULL,
  `donutGalaxy`                 TINYINT(1)             NOT NULL,
  `donutSystem`                 TINYINT(1)             NOT NULL,
  `wfEnabled`                   TINYINT(1)             NOT NULL,
  `wfMinimumRessLost`           MEDIUMINT UNSIGNED     NOT NULL,
  `wfMinimumLossPercentage`     TINYINT UNSIGNED       NOT NULL,
  `wfBasicPercentageRepairable` TINYINT UNSIGNED       NOT NULL,
  `globalDeuteriumSaveFactor`   DECIMAL(6, 4) UNSIGNED NOT NULL,
  `seen_int`                    BIGINT                 NOT NULL DEFAULT 0,
  PRIMARY KEY (`server_id`, `seen_int`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
