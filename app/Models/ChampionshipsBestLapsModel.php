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

	/**
	 * Return a list of the best lap for every user
	 * @param string $date_start La fecha en el que comienza el campeonato
	 * @param string $date_end La fecha en la que acaba el campeonato
	 * @param string $track_id El ID de la pista
	 * @param string $car_cat La categoría de los coches
	 * @param string $wettness El tiempo
	 * @return array Un array con los resultados
	 */
	public function getChampionshipData(string $date_start, string $date_end, string $track_id, string $car_cat, string $wettness): array
	{
		$list = [];
		$date_start = $this->db->escape($date_start);
		$date_end = $this->db->escape($date_end);
		$track_id = $this->db->escape($date_end);
		$car_cat = $this->db->escape($car_cat);
		$wettness = $this->db->escape($wettness);

		$builder = $this->builder();
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
		$builder->where('cbl.car_cat', $car_cat);
		$builder->where('cbl.track_id', $track_id);
		$builder->where('cbl.wettness', $wettness);
		$builder->groupBy('r.id');
		$builder->orderBy('cbl.laptime');
		$query = $builder->get();
		
		if ($query && $query->getNumRows() > 0) $list = $query->getResult();

		return $list;
	}

	/**
	 * Return a list of the best lap for every user
	 * @param string $date_start La fecha en el que comienza el campeonato
	 * @param string $date_end La fecha en la que acaba el campeonato
	 * @param string $track_id El ID de la pista
	 * @param string $car_cat La categoría de los coches
	 * @param string $wettness El tiempo
	 * @return array Un array con los resultados
	 */
	public function getChampionshipTotals(string $date_start, string $date_end, string $track_id, string $car_cat, string $wettness): array
	{
		$data = [
			'total_races'	=> 0,
			'total_laps'	=> 0,
		];

		$builder = $this->db->table('races r');
		$builder->join('laps l', 'l.race_id = r.id');
		$builder->select('COUNT(DISTINCT r.id) AS total_races, COUNT(l.id) AS total_laps');
		$builder->where([
			'l.car_cat'		=> $car_cat,
			'l.track_id' 	=> $track_id,
			'l.wettness'	=> $wettness
		]);
		$builder->where("r.timestamp BETWEEN {$date_start} AND {$date_end}");
		$query = $builder->get();

		if ($query && $query->getNumRows() == 1)
		{
			$data = $query->getRow();
		}

		return $data;
	}
}
