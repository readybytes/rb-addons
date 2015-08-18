<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Authorizenet
* @contact		team@readybytes.in
*/

// no direct access
if(!defined( '_JEXEC' )){
	die( 'Restricted access' );
}

/**
 * @author Neelam Soni
 *
 */
class  plgRb_ecommerceprocessorAuthorizenet extends RB_Plugin
{
	function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		$fileName = __DIR__.'/processors/authorize/authorize.php';
		Rb_HelperLoader::addAutoLoadFile($fileName, 'Rb_EcommerceProcessorAuthorize');
		
		$helper = Rb_EcommerceFactory::getHelper();
		$helper->processor->push('authorizenet', array('location' => $fileName, 'class' => 'Rb_EcommerceProcessorAuthorize'));
		
		// load language file also
		$this->loadLanguage();
	}
}
