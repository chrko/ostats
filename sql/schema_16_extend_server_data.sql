ALTER TABLE `server_data`
  ADD `researchDurationDivisor` TINYINT NULL DEFAULT NULL
  AFTER `probeCargo`,
  ADD `darkMatterNewAcount` MEDIUMINT UNSIGNED NULL DEFAULT NULL
  AFTER `researchDurationDivisor`;
