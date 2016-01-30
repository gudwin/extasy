<?php
namespace Extasy\Controllers;

use \Faid\Debug\exceptionRenderer as faidExceptionRenderer;
use \Faid\Debug\errorRenderer as faidErrorRenderer;
use \Faid\Debug\Debug;
use \NotFoundException;
use \CMSLog;
use \CMSAuth;
use \ForbiddenException;
use \InternalErrorException;
use \SiteMapController;
class ErrorController extends SiteMapController {
	protected $layout = 'layout/default';
	protected $template;
	protected $errorProcessing = false;
	protected $errorDuringErrorProcessing = false;

	/**
	 *
	 */
	public function onException( $exception ) {

		try {

			$this->checkIfCMSStarted();
			$this->setupErrorProcessingFlag();

			// Decide how that
			if ( $exception instanceof NotFoundException ) {
				$this->sendHeader( 404 );
				$this->template = VIEW_PATH . 'errors/404.tpl';
			} elseif ( $exception instanceof ForbiddenException ) {
				$this->sendHeader( 403 );
				$this->template = VIEW_PATH . 'errors/403.tpl';
			} elseif ( $exception instanceof InternalErrorException ) {
				$this->sendHeader( 500 );
				$this->template = VIEW_PATH . 'errors/500.tpl';
			} else {
				$this->sendHeader( 404 );
				$this->template = VIEW_PATH . 'errors/custom.tpl';
			}
			// call events
			$this->prepareParseData();
			// Add developer information, if administrator logged in
			if ( CMSAuth::getInstance()->isLogined() ) {
				$add = array();
				// add stack trace
				$add[ 'source' ]      = debug::getFileSource( $exception->getFile(), $exception->getLine() );
				$add[ 'description' ] = $exception;

				$add[ 'trace' ] = \Faid\Debug\displayCallerCode( 2, false );
//				$add['trace'] = implode( "<br>\r\n", $exception->getTrace());
				$this->set( 'errorDescription', $add );
			}

			if ( !file_exists( $this->template ) ) {
				faidExceptionRenderer::render( $exception );
			} else {
				$this->output( $this->template, array(), array( 'Error.Render' ) );
			}

		}
		catch ( \Exception $e ) {
			faidExceptionRenderer::render( $e );
			faidExceptionRenderer::render( $exception );
			print nl2br($e);

		}
	}

	/**
	 * @param        $errno
	 * @param        $errstr
	 * @param string $errfile
	 * @param string $errline
	 */
	public function onError( $errno, $errstr, $errfile = '', $errline = '' ) {

		try {

			$this->sendHeader( 404 );
			$this->checkIfCMSStarted();
			$this->setupErrorProcessingFlag();

			$errorDescription = sprintf( 'Error [%d]: %s', $errno, $errstr );
			//
			$this->template = VIEW_PATH . 'errors/custom.tpl';
			//
			$this->prepareParseData();
			//
			if ( CMSAuth::getInstance()->isLogined() ) {
				$add = array();
				// add stack trace
				//debug::out( $errfile, $errline );
				if ( !empty( $errfile ) ) {
					$add[ 'source' ] = debug::getFileSource( $errfile, $errline );
				}
				$add[ 'description' ] = $errorDescription;
				// one for current call, one for render method, one for error handler
				$add[ 'trace' ] = \Faid\Debug\displayCallerCode( 2, false );
				$this->set( 'errorDescription', $add );
			}
			$this->output( $this->template, array(), array( 'Error.Render' ) );
		}
		catch ( \Exception $e ) {

			faidErrorRenderer::render( $errno, $errstr, $errfile, $errline );

			print nl2br($e);
		}
	}

	public function output( $tpl = '', $aData = array(), $aEvent = array() ) {
		ob_clean();
		return parent::output( $tpl, $aData, $aEvent );
	}

	protected function setupErrorProcessingFlag() {
		if ( $this->errorProcessing ) {
			throw new Exception( 'Internal error. Something wrong happened during error handling');
		}
		$this->errorProcessing = true;
	}

	/**
	 *
	 */
	protected function prepareParseData() {
		// Устанавливаем текущий документ
		$this->aUrlInfo = array(
			'name'          => 'Error page',
			'full_url'      => '/error',
			'script'        => 'ErrorController',
			'document_name' => '',
			'document_id'   => 0,
			'id'            => 0,
		);
		//
		//
		\Extasy\sitemap\Route::setCurrentUrlInfo( $this->aUrlInfo );
	}
	protected function sendHeader( $headerNumber ) {
		$map = array(
			403 => '403 Forbidden',
			404 => '404 Not Found',
			500 => '500 Internal Server Error'
		);
		if ( !isset( $map[ $headerNumber ] )) {
			throw new NotFoundException('Http Header code not found');
		}
		header( 'HTTP/1.1 '.$map[ $headerNumber ] );
	}
	protected function checkIfCMSStarted( ) {
		$cms = \Extasy\CMS::getInstance();
		if ( empty( $cms )) {
			throw new \Exception('Error happened before CMS started');
		}
	}
}