<?php
use \Faid\UParser;
class CUserSelect extends CControl {
	/**
	 * 
	 * Хранит индекс текущего юзверя
	 * @var unknown_type
	 */
	protected $currentUserId;
	public function __set($key, $value) {
		$key = strtolower($key);
		switch ($key) {
			case 'name':
				$this->szName = $value;
				break;
			case 'current':
				$this->currentUserId = $value;
				break;
		}
	}	
	public function generate() {
		$parseData = array(
			'name' => $this->szName,
		);
		if (!empty($this->currentUserId)) {
			try {
				$user = new UserAccount($this->currentUserId);
				$parseData['currentUser'] = $user->getParseData();
			} catch (Exception $e) {
				
			}
		}
		
			
		return UParser::parsePHPFile( __DIR__ .DIRECTORY_SEPARATOR .'tpl/userselect.tpl', $parseData);
	}
}