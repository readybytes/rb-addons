<?php

/**
* @copyright	Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.MoneyBookers
* @contact		support+payinvoice@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @author Manisha Ranawat
 *
 */
class  plgRb_ecommerceprocessorMoneybookers extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = dirname(__FILE__).'/processors/moneybookers/moneybookers.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorMoneybookers');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('moneybookers', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorMoneybookers'));
		
		// load language file also
		$this->loadLanguage();	
	}
}
