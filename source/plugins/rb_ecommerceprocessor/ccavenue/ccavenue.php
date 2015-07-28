<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.ccavenue
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or	die( 'Restricted access' );

/**
 * @author Team Readybytes
 *
 */
class  plgRb_ecommerceprocessorCcavenue extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		
		$fileName = __DIR__.'/processors/ccavenue/ccavenue.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorCcavenue');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('ccavenue', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorCcavenue'));
		
		// load language file also
		$this->loadLanguage();
	}
}
