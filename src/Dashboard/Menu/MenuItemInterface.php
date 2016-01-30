<?php


namespace Extasy\Dashboard\Menu;


interface MenuItemInterface {
    public function isVisible();
    public function getChildren();
    public function getParseData();
} 