DROP TRIGGER IF EXISTS `alliance_member_insert`;
CREATE TRIGGER `alliance_member_insert` AFTER INSERT ON `alliance_member`
FOR EACH ROW INSERT INTO `alliance_member_log` VALUES (
    NEW.`server_id`,
    NEW.`alliance_id`,
    NEW.`player_id`,
    NEW.`first_seen`,
    NULL
)
ON DUPLICATE KEY UPDATE `last_seen` = NULL;

DROP TRIGGER IF EXISTS `alliance_member_delete`;
CREATE TRIGGER `alliance_member_delete` AFTER DELETE ON `alliance_member`
FOR EACH ROW INSERT INTO `alliance_member_log` VALUES (
    OLD.`server_id`,
    OLD.`alliance_id`,
    OLD.`player_id`,
    OLD.`first_seen`,
    OLD.`last_update`
)
ON DUPLICATE KEY UPDATE
    `last_seen` = OLD.`last_update`;

DROP TRIGGER IF EXISTS `alliance_insert`;
CREATE TRIGGER `alliance_insert` AFTER INSERT ON `alliance`
FOR EACH ROW INSERT INTO `alliance_overall` VALUES (
    NEW.`server_id`,
    NEW.`id`,
    NEW.`name`,
    NEW.`tag`,
    NEW.`homepage`,
    NEW.`logo`,
    NEW.`last_update`,
    NULL
)
ON DUPLICATE KEY UPDATE
    `name`      = NEW.`name`,
    `tag`       = NEW.`tag`,
    `homepage`  = NEW.`homepage`,
    `logo`      = NEW.`logo`,
    `last_seen` = NULL;

DROP TRIGGER IF EXISTS `alliance_delete`;
CREATE TRIGGER `alliance_delete` AFTER DELETE ON `alliance`
FOR EACH ROW UPDATE `alliance_overall`
SET `last_seen` = OLD.`last_update`
WHERE `id` = OLD.`id` AND `server_id` = OLD.`server_id`;

DROP TRIGGER IF EXISTS `alliance_update`;
DELIMITER $$
CREATE TRIGGER `alliance_update` AFTER UPDATE ON `alliance`
FOR EACH ROW BEGIN
    UPDATE `alliance_overall`
    SET
        `name` = NEW.`name`,
        `tag`  = NEW.`tag`,
        `homepage` = NEW.`homepage`,
        `logo` = NEW.`logo`
    WHERE `id` = NEW.`id` AND `server_id` = NEW.`server_id`;

    IF OLD.`name` <> NEW.`name`
    THEN
        INSERT INTO `alliance_log_name` VALUES (
            NEW.`server_id`,
            NEW.`id`,
            NEW.`last_update`,
            OLD.`name`,
            NEW.`name`
        )
        ON DUPLICATE KEY UPDATE
            `old_name` = OLD.`name`,
            `new_name` = NEW.`name`;
    END IF;

    IF OLD.`tag` <> NEW.`tag`
    THEN
        INSERT INTO `alliance_log_tag` VALUES (
            NEW.`server_id`,
            NEW.`id`,
            NEW.`last_update`,
            OLD.`tag`,
            NEW.`tag`
        )
        ON DUPLICATE KEY UPDATE
            `old_tag` = OLD.`tag`,
            `new_tag` = NEW.`tag`;
    END IF;

    IF OLD.`logo` <> NEW.`logo`
       OR (OLD.`logo` IS NULL AND NEW.`logo` IS NOT NULL)
       OR (OLD.`logo` IS NOT NULL AND NEW.`logo` IS NULL)
    THEN
        INSERT INTO `alliance_log_logo` VALUES (
            NEW.`server_id`,
            NEW.`id`,
            NEW.`last_update`,
            OLD.`logo`,
            NEW.`logo`
        )
        ON DUPLICATE KEY UPDATE
            `old_logo` = OLD.`logo`,
            `new_logo` = NEW.`logo`;
    END IF;

    IF OLD.`homepage` <> NEW.`homepage`
       OR (OLD.`homepage` IS NULL AND NEW.`homepage` IS NOT NULL)
       OR (OLD.`homepage` IS NOT NULL AND NEW.`homepage` IS NULL)
    THEN
        INSERT INTO `alliance_log_homepage` VALUES (
            NEW.`server_id`,
            NEW.`id`,
            NEW.`last_update`,
            OLD.`homepage`,
            NEW.`homepage`
        )
        ON DUPLICATE KEY UPDATE
            `old_homepage` = OLD.`homepage`,
            `new_homepage` = NEW.`homepage`;
    END IF;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS `player_insert`;
CREATE TRIGGER `player_insert` AFTER INSERT ON `player`
FOR EACH ROW INSERT INTO `player_overall`
VALUES (
    NEW.`server_id`,
    NEW.`id`,
    NEW.`name`,
    NEW.`vacation`,
    NEW.`inactive`,
    NEW.`inactive_long`,
    NEW.`banned`,
    NEW.`outlaw`,
    NEW.`admin`,
    NEW.`last_update`,
    NULL
)
ON DUPLICATE KEY UPDATE
    `name`          = NEW.`name`,
    `vacation`      = NEW.`vacation`,
    `inactive`      = NEW.`inactive`,
    `inactive_long` = NEW.`inactive_long`,
    `banned`        = NEW.`banned`,
    `outlaw`        = NEW.`outlaw`,
    `admin`         = NEW.`admin`,
    `last_seen`     = NULL;

DROP TRIGGER IF EXISTS `player_delete`;
CREATE TRIGGER `player_delete` AFTER DELETE ON `player`
FOR EACH ROW UPDATE `player_overall`
SET `last_seen` = OLD.`last_update`
WHERE `id` = OLD.`id` AND `server_id` = OLD.`server_id`;


DROP TRIGGER IF EXISTS `player_update`;
DELIMITER $$
CREATE TRIGGER `player_update` AFTER UPDATE ON `player`
FOR EACH ROW BEGIN
    UPDATE `player_overall`
    SET
        `name`          = NEW.`name`,
        `vacation`      = NEW.`vacation`,
        `inactive`      = NEW.`inactive`,
        `inactive_long` = NEW.`inactive_long`,
        `banned`        = NEW.`banned`,
        `outlaw`        = NEW.`outlaw`,
        `admin`         = NEW.`admin`
    WHERE `id` = NEW.`id` AND `server_id` = NEW.`server_id`;

    IF OLD.`name` <> NEW.`name`
    THEN
        INSERT IGNORE INTO `player_log_name` VALUES (
            NEW.`server_id`,
            NEW.`id`,
            NEW.`last_update`,
            OLD.`name`,
            NEW.`name`
        );
    END IF;

    IF OLD.`vacation` <> NEW.`vacation`
    THEN
        INSERT IGNORE INTO `player_log_vacation` VALUES (
            NEW.`server_id`,
            NEW.`id`,
            NEW.`last_update`,
            NEW.`vacation`
        );
    END IF;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS `planet_insert`;
CREATE TRIGGER `planet_insert` AFTER INSERT ON `planet`
FOR EACH ROW INSERT INTO `planet_overall` VALUES (
    NEW.`server_id`,
    NEW.`id`,
    NEW.`name`,
    NEW.`galaxy`,
    NEW.`system`,
    NEW.`position`,
    NEW.`player_id`,
    NEW.`first_seen`,
    NULL
)
ON DUPLICATE KEY UPDATE
    `name`      = NEW.`name`,
    `galaxy`    = NEW.`galaxy`,
    `system`    = NEW.`system`,
    `position`  = NEW.`position`,
    `last_seen` = NULL;

DROP TRIGGER IF EXISTS `planet_update`;
DELIMITER $$
CREATE TRIGGER `planet_update` AFTER UPDATE ON `planet`
FOR EACH ROW BEGIN
    INSERT INTO `planet_overall` VALUES (
        NEW.`server_id`,
        NEW.`id`,
        NEW.`name`,
        NEW.`galaxy`,
        NEW.`system`,
        NEW.`position`,
        NEW.`player_id`,
        NEW.`first_seen`,
        NULL
    )
    ON DUPLICATE KEY UPDATE
        `name`      = NEW.`name`,
        `galaxy`    = NEW.`galaxy`,
        `system`    = NEW.`system`,
        `position`  = NEW.`position`,
        `last_seen` = NULL;

    IF OLD.`galaxy` <> NEW.`galaxy`
       OR OLD.`system` <> NEW.`system`
       OR OLD.`position` <> NEW.`position`
    THEN
        INSERT IGNORE INTO `planet_relocation` VALUES (
            NEW.`server_id`,
            NEW.`id`,
            NEW.`last_update`,
            OLD.`galaxy`,
            OLD.`system`,
            OLD.`position`,
            NEW.`galaxy`,
            NEW.`system`,
            NEW.`position`
        );
    END IF;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS `planet_delete`;
CREATE TRIGGER `planet_delete` AFTER DELETE ON `planet`
FOR EACH ROW INSERT INTO `planet_overall` VALUES (
    OLD.`server_id`,
    OLD.`id`,
    OLD.`name`,
    OLD.`galaxy`,
    OLD.`system`,
    OLD.`position`,
    OLD.`player_id`,
    OLD.`first_seen`,
    OLD.`last_update`
)
ON DUPLICATE KEY UPDATE
    `name`      = OLD.`name`,
    `galaxy`    = OLD.`galaxy`,
    `system`    = OLD.`system`,
    `position`  = OLD.`position`,
    `last_seen` = OLD.`last_update`;

DROP TRIGGER IF EXISTS `moon_insert`;
CREATE TRIGGER `moon_insert` AFTER INSERT ON `moon`
FOR EACH ROW INSERT INTO `moon_overall` VALUES (
    NEW.`server_id`,
    NEW.`id`,
    NEW.`planet_id`,
    NEW.`size`,
    NEW.`name`,
    NEW.`first_seen`,
    NULL
)
ON DUPLICATE KEY UPDATE
    `name`      = NEW.`name`,
    `last_seen` = NULL;

DROP TRIGGER IF EXISTS `moon_update`;
CREATE TRIGGER `moon_update` AFTER UPDATE ON `moon`
FOR EACH ROW INSERT INTO `moon_overall` VALUES (
    NEW.`server_id`,
    NEW.`id`,
    NEW.`planet_id`,
    NEW.`size`,
    NEW.`name`,
    NEW.`first_seen`,
    NULL
)
ON DUPLICATE KEY UPDATE
    `name`      = NEW.`name`,
    `last_seen` = NULL;

DROP TRIGGER IF EXISTS `moon_delete`;
CREATE TRIGGER `moon_delete` AFTER DELETE ON `moon`
FOR EACH ROW INSERT INTO `moon_overall` VALUES (
    OLD.`server_id`,
    OLD.`id`,
    OLD.`planet_id`,
    OLD.`size`,
    OLD.`name`,
    OLD.`first_seen`,
    NULL
)
ON DUPLICATE KEY UPDATE
    `name`      = OLD.`name`,
    `last_seen` = OLD.`last_update`;