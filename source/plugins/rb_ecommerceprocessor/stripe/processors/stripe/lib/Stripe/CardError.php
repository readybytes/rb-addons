<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Stripe
* @contact		team@readybytes.in
*/

// no direct access
if(!defined( '_JEXEC' )){
	die( 'Restricted access' );
} 

class Stripe_CardError extends Stripe_Error
{
  public function __construct($message, $param, $code, $http_status=null, $http_body=null, $json_body=null)
  {
    parent::__construct($message, $http_status, $http_body, $json_body);
    $this->param = $param;
    $this->code = $code;
  }
}
