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
		//$users = $this->users->getUsers();
		$tplData = [];
		//$tplData['users'] = $users;
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
		//$userid = $this->session->userid;
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