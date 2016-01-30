<?php
namespace Extasy\tests\Menu;
use Extasy\Dashboard\Controllers\Menu;
use Extasy\Dashboard\Menu\ItemList;

class MenuTest extends BaseTest {
	public function testGetChilds( ) {
		$menu = new Menu( );
		$items = $menu->getItems();
		$this->AssertTrue( $items instanceof ItemList );
	}

}