<?php

namespace App\Controllers;
use App\Models\CarsModel;

class Cars extends BaseController
{

	function index($id)
	{
		//$this->cachePage(3600);
		$car = getCar($id);
		echo get_header('Car: ' . $car->name);
		echo view('car', ['car' => $car]);
		echo get_footer();
    }
}