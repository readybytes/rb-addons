<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.2checkout
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @author Rimjhim Jain
 *
 */
class  plgRb_ecommerceprocessor2checkout extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = dirname(__FILE__).'/processors/2checkout/2checkout.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessor2checkout');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('2checkout', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessor2checkout'));
		
		// load language file also
		$this->loadLanguage();	
	}
}
