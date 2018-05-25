<?php
/**
 * Qdsmtp ver 0.2.0a
 * SMTP Talker
 *
 * PHP versions 4 and 5 (PHP4.3 upper)
 *
 * Copyright 2008, Spok in japan , tokyo
 * hal456.net/qdmail    :  http://hal456.net/qdsmtp/
 * & CPA-LAB/Technical  :  http://www.cpa-lab.com/tech/
 * Licensed under The MIT License License
 *
 * @copyright		Copyright 2008, Spok.
 * @link			http://hal456.net/qdsmtp/
 * @version			0.2.0a
 * @lastmodified	2008-10-25
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Qdsmtp is SMTP Taler library ,easy , quickly , usefull .
 * Copyright (C) 2008  spok
*/
namespace Qd;

class Qdsmtp extends QdsmtpBase
{
    function __construct( $param = null )
    {
        if(!is_null($param)):
            $param = func_get_args();
        endif;
        parent::__construct( $param );
    }
}