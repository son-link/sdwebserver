<?php

namespace App\Controllers;
use App\Models\UsersModel;
use App\Models\CarsModel;
use App\Models\TracksModel;

class Races extends BaseController
{
    protected object $users;
	protected object $carsModel;
	protected object $tracksModel;

	public function __construct()
	{
		$this->users = new UsersModel();
		$this->carsModel = new CarsModel();
		$this->tracksModel = new TracksModel();
	}

    public function index($race)
    {
		//$this->cachePage(360);
		$builder = $this->db->table('races r');
		$builder->join('cars c', 'c.id = r.car_id');
		$builder->join('tracks t', 't.id = r.track_id');
		$builder->join('users u', 'u.id = r.user_id');
		$builder->select('r.id, r.type, r.timestamp, r.car_id, c.name AS car_name, c.img as car_img, r.track_id, t.name AS track_name, t.img AS track_img, u.username');
		$builder->where('r.id', $race);
		$query = $builder->get(1);

		$tplData = [];

		if (!$query || $query->getNumRows() == 0) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		$tplData['race'] =  $query->getRow();
		$builder = $this->db->table('laps');
		$builder->where('race_id', $tplData['race']->id);
		$query = $builder->get();
			
		$tplData['race']->n_laps = 0;
		if ($query && $query->getNumRows() > 0)
		{
			$tplData['laps'] = json_encode($query->getResult());
			$tplData['race']->n_laps = $query->getNumRows();
		}

		echo get_header('Races');
		echo view('race', $tplData);
		echo get_footer(['chart.min.js']);
    }
}