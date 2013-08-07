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
 * App Manager Html View
 * @author Gaurav Jain
 */
require_once dirname(__FILE__).'/view.php';
class PayInvoiceAdminViewRbappmanager extends PayInvoiceAdminBaseViewRbappmanager
{
	protected function _adminToolbar()
	{
		$this->_adminToolbarTitle();
	}
	
	public function display($tpl = null)
	{
		$app_data 	 = $this->_helper->get_items();
//		$added_items = $this->_helper->get('cart_items');
		
		$email = $this->_helper->get('email');
		$accessible_items = array();
		if(!empty($email)){
			// get user from email
			try{
				$user 				= (array) $this->_helper->get_user($email);
				$user 				= array_shift($user);				
				$accessible_items 	= (array)$this->_helper->get_accessible_items($user['buyer_id']);											
			}
			catch (Exception $e){
				$accessible_items = array();
			}			
		}
		
		if(!empty($added_items)){
			$added_items = explode(",", $added_items);
		}
		 
		$this->assign('helper', $this->_helper);
//		$this->assign('added_items', 	 $added_items);
		$this->assign('app_data', 		 $app_data);
		
		// XITODO : get component name
		$this->assign('default_tag', 	 'com_payinvoice');
		$this->assign('accessible_items',$accessible_items);
		return true;
	} 
}