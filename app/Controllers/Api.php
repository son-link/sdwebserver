<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BestLapsModel;
use App\Models\CarsModel;
use App\Models\CarCatsModel;
use App\Models\TracksModel;
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
}