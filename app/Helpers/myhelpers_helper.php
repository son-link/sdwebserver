<?php

use App\Models\UsersModel;
use App\Models\TracksModel;
use App\Models\CarsModel;

function get_header($title, $css=[], $dasboard=false)
{
	if (!is_array($css)) $css = [];
	$data = [
		'title'			=> $title,
		'custom_css'	=> $css,
	];

	if ($dasboard) return view('dashboard/header', $data);
	return view('templates/header', $data);
}

/**
 * Esta función muestra la plantilla del pie de la página.
 * @param array $js Un array con los scripts opcionales.
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
/*
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
*/
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

function percentStr($smallvalue, $bigvalue)
{
	if($bigvalue==0) return '-%';

	$percent = round($smallvalue*100/$bigvalue,0);
	return $percent.'%';
}

function weatherTag($value)
{
	switch($value)
	{
		case 0:
			return '<i class="wi wi-day-sunny" title="Sunny"></i>';
		case 1:
			return '<i class="wi wi-rain" title="Light rain"></i>';
		case 2:
			return '<i class="wi wi-rain" title="Medium rain"></i>';
		case 3:
			return '<i class="wi wi-rain" title="Hard rain"></i>';
	}
}


// This will rewrite the current url
// and modify the given param if needed
function rewriteUrl($paramName, $paramValue)
{
	$query = $_GET;
	// replace parameter(s)
	$query[$paramName] = $paramValue;
	// rebuild url
	$query_result = http_build_query($query);
	// new link
	return base_url() . '?' .$query_result;
}

/*
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
*/
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
/*
class CarCategory
{
	function category($category)
	{
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
	function category($category){
		$this->import($category);
	}
	public function import($properties){
		foreach($properties as $key => $value){
			$this->{$key} = $value;
		}
	}
}
*/

function racetype($num)
{
	switch ($num){
		case 0:
			return 'practice';
		case 1:
			return 'qualify';
		case 2:
			return 'race';
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

	if (!$query || $query->getNumRows() == 0) return [];

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

function getOS() {

	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	log_message('debug', "HTTP_USER_AGENT: $user_agent");
	$os_platform = "Unknown Operative System";
	$os_array = array(
		'/windows nt 10/i'		=>  'Windows 10',
		'/windows nt 6.3/i'		=>  'Windows 8.1',
		'/windows nt 6.2/i'		=>  'Windows 8',
		'/windows nt 6.1/i'     =>  'Windows 7',
		'/windows nt 6.0/i'		=>  'Windows Vista',
		'/windows nt 5.2/i'		=>  'Windows Server 2003/XP x64',
		'/windows nt 5.1/i'		=>  'Windows XP',
		'/windows xp/i'			=>  'Windows XP',
		'/windows nt 5.0/i'		=>  'Windows 2000',
		'/windows me/i'			=>  'Windows ME',
		'/win98/i'				=>  'Windows 98',
		'/win95/i'				=>  'Windows 95',
		'/win16/i'				=>  'Windows 3.11',
		'/macintosh|mac os x/i' =>  'Mac OS X',
		'/mac_powerpc/i'		=>  'Mac OS 9',
		'/linux/i'				=>  'Linux',
		'/ubuntu/i'				=>  'Ubuntu',
		'/iphone/i'				=>  'iPhone',
		'/ipod/i'				=>  'iPod',
		'/ipad/i'				=>  'iPad',
		'/android/i'			=>  'Android',
		'/blackberry/i'			=>  'BlackBerry',
		'/webos/i'				=>  'Mobile',
		'/libcurl-agent/i'		=>  'Unknown (Curl)'
	);

	foreach ( $os_array as $regex => $value )
		if ( preg_match($regex, $user_agent ) ) $os_platform = $value;

	log_message('debug', "Operative System: $os_platform");
	return $os_platform;
}

/**
 * Returns if the current user if log in
 * @return bool The user level if logged in
 */
function ifLoggedIn(): bool
{
	//$db = \Config\Database::connect();
	$session = session();
	if ($session->get('logged_in')) return true;
	return false;
}

function findObjectById($array, $id)
{
	foreach ( $array as $element )
	{
		if ( $id == $element->id ) return true;
	}

	return false;
}

function imgTag(string $img, string $width, string $title): string
{
	$img = str_replace('./', '/', $img);
	$url = base_url($img);
	return "<img width='$width' src='$url' alt='$title' title='$title'>";
}

function imgTagFull(string $img, string $class, string $alt)
{
	$img = str_replace('./', '/', $img);
	$url = base_url($img);
	return "<img src='$url' class='$class' alt='$alt'>";
}

function clickableName(string $id, string $type, string $content): string
{
	return linkTag($id, $type, $content);
}

function clickableImgTag(string $id, string $size, string $type, string $title): string
{
	return linkTag($id, $type, imgTag($id, $size, $title));
}

function linkTag(string $id, string $type , string $content): string
{
	$url = base_url("$type/$id");
	return "<a href='$url'>$content</a>";
}

function linkTitleImgTag(string $id, string $type, string $name, string $img): string
{
	if ($type != 'car' && $type != 'track') return '';

	$url = base_url("$type/$id");
	$content = $name . '<br />' . imgTag($img, '80px', $name);
	return "<a href='$url'>$content</a>";
}

/**
 * Return time diff between current date and period
 * @param mixed $period
 * @return int
 */
function getDateDiff($period)
{
	switch ($period)
		{
			case 'today': //today
				$datediff = 1 * 24 * 60 * 60;
				return time() - $datediff;
			case 'week': //last week
				$datediff = 7 * 24 * 60 * 60;
				return time() - $datediff;
			case 'month': //last month
				$datediff = 30 * 24 * 60 * 60;
				return time() - $datediff;
			case 'year': //last year
				$datediff = 365 * 24 * 60 * 60;
				return time() - $datediff;
			default://always
				$datediff = 50000 * 24 * 60 * 60;
				return time() - $datediff;
		}
}