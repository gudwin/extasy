<?
class Date_Helper {
	public static  $aMonthTitle = array(
			'Январь',
			'Февраль',
			'Март',
			'Апрель',
			'Май',
			'Июнь',
			'Июль',
			'Август',
			'Сентябрь',
			'Октябрь',
			'Ноябрь',
			'Декабрь',
		);
	public static  $aMonth = array(
			'января',
			'февраля',
			'марта',
			'апреля',
			'мая',
			'июня',
			'июля',
			'августа',
			'сентября',
			'октября',
			'ноября',
			'декабря',
		);
	public static  $englishMonths = array(
			'january',
			'february',
			'march',
			'april',
			'may',
			'june',
			'july',
			'august',
			'september',
			'october',
			'november',
			'december',
		);
	public static function getCyrilicViewValue($value,$bWithoutTime = false)
	{
		 
		$aDate = preg_split('#[ \:\-\.]#',$value);
		if ($aDate[1] == 0)
		{
			return '';
		}
		$aMonth = self::$aMonth;
		$szResult = $aDate[2].' '.$aMonth[$aDate[1] - 1].' '.$aDate[0];
		if ((!empty($aDate['3']) || !emptY($aDate[4])) and ($bWithoutTime == false))
		{
			$szResult.= ' '.$aDate[3].':'.$aDate[4];
		}
		return $szResult;
	}
	/**
	 * Возвращает дату в строковом представлении на аглийском языке
	 */
	public static function getEnglishViewValue($value, $format = 'F d, Y')
	{
		$date = date_parse($value); 
		$time = mktime(0,0,0,$date['month'],$date['day'],$date['year']);
		return date( $format, $time);
		
	}
	/**
		Декодирует дату, написанную на русском (определяет формат типа 20 августа 2009)
	 */
	public static function decodeCyrilicDate($value) {
		$value = explode(' ',trim($value));
		$value[1] = mb_strtolower($value[1],'utf-8');
		$day = $value[0];
		$year = $value[2];
		$month = 0;
		foreach (self::$aMonth as $key=>$row) { 
			if ($row == $value[1]) { 
				$month = $key+1;
				break;
			}	
		}
		return implode('-',array($year,$month,$day));
	}
	/**
	 * 
	 * Проверяет, корректен ли формат  даты
	 * @param string $subject
	 */
	public static function isCorrectDate($subject) {
		$datePattern = '#^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$#';
		return preg_match($datePattern, $subject);
	}
	public static function isCorrectDateTime($subject) {
		$datePattern = '#^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$#';
		return preg_match($datePattern, $subject);
	}
	public static function getISO8061( $date ) {
		return date('c',strtotime( $date ));
	}
}

/**
 * 
 * Алиас для класса
 * @author Gisma
 *
 */
class DateHelper extends Date_Helper {}
?>