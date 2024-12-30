<?php
namespace App\Models;
use App\Models\BaseModel;

class CarCatsModel extends BaseModel
{
	protected $table      = 'cars_cats';
	protected $allowedFields = ['id', 'name', 'carId'];

	/**
	 * Return all cars in the indicated category
	 * @param mixed $carCatId The ID od the category
	 * @return array A array with all cars ID in teh category
	 */
	public function getCarsInCat($carCatId)
	{
		$carsCatList = $this->select('carId')->where('id', $carCatId)->findAll();

		$carsCatIds = [];
		foreach ($carsCatList as $car) $carsCatIds[] = $car->carId;

		return $carsCatIds;
	}
}
