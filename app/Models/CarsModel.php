<?php
namespace App\Models;
use App\Models\BaseModel;

class CarsModel extends BaseModel
{
	protected $table      		= 'cars';

	protected $allowedFields 	= ['id', 'name', 'img', 'category', 'width', 'length', 'mass', 'fueltank', 'engine', 'drivetrain'];

	/**
	 * Return the most used cars in the category
	 * @param array $carsCatIds List with the car's ID on the category
	 * @param string $period The period of time between the current date and when you want to obtain the list.
	 * @param int $page Current page
	 * @param int $limit The limit of results to be obtained
	 * @return array An array with the list and the total number of unpaginated results.
	 */
	public function getMostUsedCars(array $carsCatIds, string $period='today', int $page=0, int $limit=20)
	{
		$list = [];
		$total = 0;
		$offset = $page * $limit;
		$backto = getDateDiff($period);

		$builder = $this->db->table('races r');
		$builder->join('cars c', 'c.id = r.car_id');
		$builder->select('r.car_id, COUNT(r.car_id) as count, c.name');
		$builder->where('UNIX_TIMESTAMP(r.timestamp) >', $backto);
		$builder->whereIn('r.car_id', $carsCatIds);
		$builder->groupBy('r.car_id');
		$builder->orderBy('count DESC');
		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) $total = $query->getNumRows();
		if ($total == 0) return [[], 0];

		$builder = $this->db->table('races r');
		$builder->join('cars c', 'c.id = r.car_id');
		$builder->select('r.car_id, COUNT(r.car_id) as count, c.name');
		$builder->where('UNIX_TIMESTAMP(r.timestamp) >', $backto);
		$builder->whereIn('r.car_id', $carsCatIds);
		$builder->groupBy('r.car_id');
		$builder->orderBy('count DESC');
		if ($limit > 0) $builder->limit($limit, $offset);

		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) $list = $query->getResult();
		
		return [$list, $total];
	}
}
