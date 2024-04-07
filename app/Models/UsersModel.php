<?php
namespace App\Models;

use App\Models\BaseModel;
use App\Models\CarsModel;
use App\Models\TracksModel;

class UsersModel extends BaseModel
{
	protected $table      = 'users';
	protected $allowedFields = ['username', 'email', 'password',  'img', 'nation', 'registrationdate',
		'sessionid', 'sessionip', 'sessiontimestamp', 'level'
	];

	public $id;

	protected $raceTypes;

	private $carsModel;

	private $tracksModel;

	private $session;

	private $user;

	const HASH = PASSWORD_DEFAULT;
	const COST = 16;

	public function initialize()
	{
		$this->session = \Config\Services::session();
		$this->carsModel = new CarsModel();
		$this->tracksModel = new TracksModel();

		$this->raceTypes = new \stdClass;
		$this->raceTypes->practice = 0; 
		$this->raceTypes->qualify = 1;
		$this->raceTypes->race = 2;
	}

	/*
	public function initialize()
	{
		$this->session = session();
		$this->carsModel = new CarsModel;
		$this->tracksModel = new TracksModel;

		$this->raceTypes = new \stdClass;
		$this->raceTypes->practice = 0; 
		$this->raceTypes->qualify = 1;
		$this->raceTypes->race = 2;
	}

	public function getUsers()
	{
		$query = $this->db->table('users')->get();
		$users = [];

		if ($query && $query->getNumRows() > 0) $users = $query->getResult();
		return $users;
	}
	*/
	public function getUser($username)
	{
		$builder = $this->builder();
		$builder->select('id, email, username, nation, img');
		$builder->where('username', $username);
		$query = $builder->get(1);

		if ($query && $query->getNumRows() == 1)
		{
			$data = $query->getRow();
			$this->id = $data->id;
			$data->flag = str_replace(' ', '_', $data->nation) . '.png';
			return $data;
		} 

		return false;
	}

	/**
	 * Check if the user and/or email is already in use.
	 * @param string $username The username to check.
	 * @param string $email The email to check.
	 * @return boolean False if the username and email are not in use
	 */
	public function compUser($username, $email)
	{
		$builder = $this->builder();
		$builder->select('id');
		$builder->where('username', $username);
		$builder->orWhere('email', $email);
		$query = $builder->get(1);

		if ($query && $query->getNumRows() == 1)
		{
			return true;
		}
		
		return false;
	}

	/*
    private function import($properties)
	{    
		foreach($properties as $key => $value){
			$this->{$key} = $value;
		}

		// Add the flag filename
		$this->flag = str_replace(' ', '_', $this->nation) . '.png';
    }
	
	public function getFromDb()
	{
		$builder = $this->db->table('users');
		$builder->where('id', $this->id);
		$query = $builder->get(1);

		if ($query && $query->getNumRows() == 1)
		{
			$this->import($query->getRowArray());
		}
		else
		{
			//no valid result found! create a fake user
			$this->username = '<i>guest</i>';
			$this->nation = '';
		}
	}

	public function getLink($text='')
	{
		if ($text == '') $text=$this->username;
		return "<a href='" . base_url() . "user/{$this->username}'>$text</a>";
	}

	public function getSmallFlagImg()
	{		
		return "<img src='" . base_url() . "img/flags/flags_small/{$this->flag}' alt='{$this->nation}'>";
	}

	public function getMediumFlagImg()
	{
		return "<img src='" . base_url() . "img/flags/flags_medium/{$this->flag}' alt='{$this->nation}'>";
	}

	public function getImgFile()
	{
		return "<img class='avatar' src='" . base_url() . "img/users/{$this->img}' alt='{$this->username}'>";
	}

	*/
	public function getRaceSessions()
	{
		$builder = $this->db->table('races r');
		$builder->select('r.id, r.user_skill, r.track_id, r.car_id, r.startposition, r.endposition');
		$builder->select('r.sdversion, r.timestamp, r.type, t.name as track_name, c.name as car_name');
		$builder->join('tracks t', 't.id = r.track_id');
		$builder->join('cars c', 'c.id = r.car_id');
		$builder->where('r.user_id', $this->id);
		$builder->orderBy('r.id DESC');
		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) return $query->getResult();
		return [];
	}

	public function getRaces()
	{
		$builder = $this->db->table('races');
		$builder->where([
			'user_id'	=> $this->id,
			'type'		=> $this->raceTypes->race
		]);
		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) return $query->getResult();
		return [];
	}

	public function getWon()
	{
		$builder = $this->db->table('races');
		$builder->select('COUNT(*) as total');
		$builder->where([
			'user_id'		=> $this->id,
			'type'			=> $this->raceTypes->race,
			'endposition'	=> 1
		]);

		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) return $query->getRow()->total;
		return 0;
	}

	public function getPodiums()
	{
		$builder = $this->db->table('races');
		$builder->select('SUM(IF(endposition = 1, 1, 0)) as totalGold');
		$builder->select('SUM(IF(endposition = 2, 1, 0)) as totalSilver');
		$builder->select('SUM(IF(endposition = 3, 1, 0)) as totalBronze');
		$builder->select('COUNT(*) as totalPodiums');
		$builder->where([
			'user_id'			=> $this->id,
			'type'				=> $this->raceTypes->race,
			'endposition >'		=> 0,
			'endposition <='	=> 3
		]);
		
		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) return $query->getRow();
		return [
			'totalPodiums'	=> 0,
			'totalGold'		=> 0,
			'totalSilver'	=> 0,
			'totalBronze'	=> 0,
		];
	}

	public function getPractices()
	{
		$builder = $this->db->table('races');
		$builder->select('COUNT(*) as total');
		$builder->where([
			'user_id'	=> $this->id,
			'type'		=> $this->raceTypes->practice,
		]);

		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) return $query->getRow()->total;
		return 0;
	}

	public function getQualifies()
	{
		$builder = $this->db->table('races');
		$builder->select('COUNT(*) as total');
		$builder->where([
			'user_id'	=> $this->id,
			'type'		=> $this->raceTypes->qualify,
		]);

		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) return $query->getRow()->total;
		return 0;
	}

	public function getUnfinisced()
	{
		$builder = $this->db->table('races');
		$builder->select('COUNT(*) as total');
		$builder->where([
			'user_id'		=> $this->id,
			'type'			=> $this->raceTypes->race,
		]);
		$builder->where('(endposition = 0 OR endposition IS NULL)');
		$query = $builder->get(1);

		if ($query && $query->getNumRows() > 0) return $query->getRow()->total;
		return 0;
	}

	public function getMostUsedCar()
	{
		$builder = $this->db->table('races');
		$builder->select('car_id, COUNT(*) as count');
		$builder->where([
			'user_id'	=> $this->id,
		]);
		$builder->orderBy('count', 'desc');
		$builder->groupBy('car_id');

		$query = $builder->get(1);

		$data = new \stdClass;
		if ($query && $query->getNumRows() > 0)
		{
			$data->car = $this->carsModel->find($query->getRow()->car_id);
			$data->total = $query->getRow()->count;
		}
		return $data;
	}

	public function getMostUsedTrack()
	{
		$builder = $this->db->table('races');
		$builder->select('track_id, COUNT(*) AS total');
		$builder->where([
			'user_id'	=> $this->id
		]);
		$builder->orderBy('total', 'desc');
		$builder->groupBy('track_id');

		$query = $builder->get(1);

		$data = new \stdClass;
		
		if ($query && $query->getNumRows() > 0)
		{
			$result = $query->getRow();
			log_message('debug', json_encode($result));
			$data->track = $this->tracksModel->find($result->track_id);
			$data->total = $result->total;
		}
		return $data;
	}

	public function getTimeOnTracks()
	{
		$builder = $this->db->table('laps l');
		$builder->selectSum('l.laptime');
		$builder->join('races r', 'l.race_id = r.id');
		$builder->where([
			'r.user_id'	=> $this->id
		]);
		//$builder->orderBy('count', 'desc');

		$query = $builder->get(1);

		if ($query && $query->getNumRows() > 0) return $query->getRow()->laptime;
		
		return 0;
	}

	public function getTimeOnRace()
	{
		$builder = $this->db->table('laps l');
		$builder->selectSum('l.laptime');
		$builder->join('races r', 'l.race_id = r.id');
		$builder->where([
			'r.user_id'	=> $this->id,
			'r.type'	=> $this->raceTypes->race
		]);
		//$builder->orderBy('count', 'desc');

		$query = $builder->get(1);

		if ($query && $query->getNumRows() > 0) return $query->getRow()->laptime;
		return 0;
	}

	public function getTimePractice()
	{
		$builder = $this->db->table('laps l');
		$builder->selectSum('l.laptime');
		$builder->join('races r', 'l.race_id = r.id');
		$builder->where([
			'r.user_id'	=> $this->id,
			'r.type'	=> $this->raceTypes->practice
		]);
		//$builder->orderBy('count', 'desc');

		$query = $builder->get(1);

		if ($query && $query->getNumRows() > 0) return $query->getRow()->laptime;
		return 0;
	}

	public function getTimeQualify()
	{
		$builder = $this->db->table('laps l');
		$builder->selectSum('l.laptime');
		$builder->join('races r', 'l.race_id = r.id');
		$builder->where([
			'r.user_id'	=> $this->id,
			'r.type'	=> $this->raceTypes->qualify
		]);
		//$builder->orderBy('count', 'desc');

		$query = $builder->get(1);

		if ($query && $query->getNumRows() > 0) return $query->getRow()->laptime;
		return 0;
	}

	/**
	 * Add new to the database
	 */
	public function addUser($data, $image)
	{
		$response = [
			'ok'	=> false,
			'msg'	=> ''
		];

		$user = [
			'username'	=> $data['username'],
			'email'		=> $data['email'],
			'nation'	=> $data['nation'],
		];

		// First verify if the user or email
		$builder = $this->db->table('users');
		$builder->where('username', $user['username']);
		$builder->orWhere('email', $user['email']);
		$query = $builder->get(1);
		
		if ($query && $query->getNumRows() == 1){
			$response['msg'] = 'Username and/or email are registered';
		}
		else
		{
			// Encrypt the password
			$user['password'] = password_hash($data['password'], self::HASH, [self::COST]);
			unset($data);
			// Insert data
			$this->insert($user);
			$error = $this->db->error();

			if ($error['code'] != 0) $response['msg'] = 'An error ocurred on insert the new user to the database';
			else
			{
				$id = $this->db->insertID();
				//$builder->resetQuery();
				// Move the file to it's new home
				$filename =  $user['username'] . '.' . $image->getExtension();
				$image->move(FCPATH . '/img/users/', $filename);
				$this->where('id', $id)->update(['img' => $filename]);
				$response['ok'] = true;
			}
		}

		return $response;
	}

	public function login($data)
	{
		$builder = $this->builder();
		$builder->select('id, password, level, username');
		$builder->where('username', $data['username']);
		$query = $builder->get(1);
		if ($query && $query->getNumRows() == 1)
		{
			$user = $query->getRow();
			if (password_verify($data['passwd'], $user->password))
			{
				$sessiondata = [
					'userid'	=> $user->id,
					'userlevel'	=> $user->level,
					'username'	=> $user->username,
					'logged_in'	=> true
				];

				$this->session->set($sessiondata);
				return true;
			}

			return false;
		}
		
		return false;
	}
}