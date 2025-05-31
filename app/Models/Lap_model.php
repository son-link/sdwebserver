<?php

namespace App\Models;

use CodeIgniter\Model;

class Lap_model extends Model
{
    protected $DBGroup = 'default';

    public function get_fastest_lap_windows()
    {
      $sql = "
        WITH current_championship AS (
          SELECT *
          FROM championship
          WHERE NOW() BETWEEN date_start AND date_end
          ORDER BY date_start DESC
          LIMIT 1
        ),
        eligible_cars AS (
          SELECT id AS car_id
          FROM cars
          WHERE category COLLATE utf8mb4_unicode_ci = (SELECT car_cat FROM current_championship)
        ),
        eligible_races AS (
          SELECT r.*
          FROM races r
          JOIN eligible_cars c ON r.car_id = c.car_id
          WHERE r.track_id COLLATE utf8mb4_unicode_ci = (SELECT track_id FROM current_championship)
            AND r.`timestamp` BETWEEN (SELECT date_start FROM current_championship)
                                AND (SELECT date_end FROM current_championship)
        ),
        ordered_laps AS (
          SELECT
            l.id,
            l.race_id,
            r.user_id,
            u.username,
            r.track_id,
            r.car_id,
            c.name AS car_name,
            l.laptime,
            l.valid,
            l.wettness,
            ROW_NUMBER() OVER (
              PARTITION BY l.race_id, r.user_id, r.track_id, r.car_id
              ORDER BY l.id
            ) AS rn
          FROM laps l
          JOIN eligible_races r ON l.race_id = r.id
          JOIN users u ON r.user_id = u.id
          JOIN cars c ON r.car_id = c.id
        ),
        lap_windows AS (
          SELECT
            l1.race_id,
            l1.user_id,
            l1.username,
            l1.track_id,
            l1.car_id,
            l1.car_name,
            l1.rn AS start_rn,
            COUNT(*) AS total_laps,
            SUM(CASE WHEN l2.valid = 1 THEN 1 ELSE 0 END) AS valid_laps,
            SUM(l2.laptime) AS total_laptime,
            GROUP_CONCAT(l2.id ORDER BY l2.id) AS lap_ids,
            MIN(l2.wettness) AS min_wettness,
            MAX(l2.wettness) AS max_wettness
          FROM ordered_laps l1
          JOIN ordered_laps l2
            ON l1.race_id = l2.race_id
            AND l1.user_id = l2.user_id
            AND l1.track_id = l2.track_id
            AND l1.car_id = l2.car_id
            AND l2.rn BETWEEN l1.rn AND l1.rn + 2
          GROUP BY l1.race_id, l1.user_id, l1.username, l1.track_id, l1.car_id, l1.car_name, l1.rn
          HAVING COUNT(*) = 3
            AND SUM(CASE WHEN l2.valid = 1 THEN 1 ELSE 0 END) >= 2
            AND l1.rn = 1
            AND MIN(l2.wettness) = MAX(l2.wettness)
            AND MIN(l2.wettness) = (SELECT wettness FROM current_championship)
        ),
        best_lap_windows AS (
          SELECT *,
                ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY total_laptime ASC) AS rn
          FROM lap_windows
        )
        SELECT *
        FROM best_lap_windows
        WHERE rn = 1;

      ";

      return $this->db->query($sql)->getResult();
  }
}