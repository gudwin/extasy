<?php


namespace Extasy\Users\Social;


class Controller extends \extasyPage {
	protected $type = null;
	/**
	 * @var null
	 */
	protected $api = null;
	public function __construct() {
		$this->type = \Extasy\CMS::getInstance()->getDispatcher()->getRequest()->get('type');
		if ( !in_array( $this->type,['vkontakte','twitter','odnoklassniki'] )) {
			throw new \InvalidArgumentException('Unknown social network');
		}
		switch ( $this->type ) {
			case 'vkontakte' :
				$this->api = VkontakteApiFactory::getInstance();
				break;
			case 'twitter' :
				$this->api = TwitterApiFactory::getInstance();
				break;
			case 'odnoklassniki' :
				$this->api = OdnoklassnikiApiFactory::getInstance();
				break;
		}
	}
	public function oauthStart() {
		$url = $this->api->getAuthUrl();
		$this->jump( $url );
		die;
	}
	public function oauthResults() {
		try {
			$this->api->authCallback();

			$response = $this->api->getCurrentSession();
		} catch (\Exception $e ) {
			$response['error'] = $e->getMessage();
		}
		$view = new \Faid\View\View( __DIR__ . '/response.tpl');
		$view->set('response', $response);
		$view->set('type', $this->type);
		print $view->render();
		die;


	}
} 