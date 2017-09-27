CREATE VIEW `server_data_public`  AS
  SELECT *
  FROM `server_data`
  WHERE NOT (`server_data`.`server_id` LIKE 'de6__' AND `server_data`.`server_id` LIKE 'de7__');

CREATE VIEW `server_data_tests`  AS
  SELECT *
  FROM `server_data`
  WHERE (`server_data`.`server_id` LIKE 'de6__' AND `server_data`.`server_id` LIKE 'de7__');


CREATE VIEW `server_data_public_last_entry` AS
  SELECT
    `sd`.`server_id`     AS `server_id`,
    max(`sd`.`seen_int`) AS `last`
  FROM `server_data_public` AS `sd`
  GROUP BY `sd`.`server_id`;

CREATE VIEW `server_data_public_current_version` AS
  SELECT
    `sd`.`server_id` AS `server_id`,
    `sd`.`version`   AS `version`
  FROM
    `server_data_public` AS `sd`,
    `server_data_public_last_entry` AS `sd_le`
  WHERE `sd`.`server_id` = `sd_le`.`server_id`
        AND `sd`.`seen_int` = `sd_le`.`last`;

CREATE VIEW `server_data_public_current_versions` AS
  SELECT
    `sdcv`.`version` AS `version`,
    count(0)         AS `count`
  FROM `server_data_public_current_version` `sdcv`
  GROUP BY `sdcv`.`version`;


CREATE VIEW `server_data_tests_last_entry` AS
  SELECT
    `sd`.`server_id`     AS `server_id`,
    max(`sd`.`seen_int`) AS `last`
  FROM `server_data_tests` AS `sd`
  GROUP BY `sd`.`server_id`;

CREATE VIEW `server_data_tests_current_version` AS
  SELECT
    `sd`.`server_id` AS `server_id`,
    `sd`.`version`   AS `version`
  FROM
    `server_data_tests` AS `sd`,
    `server_data_tests_last_entry` AS `sd_le`
  WHERE `sd`.`server_id` = `sd_le`.`server_id`
        AND `sd`.`seen_int` = `sd_le`.`last`;

CREATE VIEW `server_data_tests_current_versions` AS
  SELECT
    `sdcv`.`version` AS `version`,
    count(0)         AS `count`
  FROM `server_data_tests_current_version` `sdcv`
  GROUP BY `sdcv`.`version`;