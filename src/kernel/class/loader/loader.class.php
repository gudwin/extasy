<?	
	/**
		*	@name Loader-module 2005.10.29
		*	@desc Модуль для передачи управления функциям в зависимости от параметров из массивов GET и POST.
		*	Используются три метода 
		*	-	addPost(name,actionn)
		*	-	addGet(name,action)
		*	-	process()
		*	@author Gisma
		*	@package _Exstasy_Modules
	*/
	
class Loader  {
	var $aPost = array();
	var $aGet = array();
	var $szDefault = '';
	function addPost($name,$action) {
		$this->aPost[$name] = $action;
	}
	function addGet($name,$action) {
		$this->aGet[$name] = $action;
	}
	function setDefault($name) {
		$this->szDefault = $name;
	}
	function process() {
		$bFlag = 1;
		
		if ($_SERVER['REQUEST_METHOD'] == 'GET') 
			foreach ($this->aGet as $name=>$value) {
				$tmp = explode(',',$name);
				$bFlag = 1;
				for ($i = 0; $i < sizeof($tmp); $i++) 
					if (!isset($_GET[$tmp[$i]])) {
						$bFlag = 0;
						break;
					}	
				
				if ($bFlag == 1) 
					if (is_callable($value)) { 
						call_user_func($value);
						return;
					} else throw new Exception('Loader::process не могу найти функцию :"'.$value.'"');
			}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') 
			
			foreach ($this->aPost as $name=>$value)  {
				$tmp = explode(',',$name);
				$bFlag = 1;
				for ($i = 0; $i < sizeof($tmp); $i++) 
					if (!isset($_POST[$tmp[$i]])) {
						$bFlag = 0;
						break;
					}	
				if ($bFlag == 1) 
					if (is_callable($value)) { 
						call_user_func($value);
						return;
					} else throw new Exception('Loader::process не могу найти функцию :"'.$value.'"');
			}
		if (is_callable($this->szDefault)) {
			$value = $this->szDefault;
			call_user_func($value);
		}
	}
}

?>