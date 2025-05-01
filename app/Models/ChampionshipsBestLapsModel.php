<?php
namespace App\Models;
use App\Models\BaseModel;

class ChampionshipsBestLapsModel extends BaseModel
{
	protected $table      = 'championship_bestlaps';
	protected $allowedFields = ['user_id', 'race_id', 'lap_id', 'track_id', 'car_cat', 'car_id', 'wettness', 'laptime'];

	public function setBestLap($lap, $data)
	{
		if ($data->valid === '0') return;
		
		// Get currents championship data
		$query = $this->db->query('SELECT * FROM championship WHERE NOW() BETWEEN date_start and date_end');
		if (!$query || $query->getNumRows() == 0) return;

		$championship = $query->getRow();
		$where = [
			'user_id'	=> $data->user_id,
			'track_id'	=> $championship->track_id,
			'car_cat'	=> $championship->car_cat,
			'wettness'	=> $championship->wettness
		];

		// We check if there is a fast lap record, as well as the recorded time.
		$query = $this->select('IF(COUNT(*) > 0, 1, 0) AS have_lap, laptime')
			->where($where)
			->first();

		if (!$query) return;

		// If there is already a record, we check if the current time is less than the one obtained.
		// In case there is no record, we add it
		if ($query->have_lap == 1 && $data->laptime < $query->laptime)
		{
			$this->set([
				'lap_id'	=> $lap,
				'laptime'	=> $data->laptime,
				'car_id'	=> $data->car_id,
				'race_id'	=> $data->race_id
			])->where($where)->update();
		}
		else if ($query->have_lap == 0)
		{
			$data->lap_id = $lap;
			$this->insert($data);
		}
	}
}
