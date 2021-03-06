ALTER TABLE `highscore_1_0` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_1_1` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_1_2` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_1_3` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_1_4` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_1_5` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_1_6` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_1_7` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_2_0` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_2_1` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_2_2` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_2_3` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_2_4` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_2_5` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_2_6` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);

ALTER TABLE `highscore_2_7` PARTITION BY RANGE (UNIX_TIMESTAMP(`seen`))
PARTITIONS 2 (
PARTITION p0 VALUES LESS THAN (0),
PARTITION pLast VALUES LESS THAN MAXVALUE
);
