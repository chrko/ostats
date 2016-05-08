# Search: ^ALTER TABLE `(.*)` ADD `(.*)` BIGINT NOT NULL DEFAULT 0 AFTER `(.*)`;$
# Replace: ALTER TABLE `$1` DROP `$3`;\nALTER TABLE `$1` ADD `$3` TIMESTAMP AS (FROM_UNIXTIME(`$2`)) VIRTUAL AFTER `$2`;

# ALTER TABLE `alliance` DROP `last_update`;
# ALTER TABLE `alliance` ADD `last_update` TIMESTAMP AS (FROM_UNIXTIME(`last_update_int`)) VIRTUAL AFTER `open`;

ALTER TABLE `alliance` DROP `last_update`;
ALTER TABLE `alliance` ADD `last_update` TIMESTAMP AS (FROM_UNIXTIME(`last_update_int`)) VIRTUAL AFTER `last_update_int`;

ALTER TABLE `alliance_log_homepage` DROP `seen`;
ALTER TABLE `alliance_log_homepage` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `alliance_log_logo` DROP `seen`;
ALTER TABLE `alliance_log_logo` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `alliance_log_name` DROP `seen`;
ALTER TABLE `alliance_log_name` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `alliance_log_tag` DROP `seen`;
ALTER TABLE `alliance_log_tag` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;

ALTER TABLE `alliance_overall` DROP `first_seen`;
ALTER TABLE `alliance_overall` ADD `first_seen` TIMESTAMP AS (FROM_UNIXTIME(`first_seen_int`)) VIRTUAL AFTER `first_seen_int`;
ALTER TABLE `alliance_overall` DROP `last_seen`;
ALTER TABLE `alliance_overall` ADD `last_seen` TIMESTAMP AS (FROM_UNIXTIME(`last_seen_int`)) VIRTUAL AFTER `last_seen_int`;

ALTER TABLE `alliance_member` DROP `first_seen`;
ALTER TABLE `alliance_member` ADD `first_seen` TIMESTAMP AS (FROM_UNIXTIME(`first_seen_int`)) VIRTUAL AFTER `first_seen_int`;
ALTER TABLE `alliance_member` DROP `last_update`;
ALTER TABLE `alliance_member` ADD `last_update` TIMESTAMP AS (FROM_UNIXTIME(`last_update_int`)) VIRTUAL AFTER `last_update_int`;

ALTER TABLE `alliance_member_log` DROP `first_seen`;
ALTER TABLE `alliance_member_log` ADD `first_seen` TIMESTAMP AS (FROM_UNIXTIME(`first_seen_int`)) VIRTUAL AFTER `first_seen_int`;
ALTER TABLE `alliance_member_log` DROP `last_seen`;
ALTER TABLE `alliance_member_log` ADD `last_seen` TIMESTAMP AS (FROM_UNIXTIME(`last_seen_int`)) VIRTUAL AFTER `last_seen_int`;


ALTER TABLE `moon` DROP `first_seen`;
ALTER TABLE `moon` ADD `first_seen` TIMESTAMP AS (FROM_UNIXTIME(`first_seen_int`)) VIRTUAL AFTER `first_seen_int`;
ALTER TABLE `moon` DROP `last_update`;
ALTER TABLE `moon` ADD `last_update` TIMESTAMP AS (FROM_UNIXTIME(`last_update_int`)) VIRTUAL AFTER `last_update_int`;

ALTER TABLE `moon_overall` DROP `first_seen`;
ALTER TABLE `moon_overall` ADD `first_seen` TIMESTAMP AS (FROM_UNIXTIME(`first_seen_int`)) VIRTUAL AFTER `first_seen_int`;
ALTER TABLE `moon_overall` DROP `last_seen`;
ALTER TABLE `moon_overall` ADD `last_seen` TIMESTAMP AS (FROM_UNIXTIME(`last_seen_int`)) VIRTUAL AFTER `last_seen_int`;


ALTER TABLE `planet` DROP `first_seen`;
ALTER TABLE `planet` ADD `first_seen` TIMESTAMP AS (FROM_UNIXTIME(`first_seen_int`)) VIRTUAL AFTER `first_seen_int`;
ALTER TABLE `planet` DROP `last_update`;
ALTER TABLE `planet` ADD `last_update` TIMESTAMP AS (FROM_UNIXTIME(`last_update_int`)) VIRTUAL AFTER `last_update_int`;

ALTER TABLE `planet_overall` DROP `first_seen`;
ALTER TABLE `planet_overall` ADD `first_seen` TIMESTAMP AS (FROM_UNIXTIME(`first_seen_int`)) VIRTUAL AFTER `first_seen_int`;
ALTER TABLE `planet_overall` DROP `last_seen`;
ALTER TABLE `planet_overall` ADD `last_seen` TIMESTAMP AS (FROM_UNIXTIME(`last_seen_int`)) VIRTUAL AFTER `last_seen_int`;

ALTER TABLE `planet_relocation` DROP `seen`;
ALTER TABLE `planet_relocation` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;


ALTER TABLE `player` DROP `last_update`;
ALTER TABLE `player` ADD `last_update` TIMESTAMP AS (FROM_UNIXTIME(`last_update_int`)) VIRTUAL AFTER `last_update_int`;

ALTER TABLE `player_overall` DROP `first_seen`;
ALTER TABLE `player_overall` ADD `first_seen` TIMESTAMP AS (FROM_UNIXTIME(`first_seen_int`)) VIRTUAL AFTER `first_seen_int`;
ALTER TABLE `player_overall` DROP `last_seen`;
ALTER TABLE `player_overall` ADD `last_seen` TIMESTAMP AS (FROM_UNIXTIME(`last_seen_int`)) VIRTUAL AFTER `last_seen_int`;

ALTER TABLE `player_log_name` DROP `seen`;
ALTER TABLE `player_log_name` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `player_log_vacation` DROP `seen`;
ALTER TABLE `player_log_vacation` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;


ALTER TABLE `highscore_1_0` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_0` DROP `seen`;
ALTER TABLE `highscore_1_0` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_1_0` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_1` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_1` DROP `seen`;
ALTER TABLE `highscore_1_1` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_1_1` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_2` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_2` DROP `seen`;
ALTER TABLE `highscore_1_2` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_1_2` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_3` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_3` DROP `seen`;
ALTER TABLE `highscore_1_3` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_1_3` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_4` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_4` DROP `seen`;
ALTER TABLE `highscore_1_4` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_1_4` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_5` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_5` DROP `seen`;
ALTER TABLE `highscore_1_5` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_1_5` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_6` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_6` DROP `seen`;
ALTER TABLE `highscore_1_6` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_1_6` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_7` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_7` DROP `seen`;
ALTER TABLE `highscore_1_7` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_1_7` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_0` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_0` DROP `seen`;
ALTER TABLE `highscore_2_0` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_2_0` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_1` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_1` DROP `seen`;
ALTER TABLE `highscore_2_1` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_2_1` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_2` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_2` DROP `seen`;
ALTER TABLE `highscore_2_2` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_2_2` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_3` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_3` DROP `seen`;
ALTER TABLE `highscore_2_3` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_2_3` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_4` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_4` DROP `seen`;
ALTER TABLE `highscore_2_4` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_2_4` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_5` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_5` DROP `seen`;
ALTER TABLE `highscore_2_5` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_2_5` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_6` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_6` DROP `seen`;
ALTER TABLE `highscore_2_6` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_2_6` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_7` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_7` DROP `seen`;
ALTER TABLE `highscore_2_7` ADD `seen` TIMESTAMP AS (FROM_UNIXTIME(`seen_int`)) VIRTUAL AFTER `seen_int`;
ALTER TABLE `highscore_2_7` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);


ALTER TABLE `tasks` DROP `due_time`;
ALTER TABLE `tasks` ADD `due_time` TIMESTAMP AS (FROM_UNIXTIME(`due_time_int`)) VIRTUAL AFTER `due_time_int`;

# FixUp Tasks
ALTER TABLE `tasks` CHANGE `running` `running` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0';
