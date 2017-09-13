ALTER TABLE `alliance_log_homepage` DROP PRIMARY KEY, ADD PRIMARY KEY (`server_id`, `alliance_id`, `seen_int`) USING BTREE;
ALTER TABLE `alliance_log_logo` DROP PRIMARY KEY, ADD PRIMARY KEY (`server_id`, `alliance_id`, `seen_int`) USING BTREE;
ALTER TABLE `alliance_log_name` DROP PRIMARY KEY, ADD PRIMARY KEY (`server_id`, `alliance_id`, `seen_int`) USING BTREE;
ALTER TABLE `alliance_log_tag` DROP PRIMARY KEY, ADD PRIMARY KEY (`server_id`, `alliance_id`, `seen_int`) USING BTREE;

ALTER TABLE `alliance_member_log` DROP PRIMARY KEY, ADD PRIMARY KEY (`server_id`, `alliance_id`, `player_id`, `first_seen_int`) USING BTREE;

ALTER TABLE `planet_relocation` DROP PRIMARY KEY, ADD PRIMARY KEY (`server_id`, `id`, `seen_int`) USING BTREE;

ALTER TABLE `player_log_name` DROP PRIMARY KEY, ADD PRIMARY KEY (`server_id`, `id`, `seen_int`) USING BTREE;
ALTER TABLE `player_log_vacation` DROP PRIMARY KEY, ADD PRIMARY KEY (`server_id`, `id`, `seen_int`) USING BTREE;


