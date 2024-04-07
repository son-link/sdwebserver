<?php
namespace App\Models;
use App\Models\BaseModel;

class TrackCatsModel extends BaseModel
{
	protected $table      = 'tracks_cats';
	protected $allowedFields = ['id', 'name', 'trackID'];
}
