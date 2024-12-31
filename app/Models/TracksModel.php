<?php
namespace App\Models;

use App\Models\BaseModel;

use function PHPUnit\Framework\returnSelf;

class TracksModel extends BaseModel
{
	protected $table      = 'tracks';
	protected $db;
	protected $allowedFields = ['id', 'name', 'img',  'category', 'author', 'description'];

	/**
	 * Return the most used tracks in the category
	 * @param array $carsCatIds List with the car's ID on the category
	 * @param string $period The period of time between the current date and when you want to obtain the list.
	 * @param int $page Current page
	 * @param int $limit The limit of results to be obtained
	 * @return array An array with the list and the total number of unpaginated results.
	 */
	public function getMostUsedTracks(array $carsCatIds, string $period='today', int $page=0, int $limit=20)
	{
		$list = [];
		$total = 0;
		$offset = $page * $limit;
		$backto = getDateDiff($period);

		$builder = $this->db->table('races r');
		$builder->join('tracks t', 't.id = r.track_id');
		$builder->select('r.track_id, COUNT(*) AS count, t.name AS track_name');
		$builder->where('UNIX_TIMESTAMP(r.timestamp) >', $backto);
		$builder->whereIn('r.car_id', $carsCatIds);
		$builder->groupBy('r.track_id');
		$builder->orderBy('count DESC');
		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) $total = $query->getNumRows();
		if ($total == 0) return [[], 0];

		$builder = $this->db->table('races r');
		$builder->join('tracks t', 't.id = r.track_id');
		$builder->select('r.track_id, COUNT(*) AS count, t.name AS track_name');
		$builder->where('UNIX_TIMESTAMP(r.timestamp) >', $backto);
		$builder->whereIn('r.car_id', $carsCatIds);
		$builder->groupBy('r.track_id');
		$builder->orderBy('count DESC');
		if ($limit > 0) $builder->limit($limit, $offset);

		$query = $builder->get();

		if ($query && $query->getNumRows() > 0) $list = $query->getResult();
		
		return [$list, $total];
	}
}
