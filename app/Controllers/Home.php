<?php

namespace App\Controllers;
use App\Models\UsersModel;

class Home extends BaseController
{
	protected $users;

	public function index()
	{
		$tplData = [];

		// select interested period
		if(array_key_exists('period', $_COOKIE))
		{
			$period = $_COOKIE['period'];
		}

		if(array_key_exists('period', $_GET))
		{
			setcookie( "period", $_GET['period'], time()+(60*60*24*30) );
			$period = $_GET['period'];
		}
		else
		{
			$period = 'year';
		}

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
		/*
		case 'date'://from this date
			$datediff=(7*24*60*60);
			$backto=time()-$datediff;
			$periodString ='From '.date('d-m-Y', $backto);
			break;
		*/
		case 'allTime'://always
			$datediff = (50000*24*60*60);
			$backto = time() - $datediff;
			$tplData['periodString'] = 'all time';
			break;
		}

		//select the category to display
		if (array_key_exists('cat', $_GET))
		{
			$carCatId = $_GET['cat'];
			$tplData['carCatId'] = $carCatId;
		}

		//reorder the cetegories by name
		$carCategories = getCarCats();
		$carCategoriesList = get_object_vars($carCategories);
		ksort($carCategoriesList);

		$tplData['carCategories'] = $carCategories;
		$tplData['carCategoriesList'] = $carCategoriesList;

		if (!isset($carCatId)) $carCatId = array_key_first($carCategoriesList);

		$carsql = '';
		foreach ($carCategories->$carCatId->cars as $car){
			$carsql.=" OR B.car_id='$car'";
		}

		$carsql = substr($carsql, 4); //remove the first " OR "

		//UGLY: there is some category that have no car assigned so create a fake $carsql for them
		//to prevent errors in the generated queries
		if($carsql == ''){
			$carsql = " B.car_id='NonExistentCarIdFindThisIfYouCan'";
		}

		/*
		################################
		## MOST ACTIVE USER OF THIS CATEGORY BASED ON LAPS RUN
		## WITH A CAR OF THIS CATEGORY
		################################
		*/

		$builder = $this->db->table('races B');
		$builder->select('B.user_id, COUNT(*) as count');
		$builder->where('UNIX_TIMESTAMP(B.timestamp) >', $backto);
		$builder->where("($carsql)");
		$builder->groupBy('B.user_id');
		$builder->orderBy('count DESC');
	
		$tplData['users'] = [];
		$query = $builder->get();
		if ($query || $query->getNumRows() > 0) $tplData['users'] = $query->getResult();

		/*
		################################
		## SELECT THE BEST LAPS FOR EACH TRACK
		## WITH A CAR OFT HIS CATEGORY
		################################
		*/

		$builder = $this->db->table('laps A');
		$builder->select('A.race_id, B.track_id, B.car_id, B.user_id, B.timestamp, A.wettness, min(A.laptime) as bestlap');
		$builder->join('races B', 'A.race_id = B.id');
		$builder->where('UNIX_TIMESTAMP(B.timestamp) >', $backto);
		$builder->where("($carsql)");
		$builder->groupBy(['B.track_id', 'A.wettness']);
	
		$tplData['mylaps'] = [];

		$query = $builder->get();
		if ($query || $query->getNumRows() > 0) $tplData['mylaps'] = $query->getResult();

		$query = "
			SELECT track_id, COUNT(*) as count
			FROM races B
				WHERE UNIX_TIMESTAMP(timestamp) > $backto
				AND ($carsql)
				GROUP BY B.track_id
				ORDER BY COUNT(*) DESC";

		$tplData['tracks'] = [];
		$builder = $this->db->table('races B');
		$builder->select('B.track_id, COUNT(*) as count');
		$builder->where('UNIX_TIMESTAMP(B.timestamp) >', $backto);
		$builder->where("($carsql)");
		$builder->groupBy('B.track_id');
		$builder->orderBy('count DESC');
	
		$query = $builder->get();
		if ($query || $query->getNumRows() > 0) $tplData['tracks'] = $query->getResult();

		$query="
			SELECT car_id, COUNT(*) as count
			FROM races B
			WHERE UNIX_TIMESTAMP(timestamp) > $backto
				AND ($carsql)
			GROUP BY B.car_id
			ORDER BY COUNT(*) DESC";

		$tplData['cars'] = [];

		$builder = $this->db->table('races B');
		$builder->select('car_id, COUNT(*) as count');
		$builder->where('UNIX_TIMESTAMP(B.timestamp) >', $backto);
		$builder->where("($carsql)");
		$builder->groupBy('B.car_id');
		$builder->orderBy('count DESC');

		$query = $builder->get();
		if ($query || $query->getNumRows() > 0) $tplData['cars'] = $query->getResult();

		echo get_header('Home');
		echo view('main', $tplData);
		echo get_footer();
	}
}
