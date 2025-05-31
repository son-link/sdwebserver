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
			'current'	=> [],
			'previous'	=> []
		];

		// Get the current championship
		$query = $this->db->query('SELECT * FROM championship WHERE NOW() BETWEEN date_start and date_end');
		if ($query && $query->getNumRows() > 0)
		{
			$data = $query->getRow();
			$chblModel = new ChampionshipsBestLapsModel;
			/*
			$date_start = $this->db->escape($data->date_start);
			$date_end = $this->db->escape($data->date_end);
			
			// Los datos de las vueltas rápidas de cada jugador en cada campeonato
			// se guardan en una tabla aparte
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

			if ($query && $query->getNumRows() > 0) $championships['current'] = $query->getResult();

			*/
			$championships['current'] = $chblModel->getChampionshipData($data->date_start, $data->date_end, $data->track_id, $data->car_cat, $data->wettness);

			$query = $this->db->query('SELECT *, DATE_FORMAT(date_start, "%d/%m/%Y") as date_start_conv, DATE_FORMAT(date_end, "%d/%m/%Y") AS date_end_conv FROM championship WHERE id < ? ORDER BY date_end DESC', [$data->id]);
			if ($query && $query->getNumRows() > 0) $championships['previous'] = $query->getResult();

			$lapModel = new LapModel(); // Instantiate the model
			$championships['current_races'] = $lapModel->getFastestLapWindows();

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
