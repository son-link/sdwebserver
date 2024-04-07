<?php
namespace App\Models;
use App\Models\BaseModel;

class CarsModel extends BaseModel
{
	private $data;

	protected $table      = 'cars';

	protected $allowedFields = ['id', 'name', 'img', 'category', 'width', 'length', 'mass', 'fueltank', 'engine', 'drivetrain'];

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
