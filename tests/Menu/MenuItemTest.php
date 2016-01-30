<?php
namespace Extasy\tests\Menu;
use Extasy\Dashboard\Menu\MenuItem;
use Extasy\Dashboard\Menu\ItemList;
use UserAccount;
use UsersLogin;
class MenuItemTest extends BaseTest {
	public function testGetChildReturnsItemListObject( ) {
		$item = new MenuItem();
		$this->assertTrue( $item->getChildren() instanceof ItemList );
	}

	public function testParseData( ) {
		$nameFixture = 'testName';
		$linkFixture = 'testLink';
		$item = new MenuItem();
		$item->name = $nameFixture;
		$item->link = $linkFixture;
		//
		$parseData = $item->getParseData();
		$this->assertArrayHasKey( 'name', $parseData );
		$this->assertArrayHasKey( 'link', $parseData );
		//
		$this->assertEquals( $nameFixture, $parseData['name'] );
		$this->assertEquals( $linkFixture, $parseData['link'] );
		//
	}
	public function testParseDataIncludesChild( ) {

		$item = new MenuItem();
		$child = new MenuItem();
		$child2 = new MenuItem();
		//
		$child->name = 'test';
		$child2->name = 'test2';
		//
		$item->getChildren()->add( $child );
		$item->getChildren()->add( $child2 );
		//
		$parseData = $item->getParseData();
		$this->assertArrayHasKey( 'children', $parseData );
		$this->assertTrue( sizeof( $parseData['children'] ) == 2 );
	}
	public function testVisibleWithoutRights( ) {
		$item = new MenuItem( );
		$item->obj_rights->setValue(array( MenuFixtures::rightFixture));
		//
		$this->assertFalse( $item->isVisible( ));
	}
	public function testVisibleWithRights( ) {
		$item = new MenuItem( );
		$item->obj_rights->setValue(array( MenuFixtures::rightFixture));
		//
		UsersLogin::login( MenuFixtures::loginFixture, MenuFixtures::passwordFixture );
		//
		$this->assertTrue( $item->isVisible( ));
	}
}