<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\BestLapsModel;

class Webserver extends BaseController
{
	use ResponseTrait;
	protected $xmlreply;
	protected $reply;
	const HASH = PASSWORD_DEFAULT;
	const COST = 16;

	private BestLapsModel $bestLapsModel;
    public function index()
    {
		// Log connection
		$this->bestLapsModel = new BestLapsModel;
		$data = $this->request->getPost('data');

		if (!$data) return $this->failValidationErrors('No data received');

        $xml = xmlObj($data);

		$webserverversion= 1;
		$string='<?xml version="1.0" encoding="UTF-8"?>
		<params>
		</params>';

		$this->xmlreply = new \SimpleXMLElement($string);
		$temp = $this->xmlreply->xpath('/params');
		$params = $temp[0]; //size"[@label='Large']");

		$params->addAttribute('name','webServerReply');

		$content = $params->addChild('section');
		$content->addAttribute('name', 'content');

		$requestid = $content->addChild('attnum');
		$requestid->addAttribute('name','request_id');
		$requestid->addAttribute('val',$xml->request_id);

		$version = $content->addChild('attnum');
		$version->addAttribute('name','webServerVersion');
		$version->addAttribute('val',$webserverversion);

		$date = $content->addChild('attnum');
		$date->addAttribute('name','date');
		$date->addAttribute('val',time());

		$error = $content->addChild('attstr');
		$error->addAttribute('name','error');
		$error->addAttribute('val','this is an error');

		$this->reply = $content->addChild('section');
		$this->reply->addAttribute('name','reply');

		//process the request
		foreach ($xml->request as $requestype => $requestdata)
		{
			if (property_exists($requestdata, 'id'))
			{
				//there is already an id assigned, update the old data into the database
				
				$myDb = $this->db->table($requestype);
				$update = $myDb->where('id', $requestdata->id)->update((array) $requestdata);

				//xml
				$races = $this->reply->addChild('section');
				$races->addAttribute('name','races');

				$id = $races->addChild('attnum');
				$id->addAttribute('name', 'id');
				$id->addAttribute('val', $requestdata->id);

				//xml messages
				$messagges = $this->reply->addChild('section');
				$messagges->addAttribute('name','messages');

				$number = $messagges->addChild('attnum');
				$number->addAttribute('name','number');
				$number->addAttribute('val', 1);

				$msg0 = $messagges->addChild('attstr');
				$msg0->addAttribute('name','message0');
				$msg0->addAttribute('val',"Final race position registered\nfrom the server");
			}
			else
			{
				//this is new data, insert it into the database
				switch ($requestype)
				{
					case 'races':
						$this->races($requestdata);
					break;
					case 'laps':
						$this->laps($requestdata);
					break;
					case 'login':
						$this->login($requestdata);
					break;
				}
			}
		}

		//output the xml as string
		$domxml = new \DOMDocument('1.0');
		$domxml->preserveWhiteSpace = false;
		$domxml->formatOutput = true;
		$domxml->loadXML($this->xmlreply->asXML());
		return $this->response->setXML($domxml->saveXML());
    }

	private function races($requestdata)
	{
		$myDb = $this->db->table('races');
		$myDb->insert((array) $requestdata);

		//xml
		$races = $this->reply->addChild('section');
		$races->addAttribute('name','races');

		$id = $races->addChild('attnum');
		$id->addAttribute('name', 'id');
		$id->addAttribute('val', $this->db->insertID());

		// Select the best lap for this user's car/track combo

		$myDb = $this->db->table('laps A');
		$myDb->select('min(A.laptime) as bestlap');
		$myDb->join('races B', 'A.race_id = B.id');
		$myDb->where([
			'B.car_id'		=> $requestdata->car_id,
			'B.track_id'	=> $requestdata->track_id,
			'B.user_id'		=> $requestdata->user_id,
		]);
		
		$results = $myDb->get(1);
		if (!$results || $results->getNumRows() == 0) return;
		
		$bestlap = $results->getRow();

		//xml messages
		$messagges = $this->reply->addChild('section');
		$messagges->addAttribute('name','messages');

		$number = $messagges->addChild('attnum');
		$number->addAttribute('name','number');
		$number->addAttribute('val', 1);

		$msg0 = $messagges->addChild('attstr');
		$msg0->addAttribute('name','message0');
		$msg0->addAttribute('val',"Race registered\nfrom the server\n-\nYour best lap with this\ncar/track combo is\n".formatLaptime($bestlap->bestlap));
	}

	private function laps($requestdata)
	{
		log_message('debug', json_encode($requestdata));

		// Get track and card category
		$builder = $this->db->table('races r');
		$builder->join('cars_cats cc', 'cc.carID = r.car_id');
		$builder->select('r.track_id, cc.id AS car_cat');
		$builder->where('r.id', $requestdata->race_id);
		$query = $builder->get(1);
		if ($query && $query->getNumRows() == 1)
		{
			$result = $query->getRow();
			$requestdata->track_id = $result->track_id;
			$requestdata->car_cat = $result->car_cat;
		}

		$myDb = $this->db->table('laps');
		$insert = $myDb->insert((array) $requestdata);
		$lap_id = $this->db->insertID();

		log_message('debug', "Insert: $insert. Lap ID:  $lap_id");

		//xml
		$laps = $this->reply->addChild('section');
		$laps->addAttribute('name','laps');

		$id = $laps->addChild('attnum');
		$id->addAttribute('name', 'id');
		$id->addAttribute('val', $this->db->insertID());

		$myDb = $this->db->table('races');
		$myDb->select('track_id, car_id, user_id');
		$myDb->where('id', $requestdata->race_id);
		
		$results = $myDb->get(1);
		if (!$results || $results->getNumRows() == 0) return;
		
		$racedata = $results->getRow();

		/*
		$myDb = $this->db->table('laps A');
		$myDb->select('min(A.laptime) as bestlap');
		$myDb->join('races B', 'A.race_id = B.id');
		$myDb->where([
			'B.car_id'		=> $racedata->car_id,
			'B.track_id'	=> $racedata->track_id
		]);
		*/
		// Get the car category
		$bestlap = 0.000;
		$car_cat = null;
		$query = $this->db->query('SELECT id FROM cars_cats WHERE carID = ?', [$racedata->car_id]);
		if ($query && $query->getNumRows() == 1)
		{
			$car_cat = $query->getRow()->id;
			$query = $this->bestLapsModel->select('laptime')->where([
				'track_id'	=> $racedata->track_id,
				'car_cat'	=> $car_cat
			])->get(1);

			//$results = $myDb->get(1);
			if ($query && $query->getNumRows() == 1) $bestlap = $query->getRow()->laptime;

			// If the lap time is less the current best lap, or don't have a best lap (0.000),
			// set this is the best lap

			if ($bestlap == 0.000 || $requestdata->laptime < $bestlap)
			{
				// Get the setup of teh race
				$query = $this->db->query('SELECT setup FROM races WHERE id = ?', [$requestdata->race_id]);
				if ($query && $query->getNumRows() == 1)
				{
					$setup = $query->getRow()->setup;
					if ($bestlap == 0.000)
					{
						$this->bestLapsModel->insert([
							'race_id'	=> $requestdata->race_id,
							'lap_id'	=> $lap_id,
							'track_id'	=> $racedata->track_id,
							'car_cat'	=> $car_cat,
							'car_id'	=> $racedata->car_id,
							'laptime'	=> $requestdata->laptime,
							'user_id'	=> $racedata->user_id,
							'setup'		=> $setup,
						]);
					}
					else if ($requestdata->laptime < $bestlap)
					{
						$this->bestLapsModel
							->where([
								'track_id'	=> $racedata->track_id,
								'car_cat'	=> $car_cat,
							])
							->set([
								'race_id'	=> $requestdata->race_id,
								'lap_id'	=> $lap_id,
								'car_id'	=> $racedata->car_id,
								'laptime'	=> $requestdata->laptime,
								'user_id'	=> $racedata->user_id,
								'setup'		=> $setup,
							])->update();
					}
				}
			}
		}
		
		//xml messages
		$messagges =  $this->reply->addChild('section');
		$messagges->addAttribute('name','messages');

		$number = $messagges->addChild('attnum');
		$number->addAttribute('name','number');
		$number->addAttribute('val', 1);

		$msg0 = $messagges->addChild('attstr');
		$msg0->addAttribute('name','message0');
		$msg0->addAttribute('val',"Position:".$requestdata->position."\nFuel:".$requestdata->fuel."\nLap:Best: ".formatLaptime($bestlap)."\nLast: ".formatLaptime($requestdata->laptime)."\n Diff: ".formatLaptime($requestdata->laptime - $bestlap));
	}

	private function login($requestdata)
	{
		$myDb = $this->db->table('users');
		$myDb->where('username', $requestdata->username);
		$results = $myDb->get(1);
		
		if ($results && $results->getNumRows() > 0)
		{
			// Check the password
			$user = $results->getRow();
			if (password_verify($requestdata->password, $user->password))
			{
				$user->sessionip = $_SERVER['REMOTE_ADDR'];
				$user->sessionid = bin2hex(openssl_random_pseudo_bytes(15));
				$user->sessiontimestamp = date('Y-m-d G:i:s');//2014-12-21 21:09:10

				$myDb->where('id', $user->id)->update((array) $user);

				//xml
				$login = $this->reply->addChild('section');
				$login->addAttribute('name','login');

				$sessionid = $login->addChild('attstr');
				$sessionid->addAttribute('name', 'sessionid');
				$sessionid->addAttribute('val', $user->sessionid);

				$id = $login->addChild('attnum');
				$id->addAttribute('name', 'id');
				$id->addAttribute('val', $user->id);

				// Save user id in the session
				$this->session->set('user_id', $user->id);

				//xml messages
				$messagges = $this->reply->addChild('section');
				$messagges->addAttribute('name','messages');

				$number = $messagges->addChild('attnum');
				$number->addAttribute('name','number');
				$number->addAttribute('val', 1);

				$msg0 = $messagges->addChild('attstr');
				$msg0->addAttribute('name','message0');
				$msg0->addAttribute('val',"Succesfull logged in as\n\n".$user->username);
			}
			else
			{
				//xml
				$login = $this->reply->addChild('section');
				$login->addAttribute('name','login');

				//xml messages
				$messagges = $this->reply->addChild('section');
				$messagges->addAttribute('name','messages');

				$number = $messagges->addChild('attnum');
				$number->addAttribute('name','number');
				$number->addAttribute('val', 1);

				$msg0 = $messagges->addChild('attstr');
				$msg0->addAttribute('name','message0');
				$msg0->addAttribute('val', "FAILED to login in as\n\n{$requestdata->username}\n\nWrong username or password");
			}
		}
		else
		{
			//xml
			$login = $this->reply->addChild('section');
			$login->addAttribute('name','login');

			//xml messages
			$messagges = $this->reply->addChild('section');
			$messagges->addAttribute('name','messages');

			$number = $messagges->addChild('attnum');
			$number->addAttribute('name','number');
			$number->addAttribute('val', 1);

			$msg0 = $messagges->addChild('attstr');
			$msg0->addAttribute('name','message0');
			$msg0->addAttribute('val',"FAILED to login in as\n\n{$requestdata->username}\n\nWrong username or password");
		}
	}
}
