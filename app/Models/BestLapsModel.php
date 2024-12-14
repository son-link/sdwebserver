<?php
namespace App\Models;
use App\Models\BaseModel;

class BestLapsModel extends BaseModel
{
	protected $table      = 'bests_laps bl';
	protected $allowedFields = ['race_id', 'lap_id', 'track_id', 'car_cat', 'car_id', 'laptime', 'user_id', 'setup'];

	public function getBests(int $backto, string $carCat, int $page=0, int $limit=20)
	{
		$from = $page * $limit;
		$list = [];

		$builder = $this->builder();
		$builder->join('laps l', 'l.id = bl.lap_id');
		$builder->select('l.race_id, r.track_id, r.car_id, r.user_id, r.timestamp, l.wettness, bl.laptime AS bestlap, c.name AS car_name, t.name AS track_name, u.username');
		$builder->join('races r', 'r.id = bl.race_id');
		$builder->join('cars c', 'c.id = bl.car_id');
		$builder->join('tracks t', 't.id = bl.track_id');
		$builder->join('users u', 'u.id = bl.user_id');
		$builder->where('UNIX_TIMESTAMP(r.timestamp) >', $backto);
		$builder->where('bl.car_cat', $carCat);
		$builder->groupBy(['r.track_id', 'l.wettness']);
		if ($limit > 0) $builder->limit($from, $limit);
		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) $list = $query->getResult();

		return $list;
	}
}
