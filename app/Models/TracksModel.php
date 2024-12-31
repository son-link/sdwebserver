<?php
namespace App\Models;

use App\Models\BaseModel;

use function PHPUnit\Framework\returnSelf;

class TracksModel extends BaseModel
{
	protected $table      = 'tracks';
	protected $db;
	protected $allowedFields = ['id', 'name', 'img',  'category', 'author', 'description'];

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

	/*
	public function __construct($track=null)
	{
		if ($track) $this->import($track);
	}

	public function import($properties)
	{    
		foreach($properties as $key => $value) $this->{$key} = $value;
    }

	public function getLink($text='')
	{
		if($text == '') $text = $this->username;
		return '<a href="track/'.$this->id.'">'.$text.'</a>';
	}

	public function card($text='')
	{
		return $this->name.$this->img;
	}

	public function imgTag() {
		return "<img width='80' src='". base_url() ."/".$this->img."' alt='".$this->name."' title='".$this->name."'>";
	}
	
	public function imgTagFull() {
		return "<img src='". base_url() ."/".$this->img."'  class='track-img' alt='".$this->name."'>";
	}
	public function clickableName() {
		return $this->linkTag($this->name);

	}
	public function clickableImgTag() {
		return $this->linkTag($this->imgTag());
	}
	public function linkTag($content) {
		return "<a href='". base_url() ."/track/".$this->id."'>".$content."</a>";
	}

	public function linkTitleImgTag()
	{
		$content = $this->name . '<br />' . $this->imgTag();
		return "<a href='" . base_url() . "/track/{$this->id}'>$content</a>";
	}
	*/
}
