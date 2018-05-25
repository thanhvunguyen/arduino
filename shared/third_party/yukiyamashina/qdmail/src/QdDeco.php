<?php
namespace Qd;

class QdDeco
{
	var $template = null;
	var $data = array();

	function template( $template = null ){
		if(is_null($template)){
			return $this->template;
		}
		$this->template = trim(preg_replace("/\\r?\\n/is","\r\n",$template));
		return true;
	}

	function get( $kind ){
		if(!empty($this->data[$kind])){
			return $this->data[$kind];
		}
		if('ATTACH'===$kind){
			return array();
		}else{
			return null;
		}
	}

	function decode(){

		if(!class_exists('QdmailReceiver')){
			include('qd_mail_receiver.php');
		}

		$num_boundary = strpos(strtolower($this->template),'boundary');
		$num_crlf     = strpos($this->template,"\r\n\r\n");
		$template = $this->template;

		while((false!==$num_boundary)&&(false!==$num_crlf)&&($num_boundary > $num_crlf)){
			$template = substr($template,$num_crlf+4);
			$num_crlf = strpos($template,"\r\n\r\n");
			$num_boundary = strpos(strtolower($template),'boundary');
		}

		$receiver = QdmailReceiver::start( 'direct' , $template );
		$this->data['HTML'] = $receiver->bodyAutoSelect() ;
		if(false===$this->data['HTML']){
			$this->data['HTML'] = '';
		}
		$attach = $receiver->attach();
		foreach($attach as $att){
			if(isset($att['content-id'])){
				$cid = rtrim($att['content-id'],'>');
				$cid = ltrim($cid,'<');
			}elseif(isset($att['content_id'])){
				$cid = rtrim($att['content_id'],'>');
				$cid = ltrim($cid,'<');
			}else{
				$cid = null;
			}
			$this->data['ATTACH'][]=array(
				'PATH'			=> $att['filename_safe'],
				'NAME'			=> $att['filename_safe'],
				'CONTENT-TYPE'	=> $att['mimetype'],
				'CONTENT-ID'	=> $cid,
				'_CHARSET'		=> null,
				'_ORG_CHARSET'	=> null,
				'DIRECT'		=> $att['value'],
				'BARE'			=> false,
			);
		}
	return true;
	}
}