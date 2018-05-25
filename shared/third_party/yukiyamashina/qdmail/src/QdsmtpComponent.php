<?php
namespace Qd;

class QdsmtpComponent extends QdsmtpBase{

	var $layout		= 'default';
	var $view_dir	= 'email';
	var $layout_dir	= 'email';
	var $template	= 'default';
	var $view		= null;

	function __construct( $param = null )
    {
		if( !is_null($param)){
			$param = func_get_args();
		}
		parent::QdsmtpBase( $param );
	}

	function startup(&$controller)
    {
		$this->Controller =& $controller;
		if( defined( 'COMPONENTS' ) ){
			$this->logFilename(COMPONENTS.$this->name.'.log');
			$this->errorlogFilename( COMPONENTS . '_error' . $this->name . '.log' );
		}
		return;
	}
}