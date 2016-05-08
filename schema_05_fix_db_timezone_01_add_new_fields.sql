ALTER TABLE `alliance` ADD `last_update_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_update`;

ALTER TABLE `alliance_log_homepage` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `alliance_log_logo` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `alliance_log_name` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `alliance_log_tag` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;

ALTER TABLE `alliance_overall` ADD `first_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `first_seen`;
ALTER TABLE `alliance_overall` ADD `last_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_seen`;


ALTER TABLE `alliance_member` ADD `first_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `first_seen`;
ALTER TABLE `alliance_member` ADD `last_update_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_update`;

ALTER TABLE `alliance_member_log` ADD `first_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `first_seen`;
ALTER TABLE `alliance_member_log` ADD `last_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_seen`;


ALTER TABLE `moon` ADD `first_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `first_seen`;
ALTER TABLE `moon` ADD `last_update_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_update`;

ALTER TABLE `moon_overall` ADD `first_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `first_seen`;
ALTER TABLE `moon_overall` ADD `last_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_seen`;


ALTER TABLE `planet` ADD `first_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `first_seen`;
ALTER TABLE `planet` ADD `last_update_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_update`;

ALTER TABLE `planet_overall` ADD `first_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `first_seen`;
ALTER TABLE `planet_overall` ADD `last_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_seen`;

ALTER TABLE `planet_relocation` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;


ALTER TABLE `player` ADD `last_update_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_update`;

ALTER TABLE `player_overall` ADD `first_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `first_seen`;
ALTER TABLE `player_overall` ADD `last_seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `last_seen`;

ALTER TABLE `player_log_name` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `player_log_vacation` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;


ALTER TABLE `highscore_1_0` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_1_1` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_1_2` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_1_3` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_1_4` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_1_5` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_1_6` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_1_7` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;

ALTER TABLE `highscore_2_0` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_2_1` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_2_2` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_2_3` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_2_4` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_2_5` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_2_6` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;
ALTER TABLE `highscore_2_7` ADD `seen_int` BIGINT NOT NULL DEFAULT 0 AFTER `seen`;


ALTER TABLE `tasks` ADD `due_time_int` BIGINT NOT NULL DEFAULT 0 AFTER `due_time`;
