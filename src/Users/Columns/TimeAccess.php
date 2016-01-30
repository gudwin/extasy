<?php

namespace Extasy\Users\Columns;


use Faid\View\View;

class TimeAccess extends \Extasy\Columns\BaseColumn {
	public function __construct( $fieldName, $fieldInfo, $value ) {
		parent::__construct( $fieldName, $fieldInfo, $value );
		$this->normalizeValue();
	}
	public function onAfterSelect( $dbData ) {
		if ( isset( $dbData[$this->szFieldName ])) {
			$this->aValue = unserialize( $dbData[ $this->szFieldName ]);
			$this->normalizeValue();
		}
	}
	public function setValue( $newValue ) {
		parent::setValue( $newValue );
		$this->normalizeValue();
	}

	protected function normalizeValue() {
		if ( empty( $this->aValue ) ) {
			$this->aValue = array(
				0 => array( 'day' => 1, 'time' => '' ),
				1 => array( 'day' => 1, 'time' => '' ),
				2 => array( 'day' => 1, 'time' => '' ),
				3 => array( 'day' => 1, 'time' => '' ),
				4 => array( 'day' => 1, 'time' => '' ),
				5 => array( 'day' => 1, 'time' => '' ),
				6 => array( 'day' => 1, 'time' => '' ),
			);
		}
		foreach ( $this->aValue as $key=>$row ) {
			if ( !isset( $row['day']) || empty( $row['day'])) {
				$this->aValue[$key]['day'] = 0;
			}
		}
	}
	public function onInsert(\Extasy\ORM\QueryBuilder $queryBuilder ) {

	}
	public function onUpdate(\Extasy\ORM\QueryBuilder $query ) {
		$query->setSet( $this->szFieldName,serialize( $this->aValue ));
	}

	public function getAdminFormValue() {
		$view = new View( __DIR__ . DIRECTORY_SEPARATOR . 'time_access.tpl' );
		$view->set( 'name', $this->szFieldName );
		$view->set( 'value', $this->aValue );
		return $view->render();
	}

	/**
	 *
	 */
	public function onLogin() {
		$dayOfWeek = intval(date('N' )) - 1;
		$timeAllowed = $this->aValue[$dayOfWeek]['time'];
		if ( !empty( $this->aValue[$dayOfWeek]['day'])) {
			if ( !empty( $timeAllowed )) {
				$hours = explode('-', $timeAllowed );
				$currentHour = intval( date('H'));
				$isBetween = (intval($hours[0]) <= $currentHour) && ($currentHour <= intval( $hours[1]));

				if ( !$isBetween ) {

					throw new TimeAccessException( 'Time Access Restriction - Not allowed at this time');
				}
			}
		} else {
			throw new TimeAccessException( 'Time Access Restriction - Not allowed at this time');
		}

	}
} 