<?php

use App\Models\UsersModel;
use App\Models\TracksModel;
use App\Models\CarsModel;

function get_header($title, $css=[])
{
	if (!is_array($css)) $css = [];
	$data = [
		'title'			=> $title,
		'custom_css'	=> $css,
	];

	return view('templates/header', $data);
}

/**
 * Esta función muestra la plantilla del pie de la página.
 * @param Array $css Un array con los scripts opcionales.
 * @return * La vista del pie o false en caso de error.
 */
function get_footer($js=[])
{
	if (!is_array($js)) $js = [];
	$data = [ 'custom_js' => $js ];
	return view('templates/footer', $data);
}

/* ================================================
 * make xml data saveable on database
 * 
 * ================================================*/
 /**
    @param:
        $xml: SimpleXMLElement
        $force: set to true to always create 'text', 'attribute', and 'children' even if empty
    @return
        object with attributs:
            (string) name: XML tag name
            (string) text: text content of the attribut name
            (array) attributes: array witch keys are attribute key and values are attribute value
            (array) children: array of objects made with xml2obj() on each child
**/
function xmlObj($xmlstring)
{
	return json_decode(json_encode(simplexml_load_string($xmlstring,'SimpleXMLElement',LIBXML_NOCDATA )));
}

###################################
## extract content of a json txt file an d return an object of the json string
###################################
function jsonTxtFileToObj($fileUrl, $objClass)
{
	$myfile = fopen($fileUrl, "r") or die("Unable to open file!");
	$text = fread($myfile,filesize($fileUrl));
	fclose($myfile);

	$text = str_replace(", '", ', "', $text);
	$text = str_replace("',", '",', $text);
	$text = str_replace("{'", '{"', $text);
	$text = str_replace("'}", '"}', $text);
	$text = str_replace("['", '["', $text);
	$text = str_replace("']", '"]', $text);
	$text = str_replace("':", '":', $text);
	$text = str_replace(": '", ': "', $text);
	$objects = json_decode($text);
	$newObjects= new \stdClass();

	$models = [
		'track' => 'App\Models\TracksModel'::class,
		'car' => 'App\Models\CarsModel'::class,
		'CarCategory' => '\CarCategory'::class,
		'TrackCategory' => '\TrackCategory'::class
	];
	foreach ($objects as $key => $value)
	{
		$model = $models[$objClass];
		$newObjects->$key = new $model($value);
	}

	return $newObjects;
}

###################################
## get car categories info from the json text
###################################
function getCars()
{
	return jsonTxtFileToObj(WRITEPATH . "/data/cars.txt", 'car');
}

function getCarCats()
{
	return jsonTxtFileToObj(WRITEPATH . "/data/carCategories.txt", 'CarCategory');
}

function getTracks()
{
	return  jsonTxtFileToObj(WRITEPATH . "/data/tracks.txt", 'track');
}

function getTrackCats()
{
	return jsonTxtFileToObj(WRITEPATH . "/data/trackCategories.txt", 'TrackCategory');
}

###################################
## 
###################################
function secondsToTime($seconds)
{
	/*
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a d, %h hr, %i min, %s sec');
    */
	if ($seconds == 0) return 0;
	
	$date1 = new \DateTime("@0");
	$date2 = new \DateTime("@$seconds");
	$interval = $date1->diff($date2);
	$str = '';
	$years = $interval->y;
	$months = $interval->m;
	$days = $interval->d;
	$hours = $interval->h;
	$minutes = $interval->i;

	if ($years > 0) $str.= $years." years ";

	if ($months > 0) $str.= $months." months ";

	if ($days > 0) $str.= $days." days ";

	if($hours > 0) $str.= $hours." hours ";

	if($minutes > 0) $str.= $minutes." minutes ";

	return $str;
}

###################################
## 
###################################
function formatLaptime($seconds)
{
	$seconds = $seconds *1;
	$str='';

	//minuti
	$str.= sprintf('%02d', ($seconds/60)).':';
	$seconds = fmod($seconds, 60); 

	//secondi
	$str.=  sprintf('%02d',$seconds).'.';

	//decimali
	$decimals =  fmod($seconds, 1)*1000;
	$str.= sprintf('%03d',$decimals);

	return $str;
}

###################################
## 
###################################
function percentStr($smallvalue, $bigvalue)
{
	if($bigvalue==0) return '-%';

	$percent = round($smallvalue*100/$bigvalue,0);
	return $percent.'%';
}

###################################
## 
###################################
function weatherTag($value)
{
	switch($value)
	{
		case 0:
			return '<i class="wi wi-day-sunny"></i>';
			break;
		case 1:
			return '<i class="wi wi-rain"></i>';
			break;
		case 2:
			return '<i class="wi wi-rain"></i>';
			break;
		case 3:
			return '<i class="wi wi-rain"></i>';
			break;		
	}
}

###################################
## 
###################################
//this will rewrite the current url 
//and modify the given param if needed
function rewriteUrl($paramName, $paramValue)
{
	$query = $_GET;
	// replace parameter(s)
	$query[$paramName] = $paramValue;
	// rebuild url
	$query_result = http_build_query($query);
	// new link
	return base_url().'?'.$query_result;
}


function getCar($carId)
{
	$cars = getCars();

	if (property_exists($cars, $carId))
	{
		return $cars->$carId;
	}
	else
	{
		$fakeCar = new CarsModel([]);
		$fakeCar->category = '';
		$fakeCar->engine = '';
		$fakeCar->fueltank = '';
		$fakeCar->name = $carId.'(Modded)';
		$fakeCar->img = '';
		$fakeCar->lenght = '';
		$fakeCar->width = '';
		$fakeCar->mass = '';
		$fakeCar->drivetrain = '';
		$fakeCar->id = $carId;
		return $fakeCar;
	}
}

function getTrack($trackId)
{
	$tracks = getTracks();
	if (property_exists($tracks, $trackId))
	{
		return $tracks->$trackId;
	}
	else
	{
		$fakeTrack = new TracksModel([]);
		$fakeTrack->category = '';
		$fakeTrack->description = '';
		$fakeTrack->author = '';
		$fakeTrack->id = $trackId;
		$fakeTrack->name = $trackId.'(Modded)';
		$fakeTrack->img = '';
		return $fakeTrack;
	}
}

function generateConditions($conditions, $separator = ',')
{
	$txt='';

	foreach ($conditions as $key => $value)
	{
		$txt.=" $key = '$value'".$separator;
	}

	$pos = strrpos($txt, $separator);

	if ($pos !== false)
	{
		$txt = substr_replace($txt, '', $pos, strlen($separator));
	}

	return $txt;
}

class CarCategory
{
	function CarCategory($category){
		$this->import($category);
	}
	
    public function import($properties){    
		foreach($properties as $key => $value){
			$this->{$key} = $value;
		}
    }
}

class TrackCategory
{
	function TrackCategory($category){
		$this->import($category);
	}
    public function import($properties){    
		foreach($properties as $key => $value){
			$this->{$key} = $value;
		}
    }
}

function racetype($num){
	switch ($num){
		case 0: return 'practice';break;
		case 1: return 'qualify';break;
		case 2: return 'race';break;
	}
}

function comp_property($obj, $name)
{
	if($obj)
	{
		if (property_exists($obj, $name)) return $obj->$name;
		else return null;
	}
}

/**
 * Returns the 10 best times of this track
 * @param string $trackId The track's ID
 * @return array A array of object with the result
 */
function getBestTimesTrack($trackId)
{
	$db = \Config\Database::connect();
	$builder = $db->table('races r');
	$builder->select('u.username, r.track_id, l.laptime, r.id, r.car_id');
	$builder->join('laps l', 'l.race_id = r.id');
	$builder->join('users u', 'u.id = r.user_id');
	$builder->where('r.track_id', $trackId);
	$builder->orderBy('l.laptime');
	$query = $builder->get(10);
		
	if (!$query || $query->getNumRows() == 0)
	{
		echo "No laps to show for this tracks";
		return [];
	}

	return $query->getResult();
}

function getCarBetsLaps($carId)
{
	$sql = "SELECT MIN(l.laptime) as laptime, r.track_id, r.car_id, u.username
	FROM races r
	INNER JOIN laps l ON l.race_id = r.id
	INNER JOIN users u on u.id = r.user_id
	WHERE r.car_id = '$carId'
	GROUP BY r.id
	ORDER BY l.laptime";
}