<?php

namespace App\Controllers;
use App\Models\CarCatsModel;
use App\Models\ChampionshipsBestLapsModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\LapModel;

class Home extends BaseController
{
	use ResponseTrait;

	public function index()
	{
		$tplData = [];
		$carCatModel = new CarCatsModel;

		// select interested period
		if(array_key_exists('period', $_COOKIE)) $period = $_COOKIE['period'];

		if($this->request->getGet('period'))
		{
			$period = $this->request->getGet('period');
			setcookie( "period", $period, time()+(60*60*24*30) );
		}
		else $period = 'today';

		$tplData['period'] = $period;

		switch ($period)
		{
			case 'today': // Today
				$tplData['periodString'] = 'In the last day';
				break;
			case 'week': // Last week
				$tplData['periodString'] = 'In the last week';
				break;
			case 'month': // Last month
				$tplData['periodString'] = 'In the last month';
				break;
			case 'year': // Last year
				$tplData['periodString'] = 'In the last year';
				break;
			case 'allTime': // Always
				$tplData['periodString'] = 'all time';
				break;
			default:
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		// Select the category to display
		$catId = $this->request->getGet('cat');

		if ($catId)
		{
			// Check if the car's category exists
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

		$tplData['currCat'] = $currCat;
		$tplData['carCategoriesList'] = $categoriesList;

		echo get_header('Home', ['minidt.css']);
		echo view('main', $tplData);
		echo get_footer(['minidt.js', 'home_tables.js']);
	}

	public function championships()
	{
		$championships = [
			'weeklyData'	=> [],
			'previous'		=> [],
			'currentRaces'	=> []
		];

		// Get the current championship
		$query = $this->db->query('SELECT * FROM championship WHERE NOW() BETWEEN date_start and date_end');
		if ($query && $query->getNumRows() > 0)
		{
			$data = $query->getRow();

      		$championships['weeklyData'] = $data;

			$query = $this->db->query('SELECT *, DATE_FORMAT(date_start, "%d/%m/%Y") as date_start_conv, DATE_FORMAT(date_end, "%d/%m/%Y") AS date_end_conv FROM championship WHERE id < ? ORDER BY date_end DESC', [$data->id]);
			if ($query && $query->getNumRows() > 0) $championships['previous'] = $query->getResult();

			$lapModel = new LapModel(); // Instantiate the model
			$championships['currentRaces'] = $lapModel->getFastestLapWindows();

			echo get_header('Championships', ['minidt.css']);
			echo view('championships', $championships);
			echo get_footer(['minidt.js', 'championships.js']);
		} else {
			$query = $this->db->query('SELECT *, DATE_FORMAT(date_start, "%d/%m/%Y") as date_start_conv, DATE_FORMAT(date_end, "%d/%m/%Y") AS date_end_conv FROM championship ORDER BY date_end DESC');
			if ($query && $query->getNumRows() > 0) $championships['previous'] = $query->getResult();

			echo get_header('Championships', ['minidt.css']);
			echo view('championships', $championships);
			echo get_footer(['minidt.js', 'championships.js']);
		}
	}

	public function error404()
	{
		return view('404');
	}
}
