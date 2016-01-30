<?php
namespace Extasy\acl\control {
	use \CCheckbox;
	class Helper {
		public static function outputCheckbox( $name,$value, $checked, $title ) {
			static $number = 0;
			$checkbox          = new CCheckbox();
			$checkbox->id = sprintf('rightMap_%d', $number);
			$checkbox->name    = $name.'[' . $value . ']';
			$checkbox->value   = 1;
			$checkbox->checked = $checked;
			if ( !empty( $title ) ) {
				$checkbox->title = $value;
			}
			else {
				$checkbox->title = $value;
			}

			$number++;
			$result =  sprintf('<div class="checkbox"><b>%s</b><span>%s</span></div>',$checkbox->generate(),$title);
			return $result;
		}
		public static function drawRecursive( $name, $fullGrantList, $grantList, $level = 0)  {
			$result = '';
			foreach ( $fullGrantList as $row ) {
				$isChecked = !empty($grantList[$row[ 'fullPath' ]] );
				$checkbox  = self::outputCheckbox( $name, $row[ 'fullPath' ], $isChecked, $row[ 'title' ] );
				$result .= $checkbox;
				if ( !empty( $row[ 'children' ] ) ) {
					$result .= '<div style="margin-left:30px;">';
					$result .= self::drawRecursive( $name,$row[ 'children' ], $grantList, $level + 1);
					$result .= '</div>';
				}
			}
			return $result;
		}
	}
}