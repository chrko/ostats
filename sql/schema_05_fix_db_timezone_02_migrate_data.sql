# Search: ^ALTER TABLE `(.*)` ADD `(.*)` BIGINT NOT NULL DEFAULT 0 AFTER `(.*)`;$
# Replace: UPDATE `$1` SET `$2` = UNIX_TIMESTAMP(CONVERT_TZ(`$3`, 'UTC', 'Europe/Berlin')) WHERE `$2` = 0;

UPDATE `alliance` SET `last_update_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_update`, 'UTC', 'Europe/Berlin')) WHERE `last_update_int` = 0;

UPDATE `alliance_log_homepage` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `alliance_log_logo` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `alliance_log_name` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `alliance_log_tag` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;

UPDATE `alliance_overall` SET `first_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`first_seen`, 'UTC', 'Europe/Berlin')) WHERE `first_seen_int` = 0;
UPDATE `alliance_overall` SET `last_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_seen`, 'UTC', 'Europe/Berlin')) WHERE `last_seen_int` = 0;


UPDATE `alliance_member` SET `first_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`first_seen`, 'UTC', 'Europe/Berlin')) WHERE `first_seen_int` = 0;
UPDATE `alliance_member` SET `last_update_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_update`, 'UTC', 'Europe/Berlin')) WHERE `last_update_int` = 0;

UPDATE `alliance_member_log` SET `first_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`first_seen`, 'UTC', 'Europe/Berlin')) WHERE `first_seen_int` = 0;
UPDATE `alliance_member_log` SET `last_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_seen`, 'UTC', 'Europe/Berlin')) WHERE `last_seen_int` = 0;


UPDATE `moon` SET `first_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`first_seen`, 'UTC', 'Europe/Berlin')) WHERE `first_seen_int` = 0;
UPDATE `moon` SET `last_update_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_update`, 'UTC', 'Europe/Berlin')) WHERE `last_update_int` = 0;

UPDATE `moon_overall` SET `first_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`first_seen`, 'UTC', 'Europe/Berlin')) WHERE `first_seen_int` = 0;
UPDATE `moon_overall` SET `last_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_seen`, 'UTC', 'Europe/Berlin')) WHERE `last_seen_int` = 0;


UPDATE `planet` SET `first_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`first_seen`, 'UTC', 'Europe/Berlin')) WHERE `first_seen_int` = 0;
UPDATE `planet` SET `last_update_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_update`, 'UTC', 'Europe/Berlin')) WHERE `last_update_int` = 0;

UPDATE `planet_overall` SET `first_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`first_seen`, 'UTC', 'Europe/Berlin')) WHERE `first_seen_int` = 0;
UPDATE `planet_overall` SET `last_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_seen`, 'UTC', 'Europe/Berlin')) WHERE `last_seen_int` = 0;

UPDATE `planet_relocation` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;


UPDATE `player` SET `last_update_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_update`, 'UTC', 'Europe/Berlin')) WHERE `last_update_int` = 0;

UPDATE `player_overall` SET `first_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`first_seen`, 'UTC', 'Europe/Berlin')) WHERE `first_seen_int` = 0;
UPDATE `player_overall` SET `last_seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`last_seen`, 'UTC', 'Europe/Berlin')) WHERE `last_seen_int` = 0;

UPDATE `player_log_name` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `player_log_vacation` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;


UPDATE `highscore_1_0` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_1_1` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_1_2` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_1_3` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_1_4` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_1_5` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_1_6` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_1_7` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;

UPDATE `highscore_2_0` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_2_1` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_2_2` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_2_3` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_2_4` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_2_5` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_2_6` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;
UPDATE `highscore_2_7` SET `seen_int` = UNIX_TIMESTAMP(CONVERT_TZ(`seen`, 'UTC', 'Europe/Berlin')) WHERE `seen_int` = 0;


UPDATE `tasks` SET `due_time_int` = UNIX_TIMESTAMP(CONVERT_TZ(`due_time`, 'UTC', 'Europe/Berlin')) WHERE `due_time_int` = 0;
