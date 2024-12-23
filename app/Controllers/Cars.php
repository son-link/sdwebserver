<?php

namespace App\Controllers;
use App\Models\CarsModel;

class Cars extends BaseController
{
	private object $carsModel;

	public function __construct()
	{
		$this->carsModel = new CarsModel();
	}

	function index($id)
	{
		$this->cachePage(3600);
		$car = $this->carsModel->data($id);
		
		if (!$car) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		echo get_header('Car: ' . $car->name);
		echo view('car', ['car' => $car]);
		echo get_footer();
    }
}