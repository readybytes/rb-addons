<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.CCBill
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class  plgRb_ecommerceprocessorCCBill extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = dirname(__FILE__).'/processors/ccbill/ccbill.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorCCBill');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('ccbill', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorCCBill'));
		
		// load language file also
		$this->loadLanguage();	
	}
}
