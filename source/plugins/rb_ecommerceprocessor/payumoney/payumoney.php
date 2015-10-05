<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Payumoney
* @contact		support@readybytes.in
*/

// no direct access
if(!defined( '_JEXEC' )){
	die( 'Restricted access' );
}

/**
 * @author Neelam Soni
 *
 */
class  plgRb_ecommerceprocessorPayumoney extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = __DIR__.'/processors/payumoney/payumoney.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorPayumoney');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('payumoney', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorPayumoney'));
		
		// load language file also
		$this->loadLanguage();
	}
}
