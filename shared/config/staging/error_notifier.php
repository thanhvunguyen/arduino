<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['sendable'] = TRUE;

$config['project'] = '';
$config['template_path'] = "";
$config['masked_parameters'] = ['password'];

// For Slack
$config['driver'] = 'slack';
$config['from'] = '';
$config['web_hook'] = '';
$config['channel'] = '';

