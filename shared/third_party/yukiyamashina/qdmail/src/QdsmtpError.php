<?php
namespace Qd;

class QdsmtpError extends QdsmtpBranch
{

	var $name = 'QdsmtpError';
	var $error_display		= true;
	var $errorlog_level		= 0;
	var $log_level			= 0;
	var $error				= array() ;
	var $error_stack		= array();
	var $log_LFC			= "\r\n";
	var $log_append			= 'a';
	var $errorlog_append	= 'a';
	var $log_filename		='qdsmtp.log';
	var $errorlog_filename	='qdsmtp_error.log';
	var $log_dateformat		= 'Y-m-d H:i:s';
	var $error_ignore		= false;

	function errorRender( $error = null , $lf = null , $display = true ){
		if( is_null( $error ) ){
			$error = $this->error;
		}
		if( is_null( $lf ) ){
			$lf = $this->log_LFC ;
		}
		if( !is_array( $error ) ){
			$error = array( $error );
		}
		$out = null ;
		foreach($error as $mes){
			$out .= $this->name . ' error: ' . trim( $mes ) . $lf ;
		}
		if( $this->error_display && $display ){
			$_out = str_replace( $lf ,'<br>' . $lf , $out );
			echo  $_out ;
		}
		return $out ;
	}

	function errorGather( $message = null , $line = null){

		if( !is_null( $message ) && !$this->error_ignore){
			if( !is_null( $line ) ){
				$message .= ' line -> '.$line;
			}
			$this->error[] = $message ;
		}elseif( 0 === count( $this->error ) ){
			return true;
		}elseif( $this->error_ignore ){
			return false;
		}

		$er = $this->errorRender();
		$this->error_stack = array_merge( $this->error_stack , $this->error );
		$this->error = array();
		if( !$this->logWrite( 'error' ,  $er )){
			$this->error_stack = array_merge( $this->error_stack , $this->error );
		}
		return false;
	}

	function logWrite( $type , $message ){
		$tp = ('error' == $type) ? false:true;
		$level		=	$tp ? $this->log_level:$this->errorlog_level;
		if( 0 == $level ){
			return true;
		}
		$filename	=	$tp ? $this->log_filename:$this->errorlog_filename;
		$ap			=	$tp ? $this->log_append:$this->errorlog_append;
		$fp = fopen( $filename , $ap );
		if( !is_resource( $fp ) ){
			$this->error[]='file open error at logWrite() line->'.__LINE__;
			return false;
		}
		$spacer = $tp ? $this->log_LFC : $this->log_LFC ;
		fwrite( $fp ,
			date( $this->log_dateformat )
			. $spacer
			. trim( $message )
			. $this->log_LFC
		);
		fclose( $fp ) ;
		return true ;
	}
	function log(){
		$mes = null;
		foreach($this->smtp_log as $line){
			$mes .= trim( $line ) . $this->log_LFC;
		}
		$this->logWrite( null ,$mes );
		$this->smtp_log = array();
	}
	function logFilename( $data = null ){
		if( is_null( $data ) ){
			return $this->log_filename;
		}
		if( is_string( $data ) ){
			$this->log_filename = $data;
			return $this->errorGather();
		}else{
			return $this->errorGather('Data specified error',__LINE__);
		}
	}
	function errorlogFilename( $data = null ){
		if( is_null( $data ) ){
			return $this->errorlog_filename;
		}
		if( is_string( $data ) ){
			$this->errorlog_filename = $data;
			return $this->errorGather();
		}else{
			return $this->errorGather('Data specified error',__LINE__);
		}
	}
}