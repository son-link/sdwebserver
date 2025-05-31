<?php

namespace App\Models;

use CodeIgniter\Model;

class RaceConfigModel extends Model
{
    protected $DBGroup = 'default';

    public function getCurrentRaceConfig(): array
    {
        // Assumes there is only one active config, or the latest one is used
		    $sql = '
          SELECT c.*, tc.name AS category
          FROM championship c
          LEFT JOIN tracks_cats tc
            ON c.track_id COLLATE utf8mb4_unicode_ci = tc.trackID COLLATE utf8mb4_unicode_ci
          WHERE NOW() BETWEEN c.date_start AND c.date_end
          ORDER BY c.date_start DESC
          LIMIT 1;
        ';

        $row = $this->db->query($sql)->getRowArray();

        $wetnessValue = [
          0 => "none",
          1 => "light",
          2 => "medium",
          3 => "heavy",
        ];

        if (count($row) > 0) {
            return [
              'track'     => $row["track_id"],
              'category'  => $row["category"],
              'laps'      => $row["raceLaps"],
              'rain'      => $wetnessValue[$row["wettness"]],
              'clouds'    => $row["clouds"],
              'season'    => $row["season"],
              'timeOfDay' => $row["timeOfDay"],
          ];
        }
        
        else {
          return [
              'track'     => 'ardennen-spa',
              'category'  => 'circuit',
              'laps'      => 10,
              'rain'      => 'none',
              'clouds'    => 'clear',
              'season'    => 'summer',
              'timeOfDay' => 'afternoon',
          ];
        }
    }
}
