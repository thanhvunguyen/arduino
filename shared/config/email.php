<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| メール基本設定
|--------------------------------------------------------------------------
*/
$config['protocol'] = 'smtp';
$config['smtp_port'] = '465';
$config['charset']  = 'UTF-8';
$config['wordwrap'] = FALSE;
$config['newline'] = "\r\n";

// Server Name
$config['smtp_host'] = 'ssl://smtp.gmail.com';
// SMTP Username
$config['smtp_user'] = 'thanhvunguyenbkdn@gmail.com';
// SMTP Password
$config['smtp_pass'] = 'thanhvu123!@#';