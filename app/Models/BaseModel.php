<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $primaryKey = 'id';

    protected $returnType     = 'object';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function data($id)
	{
		$builder = $this->builder();
		$builder->where('id', $id);

		$query = $builder->get(1);

		if ($query) return $query->getRow();
	}
}
