<?php

namespace App\Controllers;
use App\Models\TracksModel;

class Tracks extends BaseController
{
	private object $tracksModel;

	public function __construct()
	{
		$this->tracksModel = new TracksModel();
	}

	function index($id)
	{
		$this->cachePage(3600);
		$track = $this->tracksModel->data($id);
		$bestLaps = getBestTimesTrack($id);
		echo get_header('Track: ' . $track->name);
		echo view('track', ['track' => $track, 'bestLaps' => $bestLaps]);
		echo get_footer();
    }
}