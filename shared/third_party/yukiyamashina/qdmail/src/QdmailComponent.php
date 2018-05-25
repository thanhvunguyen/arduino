<?php
namespace Qd;

class QdmailComponent extends QdmailUserFunc{

	var $framework	= 'CakePHP';
	var $view_dir	= 'email';
	var $layout_dir	= 'email';
	var $layout		= 'default';
	var $template	= 'default';
	var $view		= null;

	function __construct( $param = null ){
		if( !is_null($param)){
			$param = func_get_args();
		}
		parent::__construct( $param );
	}

	function startup(&$controller) {
		$this->Controller =& $controller;
		if( defined( 'COMPONENTS' ) ){
			$this->logPath(COMPONENTS);
			$this->errorlogPath(COMPONENTS);
		}
		return;
	}
	//----------------------------
	// Override Parent Method
	//----------------------------
	function & smtpObject( $null = false ){
		if( isset( $this->Qdsmtp ) && is_object( $this->Qdsmtp ) ){
			return $this->Qdsmtp;
		}

		if( !class_exists ( 'QdsmtpComponent' ) ){
			if( !$this->import( 'Component' , 'Qdsmtp' ) ){
				return $this->errorGather('Qdmail<->CakePHP Component Load Error , the name is Qdsmtp',__LINE__);
			}
		}
		$this->Qdsmtp = new QdsmtpComponent();
		if( !is_object( $this->Qdsmtp ) ){
				return $this->errorGather('Qdmail<->CakePHP Component making Instance Error , the name is QdsmtpComponent',__LINE__);
		}
		$this->Qdsmtp -> startup( $this->Controller );
		return $this->Qdsmtp;
	}
	//----------------------------
	// Cake Interface
	//----------------------------
	function import( $kind , $name ){
		if( 1.2 > (float) substr(Configure::version(),0,3) ){
			$function_name = 'load' . $kind ;
			if( function_exists( $function_name ) ){
					return $function_name( $name ) ;
			}else{
					return $this->errorGather('Qdmail<->CakePHP ' .$kind .' Load Error , the name is \'' . $name . '\'',__LINE__);
			}
		}else{
			return App::import( $kind , $name ) ;
		}
	}
	function cakeText( $content , $template = null , $layout = null , $org_charset = null , $target_charset = null , $enc = null , $wordwrap_length = null ){

		$this->template = is_null( $template ) ?  $this->template : $template ;
		$this->layout   = is_null( $layout )   ?  $this->layout : $layout ;

		list( $cont , $target_charset , $org_charset ) = $this->cakeRender( $content , 'TEXT' , $org_charset = null , $target_charset );
		return $this->text(  $cont , $wordwrap_length , $target_charset , $enc , $org_charset );
	}
	function cakeHtml( $content , $template = null , $layout = null , $org_charset = null , $target_charset = null , $enc = null ){

		$this->template = is_null( $template ) ?  $this->template : $template ;
		$this->layout   = is_null( $layout )   ?  $this->layout : $layout ;

		list( $cont , $target_charset , $org_charset ) = $this->cakeRender( $content , 'HTML' , $org_charset = null , $target_charset );
		return $this->html(  $cont , null , $target_charset , $enc , $org_charset  );
	}
	function cakeRender( $content , $type , $org_charset = null , $target_charset = null){

		if( is_null( $target_charset ) ){
			$target_charset = $this->charset_content;
		}
		if( !class_exists ( $this->Controller->view ) ){
			if( !$this->import( 'View' , $this->view ) ){
				return $this->errorGather('Qdmail<->CakePHP View Load Error , the name is \''.$this->view.'\'',__LINE__);
			}
		}
		$type = strtolower( $type );
		$view = new $this->Controller->view( $this->Controller , false );
		$view->layout = $this->layout;
		$mess = null;
		$content = $view->renderElement( $this->view_dir . DS . $type . DS . $this->template , array('content' => $content ) , true );
		if( 1.2 > (float) substr(Configure::version(),0,3) ){
			$view->subDir = $this->layout_dir . DS . $type . DS ;
		}else{
			$view->layoutPath = $this->layout_dir . DS . $type;
		}
		$mess .= $view->renderLayout( $content ) ;

		if( is_null( $org_charset ) ){
			$org_charset = $this->qd_detect_encoding( $mess );
		}
		$mess = $this->qd_convert_encoding( $mess , $target_charset , $org_charset );
		return array( $mess , $target_charset , $org_charset );
	}
	function CakePHP( $param ){
		$param = array_change_key_case( $param , CASE_LOWER );
		extract($param);
		if( isset($type) || 'HTML' == $type ){
			$type ='cakeHtml';
		}else{
			$type = 'cakeText';
		}
		return $this->{$type}( isset($content) ?  $content:null, isset($template) ?  $template:null , isset($layout) ?  $layout:null , isset($org_charset) ?  $org_charset: null , isset($target_charset) ? $target_charset:null , isset($enc) ?  $enc:null , isset($wordwrap_length) ? $wordwrap_length:null );
	}
}