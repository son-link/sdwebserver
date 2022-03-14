<?php

namespace App\Controllers;
use App\Models\TracksModel;
use App\Models\CarsModel;

class Tracks extends BaseController
{

	function index($id)
	{
		$this->cachePage(3600);
		$track = getTrack($id);
		$bestLaps = getBestTimesTrack($id);
		echo get_header('Track: ' . $track->name);
		echo view('track', ['track' => $track, 'bestLaps' => $bestLaps]);
		echo get_footer();
    }
}