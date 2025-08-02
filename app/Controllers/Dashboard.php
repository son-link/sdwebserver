<?php

namespace App\Controllers;
use App\Models\UsersModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\URI;

class Dashboard extends BaseController
{
    protected $usersModel;
	use ResponseTrait;

	const HASH = PASSWORD_DEFAULT;
	const COST = 16;

	public function __construct()
	{
		$this->usersModel = new UsersModel();
		$uri = new URI();
		$session = session();

		/*
		if ($uri->getSegment(1) != 'login' && !$session->has('logged_in'))
		{
			header('Location: '.base_url().'/login');
			die();
		}
		*/
	}

    public function index()
    {
		$tplData = [
			'total_laps'	=> 0,
			'total_races'	=> 0
		];

		// Get count of all races and laps of user
		$builder = $this->db->table('races r');
		$builder->select('COUNT(r.id) AS total_races');
		$builder->select('SUM(
				(SELECT COUNT(*) FROM laps l
				WHERE l.race_id = r.id)
			) AS total_laps');
		$builder->where('r.user_id', $this->session->userid);
		$query = $builder->get();
		
		if ($query)
		{
			$data = $query->getRow();
			$tplData = array_merge($tplData, (array) $data);
		}

		// Get the 3 most used cars
		$builder->resetQuery();
		$builder->select('COUNT(c.id) AS total_used, r.car_id, c.name AS car_name');
		$builder->join('cars c', 'c.id = r.car_id');
		$builder->where('r.user_id', $this->session->userid);
		$builder->groupBy('c.id');
		$builder->orderBy('total_used DESC');
		$query = $builder->get(3);

		if ($query) $tplData['most_used_cars'] = $query->getResult();

		// Get the 3 most used tracks
		$builder->resetQuery();
		$builder->select('COUNT(t.id) AS total_used, r.track_id, t.name AS track_name');
		$builder->select('SUM(
				(SELECT COUNT(*) FROM laps l
				WHERE l.race_id = r.id)
			) AS total_laps');
		$builder->join('tracks t', 't.id = r.track_id');
		$builder->where('r.user_id', $this->session->userid);
		$builder->groupBy('t.id');
		$builder->orderBy('total_used DESC');
		$query = $builder->get(3);

		if ($query) $tplData['most_used_tracks'] = $query->getResult();

		echo get_header('Dashboard', [], true);
		echo view('dashboard/main.php', $tplData);
		echo get_footer(['dashboard.js']);
    }

	public function login()
	{
		$data = $this->request->getVar();
		$compUser = $this->usersModel->login($data);
		return $this->respond(['ok' => $compUser]);
	}

	public function user()
	{
		$user = $this->usersModel->find($this->session->userid);

		echo get_header("My User", [], true);
		echo view('dashboard/user', ['user' => $user]);
		echo get_footer(['dashboard.js']);
	}

	public function users() {
		$users = $this->usersModel->findAll();
		return $this->respond($users);
	}

	public function logout()
	{
		$this->session->destroy();
		return redirect()->to('login');
	}

	public function updateUser()
	{
		$data = $this->request->getVar();

		$response = [
			'ok'	=> true,
			'msg'	=> ''
		];

		$update = $this->usersModel->update($this->session->userid, $data);

		if (!$update) $this->respond(['ok' => false, 'msg' => 'Error on update data']);
		
		$file = $this->request->getFile('imginput');
		if ($file && $file->getName())
		{
			// Verify is the file is correct and not, for example, a .exe renamed to .jpg
			$ext = strtolower($file->guessExtension());

			if ($ext != strtolower($file->getExtension())) $response['msg'] = 'The image is not valid';
			else
			{

				// First, get the current avatar filnename, and delete if the extension is different
				$userData = $this->usersModel->getUser($this->session->username);
				$avatar = FCPATH . '/img/users/'. $userData->img;
				$oldAvatarFile = new \CodeIgniter\Files\File($avatar);
				if ($oldAvatarFile->getExtension() != $ext && file_exists($avatar)) unlink($avatar);

				$filename = "{$this->session->username}.$ext";
				$move = $file->move(FCPATH . '/img/users/', $filename, true);
				if ($move) $update = $this->usersModel->update($this->session->userid, (object) ['img' => $filename]);
			}
    	}

		return $this->respond($response);
	}

	public function changePasswd()
	{
		$data = $this->request->getPost();

		$response = [
			'ok'	=> false,
			'msg'	=> ''
		];

		if (!$data)
		{
			$response['msg'] = 'Not data send';
			return $this->respond($response);
		}

		$query = $this->usersModel->select('password')->where('id', $this->session->userid)->get(1);

		if (!$query) return $this->respond($response);

		$user = $query->getRow();
		if (!password_verify($data['cur_password'], $user->password))
		{
			$response['msg'] = 'The current password is not correct';
			return $this->respond($response);
		}

		$password = password_hash($data['password'], self::HASH, [self::COST]);

		$update = $this->usersModel->update($this->session->userid, (object) ['password' => $password]);

		$response['ok'] = $update;
		return $this->respond($response);
	}
}