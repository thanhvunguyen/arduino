<?php
/**
 * Qdmail ver 1.2.6b
 * E-Mail for multibyte charset
 *
 * PHP version 7
 *
 * Copyright 2008, Spok in japan , tokyo
 * hal456.net/qdmail    :  http://hal456.net/qdmail/
 * & CPA-LAB/Technical  :  http://www.cpa-lab.com/tech/
 * Licensed under The MIT License License
 *
 * @copyright		Copyright 2008, Spok.
 * @link			http://hal456.net/qdmail/
 * @version			1.2.6b
 * @lastmodified	2008-10-23
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Qdmail is sending e-mail library for multibyte language ,
 * easy , quickly , usefull , and you can specify deeply the details.
 * Copyright (C) 2008   spok
*/
namespace Qd;

if (!defined('QD_DS')) {
	define('QD_DS', DIRECTORY_SEPARATOR);
}

if( !function_exists( 'qd_send_mail' ) ){

	function qd_send_mail( $type , $to = null, $subject = null , $content = null , $other_header = array() , $attach = null, $debug = 0 ){
		$type_org = $type;

		$mail = & Qdmail::getInstance();
		$mail->debug = $debug;
		if(!is_array($type)){
			$type = array('TYPE'=>$type);
		}
		list( $type , $link ) = $mail->keyUpper($type);
		$option = array();
		$return = array();
		$type = array_change_key_case( $type , CASE_UPPER ) ;
		$option = (isset($type['OPTION']) && is_array($type['OPTION'])) ? $type['OPTION'] : array();			$return = (isset($type['RETURN']) && is_array($type['RETURN'])) ? $type['RETURN'] : array();
		if(isset($type['SMTP'])){
			$option = array_merge($option,array('SMTP'=>true,'smtpServer'=>$type['SMTP']));
		}
		$type = isset($type['TYPE']) ? $type['TYPE']:'text';
		$_type=array('TEXT'=>'Text','HTML'=>'Html','DECO'=>'Deco' ,'DECOTEMPLATE'=>'DecoTemplate');
		$easy_method = isset($_type[strtoupper($type)]) ? 'easy'.$_type[strtoupper($type)]:'_';

		if(!method_exists($mail,$easy_method)){
			$mail -> errorGather('Illegal type \''.$type.'\'',__LINE__);
			return false;
		}

		$ret = $mail->{$easy_method}( $to , $subject , $content , $other_header , $attach , $option );

		foreach($return as $method => $value ){
			if(method_exists($mail,$method)){
				$type_org[$link['RETURN']][$method] = $mail -> {$method}($value);
			}
		}
		if(0!==count($return)){
			$type_org[$link['RETURN']]['qd_send_mail'] = $ret;
			$ret = $type_org;
		}

		return $ret;
	}
}

class Qdmail extends QdmailUserFunc
{
	var $name ='Qdmail';

	function __construct( $param = null )
    {
		if( !is_null($param)):
			$param = func_get_args();
		endif;
		parent::__construct( $param );
	}
}