<?php


namespace Extasy\Dashboard\Menu;


use Faid\Validator;

class IsMenuItemValidator extends Validator {
    protected $menuItem;
    public function __construct( $menuItem ) {
        $this->menuItem= $menuItem;
    }
    protected function test() {
        return  is_object( $this->menuItem) && $this->menuItem instanceof MenuItemInterface;
    }
} 