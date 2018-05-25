<?php
namespace Qd;

class sfQdmail extends QdmailUserFunc
{

	var $framework = 'Symfony';

	function __construct( $param = null ){
		if( !is_null($param)){
			$param = func_get_args();
		}
		parent::__construct( $param );
	}

	function setBody( $body ){
		if('HTML'===$this->is_html){
			$this->html( $body );
		}else{
			$this->text( $body );
		}
	}
	function getAltBody(){

		if('HTML'===$this->is_html){
			$content=$this->body();
			return !empty($content['TEXT']['CONTENT']);
		}else{
			return false;
		}
	}
	function setAltBody( $body ){
		$this->text( $body );
	}
	function addStringAttachment($data, $attach_name , $mime_type){
		$this->attachDirect( $attach_name , $data , $add = true , $mime_type );
	}
	function getRawHeader(){
		return $this->header_for_smtp;
	}
	function getRawBody(){
		return $this->$this->content_for_mailfunction;
	}

	function initialize(){
		$this->reset();
	}
	function setCharset($charset){
    	$this->charset($charset);
	}
	function getCharset(){
 	   $ret = $this->charset();
		return $ret['TEXT'];
	}
	function setContentType($content_type){
		if(false===strpos(strtoupper($content_type),'HTML')){
			$this->is_html = 'TEXT';
		}else{
			$this->is_html = 'HTML';
		}
	}
	function getContentType(){
		if('HTML'===$this->is_html){
			return 'text/html';
		}else{
			return 'text/plain';
		}
	}
	function setPriority($priority){
		$pri = array(1=>'high',3=>'normal',5=>'low');
		if(isset($pri[$priority])){
			$this->priority($pri[$priority]);
			return true;
		}else{
			return false;
		}
	}
	function getPriority(){
		$pri = array('HIGH'=>1,'NORMAL'=>3,'LOW'=>5);
		$now_priority = strtoupper($this->priority());
		if(empty($now_priority)){
			return null;
		}
		return $pri[$now_priority];
	}
	function setEncoding($encoding){
		$this->encoding($encoding);
	}
	function getEncoding(){
		return $this->encoding();
	}
	function setSubject($subject){
		$this->subject($subject);
	}
	function getSubject(){
		return $this->subject();
	}
	function getBody(){
		$content=$this->body();
		if('HTML'===$this->is_html){
			return $content['HTML']['CONTENT'];
		}else{
			return $content['TEXT']['CONTENT'];
		}
	}
	function setMailer($type = 'mail', $options = array()){
	switch ($type){
	case 'smtp':
		$this->smtp = true;
		$this->sendmail = false;
		$this->mailer = 'smtp';
		if (isset($options['keep_alive'])){
			 $this->keepParameter(true);
		}
        break;
      case 'sendmail':
			$this->sendmail = true;
			$this->smtp = false;
			$this->mailer = 'sendmail';
        break;
      default:
    		$this->smtp = false;
    		$this->sendmail = false;
			$this->mailer = 'mail';
        break;
	  	}
	}
	function getMailer(){
		return $this->mailer;
	}
	function setSender($address, $name = null){
		$this->addHeader( 'Return-Path' , $address );
	}
	function getSender(){
		return isset($this->other_header['Return-Path']) ? $this->other_header['Return-Path']:null;
	}
	function setFrom($address, $name = null){
		$this->from( $address , $name );
	}
	function addAddresses($addresses){
		$this->to( $addresses , null , true );
	}
	function addAddress($address, $name = null){
		$this->to( $address , $name , true );
	}
	function addCc($address, $name = null){
		$this->cc( $address , $name , true );
	}
	function addBcc($address, $name = null){
		$this->cc( $address , $name , true );
	}
	function addReplyTo($address, $name = null){
		$this->replyto( $address , $name , true );
	}
	function clearAddresses(){
		$this->to =array();
	}
	function clearCcs(){
		$this->cc =array();
	}
	function clearBccs(){
		$this->bcc =array();
	}
	function clearReplyTos(){
		$this->replyto =array();
	}
	function clearAllRecipients(){
		$this->clearAddresses();
		$this->clearCcs();
		$this->clearBccs();
		$this->clearReplyTos();
	}
	function addAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream'){
		return $this->attach(array('PATH'=>$path,'NAME'=>$name,'MIME-TYPE'=>$type),true);
	}
	function addEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream'){
		return $this->attach(array('PATH'=>$path,'NAME'=>$name,'MIME-TYPE'=>$type,'CONTENT-ID'=>$cid),true);
	}
	function setAttachments($attachments){}
	function clearAttachments(){
		$this->attach=array();
	}
	function addCustomHeader($name, $value){
		$this->addHeader($name, $value);
	}
	function clearCustomHeaders(){
		$this->other_header=array();
	}
	function prepare(){}
	function smtpClose(){}
	function setDomain($hostname){}
	function getDomain(){}
	function setHostname($hostname){}
	function getHostname(){}
	function setPort($port){}
	function getPort(){}
	function setUsername($username){}
	function getUsername(){}
	function setPassword($password){}
	function getPassword(){}
	function setWordWrap($wordWrap){}
	function getWordWrap(){}
}