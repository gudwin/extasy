<?
/**
 * Класс помощник по работе с целочисленными
 * @package _exst3.1
 * @author Gisma (info@gisma.ru) 05.11.2009
 */
class IntegerHelper
{
	/**
	 * Приводит поданное значение к целочисленному и позитивному
	 */
	public static function toNatural($value,$supportFloat = false)
	{
		if ($supportFloat) {
			$value = floatval($value);	
		} else {
			$value = intval($value);
		}
		
		if ($value < 0) 
		{
			$value = 0;
		}
		return $value;
	}
	/**
	 * Дает окончание слову: 1 Товар, 10 товаров, 3 товара
	 */
	public static function formatWord($num, $wordroot, $endings){  
     if((int)$num<=20) {  
         $last=(int)$num;  
     }else{  
         $last=(int)substr((string)$num, -1);  
     }  
     switch ($last){  
         case 1:  
             $ending=$endings[0];  
             break;  
         case 2:  
         case 3:  
         case 4:  
             $ending=$endings[1];  
             break;  
         default:  
             $ending=$endings[2];  
             break;  
     }  
     return $wordroot.$ending;  
 }  

}
?>