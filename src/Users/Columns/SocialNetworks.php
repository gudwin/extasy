<?php

namespace Extasy\Users\Columns;

use \Faid\DB;
use \Faid\DBSimple;
use \Extasy\Users\Social\Network;

class SocialNetworks extends \Extasy\Columns\BaseColumn {

	const UIDTable = 'user_uuids';


	public function onAfterSelect( $dbData ) {
		if ( isset( $dbData[ $this->szFieldName ])) {
			$this->setValue( $dbData[ $this->szFieldName ]);
		} else {
			$this->autoloadValue();
		}
	}
	public function onDelete(\Extasy\ORM\QueryBuilder $queryBuilder ) {
		if ( !empty( $this->document )) {
			DBSimple::delete(self::UIDTable, array(
				'user_id' => $this->document->id->getValue()
			));
		}
	}
	protected function autoloadValue() {
		$networks       = DBSimple::select( Network::TableName );
		$personalTokens = DBSimple::select( self::UIDTable,
											array(
												'user_id' => $this->document->id->getValue()
											) );
		$map            = [ ];
		$newValue       = [ ];
		foreach ( $networks as $row ) {
			$map[ $row[ 'id' ] ] = $row;
		}

		foreach ( $personalTokens as $row ) {

			if ( !isset( $map[ $row[ 'network_id' ] ] ) ) {
				throw new \NotFoundException( 'Unknown social network. Value - ' . $row[ 'network_id' ] );
			}
			$network = $map[ $row[ 'network_id' ] ];

			$newValue[ $network[ 'name' ] ] = $row[ 'uid' ];
		}
		$this->aValue = $newValue;
	}

	public function setValue( $newValue ) {
		$tmp = [ ];
		foreach ( $newValue as $network => $uid ) {
			$condition = [

			];
			if ( $this->document->id->getValue() > 0 ) {
				$condition[ ] = sprintf( 'user_id != "%d"', $this->document->id->getValue() );
			}

			try {
				$network = Network::getByName( $network );
			}
			catch ( \NotFoundException $e ) {
				continue;
			}

			$found = DBSimple::get( self::UIDTable,
									   array(
										   'network_id' => $network->id->getValue(),
										   'uid'        => $uid,
											sprintf(' `user_id` != "%d" ', $this->document->id->getValue())
									   ) );
			if ( $found ) {
				throw new SocialNetworksException( 'UID already used. ' . $found[ 'uid' ] );
			}
			$tmp[ $network->name->getValue() ] = $uid;
		}
		$this->aValue = $tmp;
	}

	public function onInsert(\Extasy\ORM\QueryBuilder $queryBuilder ) {
		$this->storeValue();
	}

	public function onUpdate(\Extasy\ORM\QueryBuilder $queryBuilder ) {
		$this->storeValue();
	}

	protected function storeValue() {
		$userId = $this->document->id->getValue();
		if ( empty( $userId )) {
			return ;
		}
		DBSimple::delete( self::UIDTable, array( 'user_id' => $userId ) );

		foreach ( $this->aValue as $network => $uid ) {
			$network = Network::getByName( $network );
			DBSimple::insert( self::UIDTable,
							  array(
								  'date'       => date( 'Y-m-d H:i:s' ),
								  'network_id' => $network->id->getValue(),
								  'user_id'    => $this->document->id->getValue(),
								  'uid'      => $uid
							  ) );
		}
	}
	public function getViewValue() {
		return $this->getValue();
	}
	public function getAdminFormValue() {
		$map = [ ];
		foreach ( $this->aValue as $network => $token ) {
			$network = Network::getByName( $network );
			$map[ ]  = [ 'title' => $network->title->getValue() , 'value' => $token ];
		}

		$view = new \Faid\View\View( __DIR__ . DIRECTORY_SEPARATOR . 'social_network.tpl' );
		$view->set( 'map', $map );
		return $view->render();
	}
	public static function getByUID( $uid, $network ) {
		$network = Network::getByName( $network );
		$found = DBSimple::get(self::UIDTable, [
			'uid' => $uid,
			'network_id' => $network->id->getValue()
		]);
		if ( empty( $found )) {
			throw new SocialNetworksException('UID not found');
		}
		$user = \UserAccount::getById( $found['user_id']);
		return $user;
	}

	public static function createTables() {

		$sql = <<<SQL
	create table %s (
		`id` int not null auto_increment,
		`user_id` int not null default 0,
		`network_id` int not null default 0,
		`uid` varchar(255) not null default '',
		`date` datetime not null default '0000-00-00 00:00:00',
		primary key ( `id` ),
		index `search_by_user` ( `user_id` )

	)
SQL;
		$sql = sprintf( $sql, self::UIDTable );
		DB::post( $sql );
	}

	public static function addColumn( $szType, $szFieldName, $aAdditional ) {
		self::createTables();
	}
} 