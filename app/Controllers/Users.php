<?php

namespace App\Controllers;
use App\Models\UsersModel;

class Users extends BaseController
{
    protected object $usersModel;

	public function __construct()
	{
		$this->usersModel = new UsersModel();
	}

    public function index()
    {
		$users = $this->usersModel->findAll();
		$tplData = [];
		$tplData['users'] = $users;
		echo get_header('Users');
		echo view('users', $tplData);
		echo get_footer();
    }

	public function user($username)
	{
		//$this->cachePage(3600);
		$user = $this->usersModel->getUser($username);
		$userraces = $this->usersModel->getRaces();
		$raceswon = $this->usersModel->getWon();
		$racespodiums = $this->usersModel->getPodiums();
		$racesretired = $this->usersModel->getUnfinisced();
		$practices = $this->usersModel->getPractices();
		$qualifies = $this->usersModel->getQualifies();
		$tplData = [
			'raceSessions'			=> $this->usersModel->getRaceSessions(),
			'userRaces'				=> $userraces,
			'racesWon'				=> $raceswon,
			'raceswonpercent'		=> percentStr($raceswon, count($userraces)),
			'racespodiums'			=> $racespodiums,
			'racespodiumpercent'	=> percentStr($racespodiums->totalPodiums, count($userraces)),
			'practicescount'		=> $practices,
			'qualifiescount'		=> $qualifies,
			'racesretired'			=> $racesretired,
			'racesretiredpercent'	=> percentStr($racesretired, count($userraces)),
			'mostusedcar'			=> $this->usersModel->getMostUsedCar(),
			'mostusedtrack'			=> $this->usersModel->getMostUsedTrack(),
			'timeontrackPractice'	=> $this->usersModel->getTimePractice(),
			'timeontrackQualify'	=> $this->usersModel->getTimeQualify(),
			'timeontrackRace'		=> $this->usersModel->getTimeOnRace(),
			'timeontrack'			=> $this->usersModel->getTimeOnTracks(),
			'user'					=> $user
		];
		log_message('debug', json_encode($tplData['raceSessions']));
		echo get_header("User: $username");
		echo view('user', $tplData);
		echo get_footer();
	}

	public function login()
	{
		echo get_header("Log In");
		echo view('login');
		echo get_footer(['dashboard.js']);
	}
}