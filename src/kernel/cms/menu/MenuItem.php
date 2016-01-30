<?php

use \Extasy\acl\ACLUser;
class MenuItem {
	protected $rights = array();

	protected $name = '';

	protected $link = '';

	protected $childs = array();

	public function __construct($name, $link, $rights) {
		$this->rights = $rights;
		$this->name   = $name;
		$this->link   = $link;
	}

	public function addChild( MenuItem $child ) {
		$this->childs[] = $child;
	}

	public function isVisible() {
		try {
			ACLUser::checkCurrentUserGrants($this->rights);
		} catch (Exception $e) {
			return false;
		}
	}
	public function getChilds( ) {
		return $this->childs;
	}
	public function getName() {
		return $this->name;
	}

	public function getLink() {
		return $this->link;
	}
}