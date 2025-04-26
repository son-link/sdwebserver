<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BestLapsModel;
use App\Models\CarsModel;
use App\Models\CarCatsModel;
use App\Models\TracksModel;
use App\Models\UsersModel;
use CodeIgniter\API\ResponseTrait;

class Api extends BaseController
{
	use ResponseTrait;

	public function getBestsLaps()
	{
		$bestLapsModel = new BestLapsModel;
		$period = $this->request->getGet('period');
		$carCatId = $this->request->getGet('car_cat');

		if (!$period || !$carCatId) return $this->fail('The period and/or category were not indicated');

		$page = $this->request->getGet('page');
		$limit = $this->request->getGet('limit');
		if (!$page | !is_numeric($page)) $page = 0;
		if (!$limit | !is_numeric($limit)) $limit = 0;

		[$list, $total] = $bestLapsModel->getBests($period, $carCatId, $page, $limit);

		return $this->respond(['data' => $list, 'total' => $total]);
	}

	public function getMostActiveUsers()
	{
		$period = $this->request->getGet('period');
		$carCatId = $this->request->getGet('car_cat');

		if (!$period || !$carCatId) return $this->fail('The period and/or category were not indicated');

		$backto = getDateDiff($period);
		$carCatsModel = new CarCatsModel;

		$carsCatIds = $carCatsModel->getCarsInCat($carCatId);

		$builder = $this->db->table('races r');
		$builder->select('COUNT(*) AS count, u.username');
		$builder->join('users u', 'u.id = r.user_id');
		$builder->where('UNIX_TIMESTAMP(r.timestamp) >', $backto);
		$builder->whereIn('r.car_id', $carsCatIds);
		$builder->groupBy('u.username');
		$builder->orderBy('count DESC');
	
		$query = $builder->get(20);
		$list = [];
		$total = 0;

		if ($query && $query->getNumRows() > 0) {
			$list = $query->getResult();
			$total = $query->getNumRows();
		}

		return $this->respond(['data' => $list, 'total' => $total]);
	}

	public function getMostUsedTracks()
	{
		$period = $this->request->getGet('period');
		$carCatId = $this->request->getGet('car_cat');

		if (!$period || !$carCatId) return $this->fail('The period and/or category were not indicated');

		$page = $this->request->getGet('page');
		$limit = $this->request->getGet('limit');
		if (!$page | !is_numeric($page)) $page = 0;
		if (!$limit | !is_numeric($limit)) $limit = 0;

		$carCatsModel = new CarCatsModel;
		$tracksModel = new TracksModel();

		$carsCatIds = $carCatsModel->getCarsInCat($carCatId);
		$list = [];
		$total = 0;

		[$list, $total] = $tracksModel->getMostUsedTracks($carsCatIds, $period, $page, $limit);

		return $this->respond(['data' => $list, 'total' => $total]);
	}

	public function getMostUsedCars()
	{
		$period = $this->request->getGet('period');
		$carCatId = $this->request->getGet('car_cat');

		if (!$period || !$carCatId) return $this->fail('The period and/or category were not indicated');

		$page = $this->request->getGet('page');
		$limit = $this->request->getGet('limit');
		if (!$page | !is_numeric($page)) $page = 0;
		if (!$limit | !is_numeric($limit)) $limit = 0;

		$carCatsModel = new CarCatsModel;
		$carsModel = new CarsModel;

		$carsCatIds = $carCatsModel->getCarsInCat($carCatId);
		$list = [];
		$total = 0;

		[$list, $total] = $carsModel->getMostUsedCars($carsCatIds, $period, $page, $limit);

		return $this->respond(['data' => $list, 'total' => $total]);
	}

	public function getUserRaces()
	{
		$username = $this->request->getGet('username');

		if (!$username) return $this->fail('The period and/or category were not indicated');

		$page = $this->request->getGet('page');
		$limit = $this->request->getGet('limit');
		if (!$page | !is_numeric($page)) $page = 0;
		if (!$limit | !is_numeric($limit)) $limit = 0;

		$usersModel = new UsersModel();
		$usersModel->getUser($username);
		$list = [];
		$total = 0;

		[$list, $total] = $usersModel->getRaceSessions($page, $limit);

		return $this->respond(['data' => $list, 'total' => $total]);
	}

	public function getChampionshipBestLaps()
	{
		$id = $this->request->getGet('championship');
		$list = [];

		if (!$id) return $this->respond($list);

		$query = $this->db->query('SELECT * FROM championship WHERE id = ?', [$id]);
		if ($query && $query->getNumRows() > 0)
		{
			$data = $query->getRow();
			$builder = $this->db->table('laps l');
			$builder->select('l.race_id, r.track_id, r.car_id, r.user_id, r.timestamp, l.wettness');
			$builder->select('MIN(l.laptime) AS laptime, c.name AS car_name, t.name AS track_name, u.username');
			$builder->select('cc.name AS category_name, l.valid');
			$builder->join('races r', 'r.id = l.race_id');
			$builder->join('cars c', 'c.id = r.car_id');
			$builder->join('tracks t', 't.id = r.track_id');
			$builder->join('users u', 'u.id = r.user_id');
			$builder->join('cars_cats cc', 'cc.id = l.car_cat');
			$builder->where('r.timestamp >= ', $data->date_start);
			$builder->where('r.timestamp <= ', $data->date_end);
			$builder->where('l.car_cat', $data->car_cat);
			$builder->where('r.track_id', $data->track_id);
			$builder->where('l.wettness', $data->wettness);
			$builder->orderBy('laptime');
			$builder->groupBy(['r.user_id']);
			$query = $builder->get();

			if ($query && $query->getNumRows() > 0) $list = $query->getResult();

			return $this->respond(['data' => $list]);
		}
	}
}