<?php
namespace App\Models;
use App\Models\BaseModel;

class CarsModel extends BaseModel
{
	protected $table      		= 'cars';

	protected $allowedFields 	= ['id', 'name', 'img', 'category', 'width', 'length', 'mass', 'fueltank', 'engine', 'drivetrain'];

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

	/*
	public function __construct($car=null)
	{
		if ($car) $this->data = $this->data($car);
	}

	public function getLink(string $text=null): string
	{
		if(!$text) $text = $this->data->name;
		return "<a href='". base_url() . "'/car/{$this->data->id}'>$text</a>";
	}

	public function card() {
		return $this->data->name . $this->data->img;
	}

	public function imgTag()
	{
		return "<img width='80' src='" . base_url() . "/{$this->img}' alt='{$this->name}' title='{$this->name}'>";
	}

	public function imgTagFull()
	{
		return "<img src='" . base_url() . "/{$this->img}' class='car-img' alt='{$this->name}'>";
	}

	public function clickableName()
	{
		return $this->linkTag($this->name);
	}

	public function clickableImgTag()
	{
		return $this->linkTag($this->imgTag());
	}

	public function linkTag($content)
	{
		return "<a href='" . base_url() . "/car/{$this->id}'>$content</a>";
	}

	public function linkTitleImgTag()
	{
		$content = $this->name . '<br />' . $this->imgTag();
		return "<a href='" . base_url() . "/car/{$this->id}'>$content</a>";
	}
	*/
}
