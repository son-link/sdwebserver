<?php

namespace App\Controllers;
use App\Models\UsersModel;
use App\Models\CarsModel;
use App\Models\TracksModel;

class Races extends BaseController
{
    protected $users;

	public function __construct()
	{
		$this->users = new UsersModel();
	}

    public function index($race)
    {
		//$this->cachePage(360);
		$builder = $this->db->table('races');
		$builder->where('id', $race);
		$query = $builder->get(1);

		$tplData = [];

		if ($query && $query->getNumRows() == 1)
		{
			$tplData['race'] =  $query->getRow();
			$builder = $this->db->table('laps');
			$builder->where('race_id', $tplData['race']->id);
			$query = $builder->get();

			if ($query && $query->getNumRows() > 0)
			{
				$tplData['laps'] = json_encode($query->getResult());
				$tplData['user'] = new UsersModel($tplData['race']->user_id);
				$tplData['car'] = new CarsModel(getCar($tplData['race']->car_id));
				$tplData['track'] = new TracksModel(getTrack($tplData['race']->track_id));
			}
		}
		echo get_header('Races');
		echo view('race', $tplData);
		echo get_footer(['chart.min.js']);
    }
}