<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYINVOICE
* @subpackage	Back-end
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * App Manager Controller
 * @author Gaurav Jain
 */
class PayInvoiceAdminControllerRbappmanager extends PayInvoiceController
{
	/**
	 * @var RbappmanagerHelper
	 */
	public $_helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->_helper = $this->getHelper();
	}
	
	public function getModel()
	{
		return null;
	}
	
	public function getHelper($name = '')
	{
		if(!$this->_helper){
			$this->_helper = new RbappmanagerHelper(); 
		}
		
		return $this->_helper;
	}
}