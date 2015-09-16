<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.bluepay
* @contact		support@readybytes.in
*/

// no direct access
if(!defined( '_JEXEC' )){
	die( 'Restricted access' );
}

/**
 * @author Garima agal
 *
 */
class  plgRb_ecommerceprocessorBluepay extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = __DIR__.'/processors/bluepay/bluepay.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorBluepay');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('bluepay', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorBluepay'));
		
		// load language file also
		$this->loadLanguage();
	}
}
