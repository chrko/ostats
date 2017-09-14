INSERT INTO `tasks` (`due_time_int`, `job_type`, `slug`, `job`)
  SELECT
    t_o.`due_time_int`,
    CONCAT('xml-', t_o.`endpoint`),
    CONCAT(t_o.`server_id`, '-', t_o.`endpoint`, '-', t_o.`category`, '-', t_o.`type`),
    t_o.`job`
  FROM `tasks_old` AS t_o;
