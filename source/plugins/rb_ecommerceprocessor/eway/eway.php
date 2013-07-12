<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Eway
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @author Manisha Ranawat
 *
 */
class  plgRb_ecommerceprocessorEway extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = dirname(__FILE__).'/processors/eway/eway.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorEway');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('eway', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorEway'));
		
		// load language file also
		$this->loadLanguage();	
	}
}
