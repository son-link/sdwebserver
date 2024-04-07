<?php
namespace App\Models;
use App\Models\BaseModel;

class CarCatsModel extends BaseModel
{
	protected $table      = 'cars_cats';
	protected $allowedFields = ['id', 'name', 'carId'];
}
