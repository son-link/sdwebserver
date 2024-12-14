DROP TABLE IF EXISTS bests_laps;
CREATE TABLE `bests_laps` (
	`race_id` INT(11) NOT NULL,
	`lap_id` INT(11) NOT NULL,
	`track_id` VARCHAR(20) NOT NULL,
	`car_cat` VARCHAR(100) NOT NULL,
	`car_id` VARCHAR(50) NOT NULL,
	`laptime` DOUBLE NOT NULL,
	`user_id` INT(11) NOT NULL,
	`setup` TEXT NULL DEFAULT NULL
)
ENGINE=InnoDB;

INSERT INTO bests_laps
SELECT DISTINCT
	r2.id AS race_id,
	l.id AS lap_id,
	r2.track_id AS track_id,
	cc.id AS car_cat,
	r2.car_id AS car_id,
	l.laptime AS laptime,
	r2.user_id AS user_id,
	r2.setup AS setup
FROM laps l
  INNER JOIN
  (
    SELECT l3.id, MIN(l3.laptime) AS bestlap
    FROM laps l3
    JOIN races r ON r.id = l3.race_id
    GROUP BY l3.race_id
  ) l2
  ON l2.id = l.id
  INNER JOIN races r2 ON r2.id = l.race_id
  INNER JOIN cars_cats cc ON cc.carID = r2.car_id
WHERE l2.bestlap = l.laptime
GROUP BY r2.track_id, cc.id