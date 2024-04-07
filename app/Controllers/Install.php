<?php

namespace App\Controllers;
use App\Models\UsersModel;
use CodeIgniter\API\ResponseTrait;

class Install extends BaseController
{
	use ResponseTrait;
	public function install()
	{
		helper('filesystem');
		$filename = ROOTPATH . '/sdwebserver.sql';
		$handle = fopen($filename, "r");
		$content = fread($handle, filesize($filename));
		fclose($handle);
		$file_array = explode(';', $content);
		foreach ($file_array as $query)
		{
			if ($query)
			{
				$this->db->query("SET FOREIGN_KEY_CHECKS = 0");
				$this->db->query($query);
				$this->db->query("SET FOREIGN_KEY_CHECKS = 1");
			}
		}	
	}

	public function update()
	{
		//$sql = "ALTER TABLE `users` ADD `level` TINYINT(1) NOT NULL DEFAULT '3' AFTER `id`;";

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `cars` (
			`id` varchar(50) DEFAULT NULL,
			`name` varchar(50) DEFAULT NULL,
			`img` varchar(100) DEFAULT NULL,
			`category` varchar(50) DEFAULT NULL,
			`width` varchar(10) DEFAULT NULL,
			`length` varchar(10) DEFAULT NULL,
			`mass` varchar(12) DEFAULT NULL,
			`fueltank` varchar(10) DEFAULT NULL,
			`engine` varchar(30) DEFAULT NULL,
			`drivetrain` varchar(5) DEFAULT NULL
			) ENGINE=InnoDB;
		");
			
		$this->db->query("CREATE TABLE IF NOT EXISTS `cars_cats` (
			`id` varchar(20) DEFAULT NULL,
			`name` varchar(50) DEFAULT NULL,
			`carID` varchar(50) DEFAULT NULL
			) ENGINE=InnoDB;
		");

		$this->db->query("CREATE TABLE IF NOT EXISTS `tracks` (
			`id` varchar(20) NOT NULL,
			`name` varchar(30) DEFAULT NULL,
			`img` varchar(100) DEFAULT NULL,
			`category` varchar(30) DEFAULT NULL,
			`author` varchar(50) DEFAULT NULL,
			`description` varchar(100) DEFAULT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");
					
		$this->db->query("CREATE TABLE IF NOT EXISTS `tracks_cats` (
			`id` varchar(20) DEFAULT NULL,
			`name` varchar(50) DEFAULT NULL,
			`trackID` varchar(50) DEFAULT NULL
			) ENGINE=InnoDB;
		");

		$this->respond(true);
	}

	/**
	 * Update cars, tracks, etc, data from the files
	 */
	public function updateData()
	{
		$carsFile = file_get_contents(WRITEPATH . '/data/cars.json');
		$cars = json_decode($carsFile, true);
		$add = [];

		foreach($cars as $id => $car) $add[] = $car;
		
		if (count($add) > 0)
		{
			$this->db->query('TRUNCATE cars');
			$builder = $this->db->table('cars');
			$insert = $builder->insertBatch($add);
		}

		// Now the tracks

		$tracksFile = file_get_contents(WRITEPATH . '/data/tracks.json');
		$tracks = json_decode($tracksFile, true);
		$add = [];

		foreach($tracks as $id => $track)
		{
			$track['description'] = $track['description']['val'];
			$add[] = $track;
		}
		
		if (count($add) > 0)
		{
			$this->db->query('TRUNCATE tracks');
			$builder = $this->db->table('tracks');
			$insert = $builder->insertBatch($add);
		}

		$carsCatsFile = file_get_contents(WRITEPATH . '/data/carCategories.json');
		$carCats = json_decode($carsCatsFile, true);
		$add = [];

		foreach($carCats as $id => $cat)
		{
			foreach($cat['cars'] as $car)
			$add[] = [
				'id'	=> $id,
				'name'	=> $cat['name'],
				'carId'	=> $car
			];
		}
		
		if (count($add) > 0)
		{
			$this->db->query('TRUNCATE cars_cats');
			$builder = $this->db->table('cars_cats');
			$insert = $builder->insertBatch($add);
		}

		$trackCatsFile = file_get_contents(WRITEPATH . '/data/trackCategories.json');
		$trackCats = json_decode($trackCatsFile, true);
		$add = [];

		foreach($trackCats as $id => $cat)
		{
			foreach($cat['tracks'] as $track)
			$add[] = [
				'id'		=> $id,
				'name'		=> $cat['name'],
				'trackId'	=> $track
			];
		}
		
		if (count($add) > 0)
		{
			$this->db->query('TRUNCATE tracks_cats');
			$builder = $this->db->table('tracks_cats');
			$insert = $builder->insertBatch($add);
		}
	}
}