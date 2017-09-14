DROP TABLE IF EXISTS `schema_history`;
CREATE TABLE `schema_history` (
  `id`              SMALLINT UNSIGNED   NOT NULL     AUTO_INCREMENT,
  `migration`       VARCHAR(100)
                    CHARACTER SET ascii NOT NULL,
  `deploy_time_int` BIGINT(20)          NOT NULL     DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `migration` (`migration`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `schema_history` (`migration`) VALUES
  ('schema_00_base.sql'),
  ('schema_01_highscore_split_type_01_create_new_tables.sql'),
  ('schema_01_highscore_split_type_02_data_migration.sql'),
  ('schema_01_highscore_split_type_03_delete_old_tables.sql'),
  ('schema_02_task_extend_type.sql'),
  ('schema_03_highscore_add_position.sql'),
  ('schema_04_highscore_partition.sql'),
  ('schema_05_fix_db_timezone_01_add_new_fields.sql'),
  ('schema_05_fix_db_timezone_02_migrate_data.sql'),
  ('schema_05_fix_db_timezone_03_fix_indexes_keys.sql'),
  ('schema_05_fix_db_timezone_04_remove_old_column.sql'),
  ('schema_06_change_server_id_encoding.sql'),
  ('schema_07_add_banned_log.sql'),
  ('schema_08_add_player_inactivity_logs.sql'),
  ('schema_09_add_schema_history_table.sql');
