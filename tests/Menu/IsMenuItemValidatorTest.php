<?php


namespace Extasy\tests\Menu;


use Extasy\Dashboard\Menu\IsMenuItemValidator;
use Extasy\Dashboard\Menu\MenuItem;

class IsMenuItemValidatorTest extends BaseTest {
    public function testValidator() {
        $validator = new IsMenuItemValidator( new MenuItem( ));
        $this->AssertTrue( $validator->isValid() );

        $validator = new IsMenuItemValidator( new \stdClass());
        $this->AssertFalse( $validator->isValid() );
    }

} 