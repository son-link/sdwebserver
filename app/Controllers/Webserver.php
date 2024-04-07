<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Webserver extends BaseController
{
	use ResponseTrait;
	protected $xmlreply;
	protected $reply;
	const HASH = PASSWORD_DEFAULT;
	const COST = 16;

    public function index()
    {
		// Log connection
		log_message('debug', 'New connection');
		$so = getOS();
		$data = $this->request->getPost('data');

		if (!$data) return $this->failValidationErrors('No data received');

        $xml = xmlObj($data);

		$webserverversion= 1;
		$string='<?xml version="1.0" encoding="UTF-8"?>
		<params>
		</params>';

		$this->xmlreply = new \SimpleXMLElement($string);
		$temp = $this->xmlreply->xpath('/params');
		$params = $temp[0];//size"[@label='Large']");

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
				//$myDb->update($requestdata, $requestype, $conditions);

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

		//select the best lap for this car/track combo

		$myDb = $this->db->table('laps A');
		$myDb->select('min(A.laptime) as bestlap');
		$myDb->join('races B', 'A.race_id = B.id');
		$myDb->where([
			'B.car_id'		=> $requestdata->car_id,
			'B.track_id'	=> $requestdata->track_id
		]);
		/*$query="
			SELECT min(A.laptime) as bestlap
			FROM laps A
			INNER
				JOIN races B
				ON A.race_id = B.id
			WHERE
				B.car_id = '".$requestdata->car_id."'
				AND B.track_id = '".$requestdata->track_id."'
		";

		$bestlap = $myDb->customSelect($query);*/

		//$query = $myDb->getResult();
		//if (!$query) return;
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
		$myDb = $this->db->table('laps');
		$myDb->insert((array) $requestdata);

		//xml
		$laps = $this->reply->addChild('section');
		$laps->addAttribute('name','laps');

		$id = $laps->addChild('attnum');
		$id->addAttribute('name', 'id');
		$id->addAttribute('val', $this->db->insertID());

		//select the car and track id for this race
		/*$query="
			SELECT track_id, car_id
			FROM races
			WHERE
				id =".$requestdata->race_id."
		";*/

		$myDb = $this->db->table('races');
		$myDb->select('track_id, car_id');
		$myDb->where('id', $requestdata->race_id);
		
		$results = $myDb->get(1);
		if (!$results || $results->getNumRows() == 0) return;
		
		$racedata = $results->getRow();

		//$racedata = $myDb->customSelect($query);

		//select the best lap for this car/track combo
		/*$query="
			SELECT min(A.laptime) as bestlap
			FROM laps A
			INNER
				JOIN races B
				ON A.race_id = B.id
			WHERE
				B.car_id = '".$racedata[0]['car_id']."'
				AND B.track_id = '".$racedata[0]['track_id']."'
		";*/

		$myDb = $this->db->table('laps A');
		$myDb->select('min(A.laptime) as bestlap');
		$myDb->join('races B', 'A.race_id = B.id');
		$myDb->where([
			'B.car_id'		=> $racedata->car_id,
			'B.track_id'	=> $racedata->track_id
		]);

		$results = $myDb->get(1);
		if (!$results || $results->getNumRows() == 0) return;
		
		$bestlap = $results->getRow();
		
		//xml messages
		$messagges =  $this->reply->addChild('section');
		$messagges->addAttribute('name','messages');

		$number = $messagges->addChild('attnum');
		$number->addAttribute('name','number');
		$number->addAttribute('val', 1);

		$msg0 = $messagges->addChild('attstr');
		$msg0->addAttribute('name','message0');
		$msg0->addAttribute('val',"Position:".$requestdata->position."\nFuel:".$requestdata->fuel."\nLap:Best: ".formatLaptime($bestlap->bestlap)."\nLast: ".formatLaptime($requestdata->laptime)."\n Diff: ".formatLaptime($requestdata->laptime-$bestlap->bestlap));
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
				$msg0->addAttribute('val',"FAILED to login in as\n\n".$requestdata->username."\n\nWrong username or password");
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
			$msg0->addAttribute('val',"FAILED to login in as\n\n".$requestdata->username."\n\nWrong username or password");
		}
	}
}
