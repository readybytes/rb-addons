<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Bankaudi
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class  plgRb_ecommerceprocessorBankaudi extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = dirname(__FILE__).'/processors/bankaudi/bankaudi.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorBankaudi');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('bankaudi', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorBankaudi'));
		
		// load language file also
		$this->loadLanguage();	
	}
}
