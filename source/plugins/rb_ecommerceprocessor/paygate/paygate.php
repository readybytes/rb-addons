<?php

/**
* @copyright	Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Paygate
* @contact		support@readybytes.in
*/

// no direct access
if(!defined( '_JEXEC' )){
	die( 'Restricted access' );
}

/**
 * @author Bhavya Shaktawat
 *
 */
class  plgRb_ecommerceprocessorPaygate extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		
		$fileName = __DIR__.'/processors/paygate/paygate.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorPaygate');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('paygate', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorPaygate'));
		
		// load language file also
		$this->loadLanguage();
	}
}
