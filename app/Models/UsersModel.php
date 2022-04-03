<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Models\CarsModel;

class UsersModel extends Model
{
	public $id;
	protected $db;
	protected $raceTypes;
	const HASH = PASSWORD_DEFAULT;
	const COST = 16;

	public function __construct($id = null)
	{
		$this->db = \Config\Database::connect();
		$this->id = $id;

		$this->raceTypes = new \stdClass;
		$this->raceTypes->practice = 0; 
		$this->raceTypes->qualify = 1;
		$this->raceTypes->race = 2;

		if ($this->id){
			$this->getFromDb();
		}
	}

	public function getUsers()
	{
		$query = $this->db->table('users')->get();
		$users = [];

		if ($query && $query->getNumRows() > 0) $users = $query->getResult();
		return $users;
	}

	public function getUser($username)
	{
		$builder = $this->db->table('users');
		$builder->select('id');
		$builder->where('username', $username);
		$query = $builder->get(1);

		if ($query && $query->getNumRows() == 1)
		{
			return $query->getRow()->id;
		}
		return false;
	}

	/**
	 * Check if the user and/or email is already in use.
	 * @param string $username The username to check.
	 * @param string $email The email to check.
	 * @return Boolean False if the username and email are not in use
	 */
	public function compUser($username, $email)
	{
		$builder = $this->db->table('users');
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
		return "<a href='" . base_url() . "/user/{$this->username}'>$text</a>";
	}

	public function getSmallFlagImg()
	{		
		return "<img src='" . base_url() . "/img/flags/flags_small/{$this->flag}' alt='{$this->nation}'>";
	}

	public function getMediumFlagImg()
	{
		return "<img src='" . base_url() . "/img/flags/flags_medium/{$this->flag}' alt='{$this->nation}'>";
	}

	public function getImgFile()
	{
		return "<img class='avatar' src='" . base_url() . "/img/users/{$this->img}' alt='{$this->username}'>";
	}

	public function getRaceSessions()
	{
		$builder = $this->db->table('races');
		$builder->where('user_id', $this->id);
		$builder->orderBy('id DESC');
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
			$data->car = new CarsModel(getCar($query->getRow()->car_id));
			$data->total = $query->getRow()->count;
		}
		return $data;
	}

	public function getMostUsedTrack()
	{
		$builder = $this->db->table('races');
		$builder->select('track_id, COUNT(*) as count');
		$builder->where([
			'user_id'	=> $this->id
		]);
		$builder->orderBy('count', 'desc');
		$builder->groupBy('track_id');

		$query = $builder->get(1);

		$data = new \stdClass;
		if ($query && $query->getNumRows() > 0)
		{
			$data->track = new TracksModel(getTrack($query->getRow()->track_id));
			$data->total = $query->getRow()->total;
		}
		return $data;
	}

	public function getTimeOnTracks()
	{
		$builder = $this->db->table('laps A');
		$builder->selectSum('A.laptime');
		$builder->join('races B', 'ON A.race_id = B.id');
		$builder->where([
			'B.user_id'	=> $this->id
		]);
		//$builder->orderBy('count', 'desc');

		$query = $builder->get(1);

		if ($query && $query->getNumRows() > 0) return $query->getRow()->laptime;
		return 0;
	}

	public function getTimeOnRace()
	{
		$builder = $this->db->table('laps A');
		$builder->selectSum('A.laptime');
		$builder->join('races B', 'ON A.race_id = B.id');
		$builder->where([
			'B.user_id'	=> $this->id,
			'B.type'	=> $this->raceTypes->race
		]);
		//$builder->orderBy('count', 'desc');

		$query = $builder->get(1);

		if ($query && $query->getNumRows() > 0) return $query->getRow()->laptime;
		return 0;
	}

	public function getTimePractice()
	{
		$builder = $this->db->table('laps A');
		$builder->selectSum('A.laptime');
		$builder->join('races B', 'ON A.race_id = B.id');
		$builder->where([
			'B.user_id'	=> $this->id,
			'B.type'	=> $this->raceTypes->practice
		]);
		//$builder->orderBy('count', 'desc');

		$query = $builder->get(1);

		if ($query && $query->getNumRows() > 0) return $query->getRow()->laptime;
		return 0;
	}

	public function getTimeQualify()
	{
		$builder = $this->db->table('laps A');
		$builder->selectSum('A.laptime');
		$builder->join('races B', 'ON A.race_id = B.id');
		$builder->where([
			'B.user_id'	=> $this->id,
			'B.type'	=> $this->raceTypes->qualify
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
			'username'	=> $data['username'],
		];

		// First verify if the user or email
		$sql = $this->db->table('users');
		$sql->where('username', $user['username']);
		$sql->orWhere('email', $user['email']);
		$query = $sql->get(1);
		
		if ($query && $query->getNumRows() == 1){
			$response['msg'] = 'Username and/or email are registered';
		}
		else
		{
			// Encrypt the password
			$user['password'] = password_hash($data['password'], self::HASH, [self::COST]);
			unset($data);
			// Insert data
			$sql->insert($user);
			$error = $this->db->error();
			if ($error['code'] != 0)
			{
				$resp['msg'] = 'An error ocurred on insert the new user to the database';
			}
			else
			{
				$id = $this->db->insertID();
				$sql->resetQuery();
				// Move the file to it's new home
				$filename =  $user['username'] . '.' . $image->getExtension();
				$image->move(FCPATH . '/img/users/', $filename);
				$sql->where('id', $id)->update(['img' => $filename]);
				$response['ok'] = true;
			}
		}

		return $response;
	}
}