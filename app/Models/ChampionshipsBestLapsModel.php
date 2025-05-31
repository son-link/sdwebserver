<?php

namespace App\Models;

use App\Models\BaseModel;

class ChampionshipsBestLapsModel extends BaseModel
{
	protected $table      = 'championship_bestlaps cbl';
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
		if ($query->have_lap == 1 && $data->laptime < $query->laptime) {
			$this->set([
				'lap_id'	=> $lap,
				'laptime'	=> $data->laptime,
				'car_id'	=> $data->car_id,
				'race_id'	=> $data->race_id
			])->where($where)->update();
		} else if ($query->have_lap == 0) {
			$data->lap_id = $lap;
			$this->insert($data);
		}
	}

	public function getChampionshipData($data)
	{
		$date_start = $this->db->escape($data->date_start);
		$date_end = $this->db->escape($data->date_end);

		$chblModel = new ChampionshipsBestLapsModel;
		$builder = $chblModel->builder();
		$builder->select('cbl.race_id, cbl.track_id, cbl.car_id, cbl.user_id, r.timestamp, cbl.wettness');
		$builder->select('cbl.laptime, c.name AS car_name, t.name AS track_name, u.username');
		$builder->select('cc.name AS category_name, l.valid');
		$builder->join('races r', 'r.id = cbl.race_id');
		$builder->join('laps l', 'l.id = cbl.lap_id');
		$builder->join('cars c', 'c.id = cbl.car_id');
		$builder->join('tracks t', 't.id = cbl.track_id');
		$builder->join('users u', 'u.id = cbl.user_id');
		$builder->join('cars_cats cc', 'cc.id = cbl.car_cat');
		$builder->where("r.timestamp BETWEEN {$date_start} AND {$date_end}");
		$builder->where('cbl.car_cat', $data->car_cat);
		$builder->where('cbl.track_id', $data->track_id);
		$builder->where('cbl.wettness', $data->wettness);
		$builder->groupBy('r.id');
		$builder->orderBy('cbl.laptime');
		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) return $query->getResult();
		return [];
	}
}
