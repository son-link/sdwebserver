<?php

namespace App\Controllers;
use App\Models\CarCatsModel;
use App\Models\TracksModel;
use App\Models\BestLapsModel;
use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
	use ResponseTrait;

	public function index()
	{
		$tplData = [];
		$carCatModel = new CarCatsModel;
		$bestLapsModel = new BestLapsModel;

		// select interested period
		if(array_key_exists('period', $_COOKIE))
		{
			$period = $_COOKIE['period'];
		}

		if($this->request->getGet('period'))
		{
			$period = $this->request->getGet('period');
			setcookie( "period", $period, time()+(60*60*24*30) );
		}
		else $period = 'today';

		$tplData['period'] = $period;

		switch ($period)
		{
			case 'today': //today
				$datediff = (1*24*60*60);
				$backto = time() - $datediff;
				$tplData['periodString'] = 'In the last day';
				break;
			case 'week': //last week
				$datediff = (7*24*60*60);
				$backto = time() - $datediff;
				$tplData['periodString'] = 'In the last week';
				break;
			case 'month': //last month
				$datediff = (30*24*60*60);
				$backto = time()-$datediff;
				$tplData['periodString'] = 'In the last month';
				break;
			case 'year': //last year
				$datediff = (365*24*60*60);
				$backto = time()-$datediff;
				$tplData['periodString'] = 'In the last year';
				break;
			case 'allTime'://always
				$datediff = (50000*24*60*60);
				$backto = time() - $datediff;
				$tplData['periodString'] = 'all time';
				break;
			default:
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		//select the category to display
		$catId = $this->request->getGet('cat');
		if ($catId)
		{
			// Check if the cat exists
			$exists = $carCatModel->find($catId);
			if (!$exists) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

			$carCatId = $catId;
		}
		else
		{
			$first = $carCatModel->select('id')->findAll(1);
			$carCatId = $first[0]->id;
		}

		$tplData['carCatId'] = $carCatId;

		// Get cars categories
		$categoriesList = $carCatModel->select('id, name, count(carId) as totalCars')->groupBy('id')->findAll();
		$currCat = $carCatModel->find($carCatId);

		$carsCatList = $carCatModel->select('carId')->where('id', $carCatId)->findAll();

		$tplData['currCat'] = $currCat;
		$tplData['carCategoriesList'] = $categoriesList;

		$carsCatIds = [];
		foreach ($carsCatList as $car) $carsCatIds[] = $car->carId;

		//UGLY: there is some category that have no car assigned so create a fake $carsql for them
		//to prevent errors in the generated queries
		/*
		$carsql = '0';
		if($carsql == ''){
			$carsql = " B.car_id='NonExistentCarIdFindThisIfYouCan'";
		}
		*/

		/*
		################################
		## MOST ACTIVE USER OF THIS CATEGORY BASED ON LAPS RUN
		## WITH A CAR OF THIS CATEGORY
		################################
		*/

		$builder = $this->db->table('races r');
		$builder->select('r.user_id, COUNT(*) AS count, u.username');
		$builder->join('users u', 'u.id = r.user_id');
		$builder->where('UNIX_TIMESTAMP(r.timestamp) >', $backto);
		$builder->whereIn('r.car_id', $carsCatIds);
		$builder->groupBy('r.user_id');
		$builder->orderBy('count DESC');
	
		$tplData['users'] = [];
		$query = $builder->get();
		if ($query && $query->getNumRows() > 0) $tplData['users'] = $query->getResult();

		/*
		################################
		## SELECT THE BEST LAPS FOR EACH TRACK
		## WITH A CAR OFT HIS CATEGORY
		################################
		*/
		
		$tplData['mylaps'] = $bestLapsModel->getBests($backto, $carCatId, 0, 0);
		
		$tplData['tracks'] = [];
		$builder = $this->db->table('races');
		$builder->select('track_id, COUNT(*) AS count');
		$builder->where('UNIX_TIMESTAMP(timestamp) >', $backto);
		$builder->whereIn('car_id', $carsCatIds);
		$builder->groupBy('track_id');
		$builder->orderBy('count DESC');
	
		$query = $builder->get();

		if ($query && $query->getNumRows() > 0)
		{
			$tracks = $query->getResult();

			$tracksIds = [];
			foreach($tracks as $track) $tracksIds[] = $track->track_id;

			$tracksModel = new TracksModel();
			$tracksNames = [];
			$tracksNamesList = $tracksModel->select('id, name')->whereIn('id', $tracksIds)->findAll();

			foreach($tracksNamesList as $name) $tracksNames[$name->id] = $name->name;
			foreach($tracksIds as $id)
			{
				if (!key_exists($id, $tracksNames)) $tracksNames[$id] = "$id (Modded)";
			}

			$tplData['tracks'] = $tracks;
			$tplData['tracksNames'] = $tracksNames;
		}

		$tplData['cars'] = [];

		$builder = $this->db->table('races r');
		$builder->join('cars c', 'c.id = r.car_id');
		$builder->select('r.car_id, COUNT(r.car_id) as count, c.name');
		$builder->where('UNIX_TIMESTAMP(r.timestamp) >', $backto);
		$builder->whereIn('r.car_id', $carsCatIds);
		$builder->groupBy('r.car_id');
		$builder->orderBy('count DESC');

		$query = $builder->get();
		if ($query && $query->getNumRows() > 0) $tplData['cars'] = $query->getResult();

		echo get_header('Home');
		echo view('main', $tplData);
		echo get_footer();
	}

	public function error404()
	{
		return view('404');
	}
}
