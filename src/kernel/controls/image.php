<?
use \Faid\UParser;
class CImage extends CControl {
	protected $src;
	protected $class;
	protected $id;
	public function __set($name,$value) {
		if ($name == 'name') {
			$this->id = $value;
		} elseif ($name == 'id') {
			$this->id = $value;
		} elseif ($name == 'src') {
			$this->src = $value;
		} 
	}
	public function __get($name) {
	}
	public function generate() {
		$parseData = array(
			'name' => $this->id,
			'value'  => $this->src,
			'imageInfo' => arraY(
				'size' 		=> 0,
				'width' 	=> 0,
				'height' 	=> 0
			) 
		);
		if ((!empty($this->src)) && (file_exists(WEBROOT_PATH.$this->src))) {
			$path = WEBROOT_PATH.$this->src;
			$imageInfo = getimagesize($path);
			$parseData['imageInfo'] = arraY(
				'size' 		=> $this->format_bytes($path),
				'width' 	=> $imageInfo[0],
				'height' 	=> $imageInfo[1]
			);
		}
		
		return UParser::parsePHPFile(dirname(__FILE__).'/tpl/image.tpl',$parseData);
	}
	protected function format_bytes($path) {
		$size = filesize($path);
    	$units = array(' B', ' KB', ' MB', ' GB', ' TB');
    	for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    	return round($size, 2).$units[$i];
	}
}
?>