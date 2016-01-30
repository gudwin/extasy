<?php
namespace Extasy\kernel\cms\_pages;
	use \Extasy\Model\Model;
class ListOrder extends \AdminOrderPage {

	const title = 'Сортировка';

	public function __construct() {
		$type           = $_REQUEST[ 'type' ];

        $validator = new \Extasy\Validators\IsModelClassNameValidator(  $type );
        if ( !$validator->isValid() ) {
            throw new \ForbiddenException( 'Not an valid model' );
        }
        $title = call_user_func( array( $type, 'getLabel'), Model::labelName );

		$title          = sprintf( 'Назад "%s" ', $title );
		$aBegin         = array(
			$title      => 'list.php?type=' . $type,
			self::title => '#'
		);
		$this->typeName = $type;
		$this->back     = 'list.php?type=' . $type;
		$this->jump_to  = 'order.php?type=' . $type;
		parent::__construct( $aBegin, 'Сортировка' );
	}
}