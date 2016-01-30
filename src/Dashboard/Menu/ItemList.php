<?php
namespace Extasy\Dashboard\Menu;

class ItemList implements \Iterator {
	protected $data = array();

	public function groupAdd( $import ) {

		foreach ( $import as $row ) {
            $validator = new IsMenuItemValidator( $row );
			$isValid = is_array( $row ) || $validator->isValid();
			if ( !$isValid ) {
				throw new Exception( 'Incorrect format' );
			}
			if ( is_array( $row ) ) {
				$item = new MenuItem( $row );
					if ( isset( $row[ 'children' ] ) ) {
					$item->getChildren()->groupAdd( $row[ 'children' ] );
					unset( $row[ 'children' ] );
				}
			} else {
				$item = $row;
			}
			$this->add( $item );
		}
	}

	public function add( $data ) {
		if ( is_array( $data ) ) {
			$this->groupAdd( $data );
			return;
		} else {
		}
		$item       = $data;
        if ( isset( $item->order )) {
            $orderValue = $item->order->getValue();
            if ( empty( $orderValue )) {
                $item->order->setValue( -1 * sizeof( $this->data ) );
            }
        }
		$this->data[ ] = $item;
	}

	public function get( $key ) {
		if ( !isset( $this->data[ $key ] ) ) {
			throw new \NotFoundException( 'Menu item not found' );
		}
		return $this->data[ $key ];
	}

	public function getParseData() {
		$result = array();
		foreach ( $this->data as $row ) {
			if ( $row->isVisible() ) {
				$result[ ] = $row->getParseData();
			}
		};
		return $result;
	}

	public function current() {
		return $this->data[ key( $this->data ) ];
	}

	public function rewind() {
		reset( $this->data );
		usort( $this->data,
			function ( $a, $b ) {
				return $b->order->getValue() - $a->order->getValue();
			} );
	}

	public function key() {
		return key( $this->data );
	}

	public function next() {
		next( $this->data );
	}

	public function valid() {
		return isset( $this->data[ key( $this->data ) ] );
	}

	public function getLength() {
		return sizeof( $this->data );
	}
}