<?php
namespace App\Models;
use App\Models\BaseModel;

class BestLapsModel extends BaseModel
{
	protected $table      = 'bests_laps';
	protected $allowedFields = ['race_id', 'lap_id', 'track_id', 'car_cat', 'car_id', 'laptime', 'user_id', 'setup'];
}
