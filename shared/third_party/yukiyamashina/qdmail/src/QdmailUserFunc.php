<?php
namespace Qd;

class QdmailUserFunc extends QdmailBase
{

    function __construct( $param = null )
    {
        parent::__construct( $param );
    }

    function validateAddr( $addr )
    {
        if (0==preg_match( $this->varidate_address_regex , $addr , $match )):
            $this->errorGather('Tyr Varidate Error by regex preg_match(\''.$this->varidate_address_regex . '\') the address is ->'.$addr,__LINE__);
        else:
            return true;
        endif;
    }

    function stripCrlf( $word )
    {
        if ( $this->force_change_charset ):
            $enc = $this->qd_detect_encoding( $word ) ;
            $word = $this->qd_convert_encoding( $word , $this->qdmail_system_charset , $enc );
        endif;
        $word = preg_replace( '/\r?\n/i' , '' , $word );
        if ( $this->force_change_charset ):
            $word = $this->qd_convert_encoding( $word , $enc , $this->qdmail_system_charset );
        endif;
        return $word ;
    }
}