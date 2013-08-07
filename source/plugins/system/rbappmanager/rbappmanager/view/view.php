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
 * App Manager Base View
 * @author Gaurav Jain
 */
class PayInvoiceAdminBaseViewRbappmanager extends PayInvoiceView
{
	/**
	 * @var RbappmanagerHelper
	 */
	public $_helper = null;	
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->addPathToTemplate(dirname(__FILE__).'/tmpl');
		$this->_helper = $this->getHelper();
	}
		
	public function getHelper($name = '')
	{
		if(!$this->_helper){
			$this->_helper = new RbappmanagerHelper(); 
		}
		
		return $this->_helper;
	}
	public function display($tpl = null)
	{
		return true;
	}
	
	public function _basicFormSetup($task)
	{
		return true;
	}
		
	public function _adminSubmenu($selMenu = 'dashboard')
	{
		
		return $this;
	}
}