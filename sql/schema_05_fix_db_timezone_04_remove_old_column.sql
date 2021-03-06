ALTER TABLE `alliance` DROP `last_update`;

ALTER TABLE `alliance_log_homepage` DROP `seen`;
ALTER TABLE `alliance_log_logo` DROP `seen`;
ALTER TABLE `alliance_log_name` DROP `seen`;
ALTER TABLE `alliance_log_tag` DROP `seen`;

ALTER TABLE `alliance_overall` DROP `first_seen`;
ALTER TABLE `alliance_overall` DROP `last_seen`;

ALTER TABLE `alliance_member` DROP `first_seen`;
ALTER TABLE `alliance_member` DROP `last_update`;

ALTER TABLE `alliance_member_log` DROP `first_seen`;
ALTER TABLE `alliance_member_log` DROP `last_seen`;


ALTER TABLE `moon` DROP `first_seen`;
ALTER TABLE `moon` DROP `last_update`;

ALTER TABLE `moon_overall` DROP `first_seen`;
ALTER TABLE `moon_overall` DROP `last_seen`;


ALTER TABLE `planet` DROP `first_seen`;
ALTER TABLE `planet` DROP `last_update`;

ALTER TABLE `planet_overall` DROP `first_seen`;
ALTER TABLE `planet_overall` DROP `last_seen`;

ALTER TABLE `planet_relocation` DROP `seen`;


ALTER TABLE `player` DROP `last_update`;

ALTER TABLE `player_overall` DROP `first_seen`;
ALTER TABLE `player_overall` DROP `last_seen`;

ALTER TABLE `player_log_name` DROP `seen`;
ALTER TABLE `player_log_vacation` DROP `seen`;


ALTER TABLE `highscore_1_0` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_0` DROP `seen`;
ALTER TABLE `highscore_1_0` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_1` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_1` DROP `seen`;
ALTER TABLE `highscore_1_1` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_2` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_2` DROP `seen`;
ALTER TABLE `highscore_1_2` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_3` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_3` DROP `seen`;
ALTER TABLE `highscore_1_3` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_4` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_4` DROP `seen`;
ALTER TABLE `highscore_1_4` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_5` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_5` DROP `seen`;
ALTER TABLE `highscore_1_5` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_6` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_6` DROP `seen`;
ALTER TABLE `highscore_1_6` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_1_7` REMOVE PARTITIONING;
ALTER TABLE `highscore_1_7` DROP `seen`;
ALTER TABLE `highscore_1_7` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_0` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_0` DROP `seen`;
ALTER TABLE `highscore_2_0` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_1` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_1` DROP `seen`;
ALTER TABLE `highscore_2_1` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_2` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_2` DROP `seen`;
ALTER TABLE `highscore_2_2` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_3` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_3` DROP `seen`;
ALTER TABLE `highscore_2_3` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_4` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_4` DROP `seen`;
ALTER TABLE `highscore_2_4` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_5` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_5` DROP `seen`;
ALTER TABLE `highscore_2_5` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_6` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_6` DROP `seen`;
ALTER TABLE `highscore_2_6` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);

ALTER TABLE `highscore_2_7` REMOVE PARTITIONING;
ALTER TABLE `highscore_2_7` DROP `seen`;
ALTER TABLE `highscore_2_7` PARTITION BY RANGE (`seen_int`) PARTITIONS 2 (PARTITION p0 VALUES LESS THAN (0), PARTITION pLast VALUES LESS THAN MAXVALUE);


ALTER TABLE `tasks` DROP `due_time`;

# FixUp Tasks
ALTER TABLE `tasks` CHANGE `running` `running` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0';
