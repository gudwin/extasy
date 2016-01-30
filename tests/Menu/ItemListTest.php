<?php
namespace Extasy\tests\Menu;

use Extasy\Dashboard\Menu\ItemList;
use Extasy\Dashboard\Menu\MenuItem;
use \UsersLogin;

class ItemListTest extends BaseTest {
	/**
	 * @var ItemList
	 */
	protected $items = null;

	public function setUp() {
		parent::setUp();
		$this->items = new ItemList();
	}

	public function testGetLengthWhenEmpty() {
		$this->assertEquals( 0, $this->items->getLength() );
	}

	/**
	 * @expectedException \NotFoundException
	 */
	public function testGetThrowsEnxeptionOnUnknownMenuItem() {
		$this->items->get( -1 );
	}

	public function testAdd() {
		$item       = new MenuItem();
		$item->name = 'Test item';

		$this->items->add( $item );

		$this->assertEquals( 1, $this->items->getLength() );
		$this->assertEquals( $item, $this->items->get( 0 ) );
	}

	public function testElementsAccessableThruForeach() {
		$item  = new MenuItem();
		$item2 = new MenuItem();
		$item3 = new MenuItem();
		//
		$fixture = array( $item, $item2, $item3 );
		//
		$item->name->setValue( 'item1' );
		$item2->name->setValue( 'item2' );
		$item3->name->setValue( 'item3' );

		$this->items->add( $item );
		$this->items->add( $item2 );
		$this->items->add( $item3 );

		$this->checkSameOrder( $fixture );

	}

	public function testElementsSortedByOrderField() {
		$item  = new MenuItem();
		$item2 = new MenuItem();
		$item3 = new MenuItem();
		//
		$item->name->setValue( 'item1' );
		$item2->name->setValue( 'item2' );
		$item3->name->setValue( 'item3' );
		//
		$this->items->add( $item );
		$this->items->add( $item2 );
		$this->items->add( $item3 );
		//
		$item2->order = 2;
		$item3->order = 1;
		//
		$fixture = array( $item2, $item3, $item );

		$this->checkSameOrder( $fixture );
	}

	/**
	 * @expectedException \Extasy\Dashboard\Menu\Exception
	 */
	public function testGroupAddWithIncorrectArray() {
		$wrongFixture = array(
			array( 'name' => 'My title' ),
			'title'
		);
		$item         = new MenuItem();
		$item->getChildren()->groupAdd( $wrongFixture );
	}

	public function testGroupAdd() {
		$correctFixture = array(
			array( 'name' => 'Link 1' ),
			array( 'name' => 'Link 2' ),
		);
		$item           = new MenuItem();
		$item->getChildren()->add( $correctFixture );

		$this->assertEquals( 2, $item->getChildren()->getLength() );
		foreach ( $item->getChildren() as $key => $subItem ) {
			$this->assertEquals( $correctFixture[ $key ][ 'name' ], $subItem->name->getValue() );
		}
	}

	public function testGroupAddSupportObjects() {
		$secondItem     = array( 'name' => 'Link 2' );
		$correctFixture = array(
			array( 'name' => 'Link 1' ),
			new MenuItem( $secondItem ),
		);
		$item           = new MenuItem();
		$item->getChildren()->add( $correctFixture );

		$this->assertEquals( 2, $item->getChildren()->getLength() );


		$this->assertEquals( $secondItem[ 'name' ], $item->getChildren()->get( 1 )->name->getValue() );
	}

	public function testGroupAddSupportChildren() {
		$correctFixture = array(
			array( 'name'     => 'Link 1',
				   'children' => array(
					   array( 'name' => 'Link 1.1' ),
					   array( 'name' => 'Link 1.2' ),
					   array( 'name'     => 'Link 1.3',
							  'children' => array(
								  array( 'name' => 'Link 1.3.1' )
							  )
					   )
				   )
			),
			array( 'name' => 'Link 2' )
		);
		$item           = new MenuItem();
		$item->getChildren()->add( $correctFixture );

		$titleFixture = $correctFixture[ 0 ][ 'children' ][ 2 ][ 'children' ][ 0 ][ 'name' ];
		$testValue    = $item->getChildren()->get( 0 )->getChildren()->get( 2 )->getChildren()->get( 0 )->name;
		$this->assertEquals( $titleFixture, $testValue );
	}


	public function testParseData() {
		$child  = new MenuItem();
		$child2 = new MenuItem();
		//
		$child->name  = 'item1';
		$child2->name = 'item2';

		$this->items->add( $child );
		$this->items->add( $child2 );

		$parseData = $this->items->getParseData();
		$this->assertEquals( 2, sizeof( $parseData ) );
		$this->assertEquals( $child->getParseData(), $parseData[ 0 ] );

	}

	public function testParseDataSkipsInvisibleElements() {
		$child        = new MenuItem();
		$child->name  = 'invisible';
		$child2       = new MenuItem();
		$child2->name = 'visible';
		$this->items->add( $child );
		$this->items->add( $child2 );

		$child->obj_rights->setValue( array( MenuFixtures::rightFixture ) );

		$parseData = $this->items->getParseData();

		$this->assertEquals( 1, sizeof( $parseData ) );
		$this->assertEquals( $child2->getParseData(), $parseData[ 0 ] );
		//
		UsersLogin::login( MenuFixtures::loginFixture, MenuFixtures::passwordFixture );
		$parseData = $this->items->getParseData();

		$this->assertEquals( 2, sizeof( $parseData ) );
		$this->assertEquals( $child->getParseData(), $parseData[ 0 ] );

	}

	protected function checkSameOrder( $fixture ) {
		$countFixture = sizeof( $fixture );
		$count        = 0;
		foreach ( $this->items as $key => $menuItem ) {
			$this->assertEquals( $fixture[ $key ], $menuItem );
			//
			$count++;
		}
		//
		$this->assertEquals( $countFixture, $count );
	}
}
