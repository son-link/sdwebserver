<?php

namespace App\Controllers;
use App\Models\UsersModel;

class Users extends BaseController
{
    protected $users;

	public function __construct()
	{
		$this->users = new UsersModel();
	}

    public function index()
    {
		$users = $this->users->getUsers();
		$tplData = [];
		$tplData['users'] = $users;
		echo get_header('Users');
		echo view('users', $tplData);
		echo get_footer();
    }

	public function user($username)
	{
		//$this->cachePage(3600);
		$userid = $this->users->getUser($username);
		$user = new UsersModel($userid);
		unset($user->addUser);
		$userraces = $user->getRaces();
		$raceswon = $user->getWon();
		$racespodiums = $user->getPodiums();
		$racesretired = $user->getUnfinisced();
		$practices = $user->getPractices();
		$qualifies = $user->getQualifies();
		$tplData = [
			'raceSessions'			=> $user->getRaceSessions(),
			'userRaces'				=> $userraces,
			'racesWon'				=> $raceswon,
			'raceswonpercent'		=> percentStr($raceswon, count($userraces)),
			'racespodiums'			=> $racespodiums,
			'racespodiumpercent'	=> percentStr($racespodiums->totalPodiums, count($userraces)),
			'practicescount'		=> $practices,
			'qualifiescount'		=> $qualifies,
			'racesretired'			=> $racesretired,
			'racesretiredpercent'	=> percentStr($racesretired, count($userraces)),
			'mostusedcar'			=> $user->getMostUsedCar(),
			'mostusedtrack'			=> $user->getMostUsedTrack(),
			'timeontrackPractice'	=> $user->getTimePractice(),
			'timeontrackQualify'	=> $user->getTimeQualify(),
			'timeontrackRace'		=> $user->getTimeOnRace(),
			'timeontrack'			=> $user->getTimeOnTracks(),
			'user'					=> $user
		];
		echo get_header("User: $username");
		echo view('user', $tplData);
		echo get_footer();
	}
}