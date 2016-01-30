<?php

namespace Extasy\tests\Users\Social\Api\Facebook;

use \Extasy\Users\Social\FacebookApiFactory;
use \Extasy\Users\Social\Network;
use \Extasy\Users\Columns\SocialNetworks;
use \Extasy\tests\Helper;

abstract class BaseTest extends \Extasy\tests\Users\UsersTest {
	/**
	 * @var TestFacebookApi
	 */
	protected $api = null;

	const UID = '123456';

	public function setup() {
		parent::setUp();
		$this->api = new TestFacebookApi();
		FacebookApiFactory::setInstance( $this->api );

		Helper::dbFixtures( [
								Network::TableName       => [
									[ 'name' => 'facebook' ],
									[ 'name' => 'vkontakte' ],
								],
								SocialNetworks::UIDTable => [
									[ 'user_id' => 1, 'network_id' => 1, 'uid' => self::UID ]
								]
							] );

	}
} 