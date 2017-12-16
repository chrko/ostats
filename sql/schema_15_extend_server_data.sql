ALTER TABLE `server_data`
  ADD `bashlimit` MEDIUMINT UNSIGNED NULL DEFAULT NULL
  AFTER `globalDeuteriumSaveFactor`,
  ADD `probeCargo` MEDIUMINT UNSIGNED NULL DEFAULT NULL
  AFTER `bashlimit`;
