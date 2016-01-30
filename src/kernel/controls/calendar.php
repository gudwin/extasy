<?
use \Extasy\CMS;
class CCalendar extends CControl{
	protected $szDate;
	public function __construct() {
		parent::__Construct();
		$this->szDate = date('Y-m-d');
	}
	public function __set($name,$value) {
		$this->szDate = htmlspecialchars($value);
	}
	public function generate() {
		$szResult = <<<EOD
<link type="text/css" rel="stylesheet" href="%sextasy/dhtml_calendar/dhtmlgoodies_calendar.css" media="screen"></LINK>
<SCRIPT type="text/javascript" src="%sextasy/dhtml_calendar/dhtmlgoodies_calendar.js"></script>
<input type="text" id="%s" name="%s" value="%s" />
<input type="button" value="..." class="calendarButton"  onclick="displayCalendar(document.getElementById('%s'),'yyyy-mm-dd',this)">
EOD;
		$szResult = sprintf(
			$szResult,
			CMS::getResourcesUrl(),
			CMS::getResourcesUrl(),
			htmlspecialchars($this->szName),
			htmlspecialchars($this->szName),
			$this->szDate,
			htmlspecialchars($this->szName)
		);
		return $szResult;
	}
}
?>
