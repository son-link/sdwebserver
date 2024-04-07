<?php

namespace App\Controllers;
use App\Models\UsersModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\URI;

class Dashboard extends BaseController
{
    protected $usersModel;
	use ResponseTrait;

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
		echo get_footer();
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
		echo get_footer();
	}

	public function users() {
		$users = $this->usersModel->findAll();
		return $this->respond($users);
	}
}