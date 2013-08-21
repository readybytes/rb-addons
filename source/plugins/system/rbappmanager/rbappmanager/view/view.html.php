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
		$email = $this->_helper->get('email');
		if(!empty($email)){
			// get user from email
			try{
				$user 				= (array) $this->_helper->get_user($email);
				$user 				= array_shift($user);				
			}
			catch (Exception $e){
				Rb_Error::assert(false, 'User Not found', Rb_Error::ERROR);
				// XITODO : pop up registration window 
				exit;
			}
		}
		
		
		$component_name = "com_payinvoice";
		
		$app_data 	 = $this->_helper->get_items($component_name, $user);
		$added_items = $this->_helper->get('cart_items');
		
		
		if(!empty($added_items)){
			$added_items = explode(",", $added_items);
			$added_items = array_combine($added_items, $added_items);
		}
		 
		$this->assign('helper', $this->_helper);
		$this->assign('added_items', 	 $added_items);
		$this->assign('data', 		 $app_data);
		
		// XITODO : get component name
		// IMP : Tag will not contain "_" so use "-"
		$this->assign('default_tag', 	 'com-payinvoice');
		return true;
	} 
}