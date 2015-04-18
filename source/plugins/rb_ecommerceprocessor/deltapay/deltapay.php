<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.DeltaPay 
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class  plgRb_ecommerceprocessorDeltapay  extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = dirname(__FILE__).'/processors/deltapay/deltapay.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorDeltapay');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('deltapay', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorDeltapay'));
		
		// load language file also
		$this->loadLanguage();	
	}
}
