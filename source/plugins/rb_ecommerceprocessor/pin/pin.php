<?php

/**
 * @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * @package		Rb_EcommerceProcessor
 * @subpackage	Pin
 * @contact		support@readybytes.in
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgRb_ecommerceprocessorPin extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = dirname(__FILE__).'/processors/pin/pin.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorPin');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('pin', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorPin'));
		
		// load language file also
		$this->loadLanguage();	
	}
}
