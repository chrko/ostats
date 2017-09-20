CREATE VIEW `server_data_last_entry` AS
  SELECT
    `server_data`.`server_id`     AS `server_id`,
    max(`server_data`.`seen_int`) AS `last`
  FROM `server_data`
  GROUP BY `server_data`.`server_id`;

CREATE VIEW `server_data_current_version` AS
  SELECT
    `sd`.`server_id` AS `server_id`,
    `sd`.`version`   AS `version`
  FROM
    `server_data` AS `sd`,
    `server_data_last_entry` AS `sd_le`
  WHERE `sd`.`server_id` = `sd_le`.`server_id`
        AND `sd`.`seen_int` = `sd_le`.`last`;

CREATE VIEW `server_data_current_versions` AS
  SELECT
    `sdcv`.`version` AS `version`,
    count(0)         AS `count`
  FROM `server_data_current_version` `sdcv`
  GROUP BY `sdcv`.`version`;
