<?php
namespace App\Models;

use CodeIgniter\Model;
# use App\Libraries\Usuarios;

class TracksModel extends Model
{
	protected $db;

	public function __construct($track)
	{
		$this->import($track);
	}

	public function import($properties){    
		foreach($properties as $key => $value){
			$this->{$key} = $value;
		}
    }
	public function getLink($text='') {
		if($text == '') $text = $this->username;
		return '<a href="track/'.$this->id.'">'.$text.'</a>';
	}
	public function card($text='') {
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
}
