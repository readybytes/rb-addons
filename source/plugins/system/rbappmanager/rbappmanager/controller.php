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
class PayInvoiceAdminControllerRbappmanager extends Rb_Controller
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
	
	public function addToCart()
	{
		$args = $this->_getArgs();
		
		$item_id = isset($args['item_id']) ? $args['item_id'] : 0;
		
		// no item-id provided to add in the cart
		if (!$item_id){
			// XITODO :
			return ;
		}

		$cart_items = $this->_helper->get('cart_items');
		
		$cart = $item_id;
		if(!empty($cart_items)){
			$cart = explode(',', $cart_items);
			
			if (!in_array($item_id, $cart)){
				$cart[] = $item_id;
			}
			
			$cart = implode(',', $cart);
		}
		
		$this->_helper->set(array('cart_items' => $cart));
		
		$response = Rb_Factory::getAjaxResponse();
		$response->addScriptCall('rbappmanager.cart.add_success', explode(',', $cart));
		$response->sendResponse();
	}
	
	public function removeFromCart()
	{
		$args = $this->_getArgs();
		
		$item_id = isset($args['item_id']) ? $args['item_id'] : 0;
		
		// 	no item-id provided to remove from the cart
		if (!$item_id){
			return ;
		}

		$added_items = $this->_helper->get('cart_items');
		$added_items = explode(",", $added_items);
		
		if(in_array($item_id, $added_items)){
			$added_items = array_diff($added_items, array($item_id));
			$this->_helper->set(array('cart_items' => implode(",", $added_items)));
		}
		
		$response = Rb_Factory::getAjaxResponse();
		$response->addScriptCall('rbappmanager.cart.remove_success', $added_items);
		$response->sendResponse();
	}
	
	public function checkout()
	{		
		$ajax_response = Rb_Factory::getAjaxResponse();
		$args = $this->_getArgs();

		$data = array();
		$data['paymart']['items'] = isset($args['items']) ? $args['items'] : array();
		$data['paymart']['buyer_email']	= $this->_helper->get('email');
		
		//XITODO : need to discuss, if we can send buyer email
		try{
			$user = (array) $this->_helper->get_user($data['paymart']['buyer_email']);
			$user = array_shift($user);
		}
		catch (Exception $e){
			//XITODO : what to do
			$ajax_response->sendResponse();
		}
		
		$data['paymart']['buyer_id']	= $user['buyer_id'];
		$data['paymart']['currency']	= 'USD';
		
		$url = JUri::getInstance();
		$data['paymart']['domain']	= $url->getHost();
		
		try{
			$invoice = $this->_helper->create_invoice($data);
		}
		catch (Exception $e){
			//XITODO : what to do
			$ajax_response->sendResponse();
		}
		
		// IMP :: as invoice has been created than clean the cart items
		// XITODO : cartitems should be deleted after invoice is paid
		$this->_helper->set(array('cart_items' => ''));
		$invoice = (array) $invoice;
		$invoice = array_shift($invoice);
		
		try{
			$url = $this->_helper->get_pay_url($invoice['invoice_id']);
		}
		catch (Exception $e){
			//XITODO : what to do
			$ajax_response->sendResponse();
		}
		
		$ajax_response->addScriptCall('rbappmanager.cart.redirect_to_pay',$url);
		$ajax_response->sendResponse();
		
	}
	
	public function install()
	{ 
		$ajax_response  = Rb_Factory::getAjaxResponse();
		$args  = $this->_getArgs();
	
		if(!isset($args['item_id']) || !isset($args['version_id'])){
			//XITODO :send error response
			$ajax_response->sendResponse();
		}
		
		try {
			$file = $this->_helper->get_version_file($args['item_id'], $args['version_id']);
		}
		catch (Exception $e){
			//XITODO : error handling
			$ajax_response->sendResponse();
		}
		
		$response = $this->_helper->install($file, $args['item_id'], $args['version_id']);
		
		$ajax_response->addScriptCall('rbappmanager.item.install_response',$response);
		$ajax_response->sendResponse();
	}

	public function credential()
	{
		$action = $this->input->get('action', 'check');
		$this->getView()->assign('action', $action);
		
		if($action == 'check'){
			$credential['email'] 		= $this->_helper->get('email');
			$credential['password'] 	= urlencode($this->_helper->get('password'));			
		}
		
		if($action == 'verify'){
			$credential = $this->_getArgs();					
		}
		
		$verified = false;
		if(isset($credential['email']) && isset($credential['password'])
			&& !empty($credential['email']) && !empty($credential['password'])){		
			// verify the credential from app server
			$response = $this->_helper->verify_crendetial($credential['email'], $credential['password']);
			if(!(is_array($response) && isset($response['error']) && $response['error']== true)){
				$verified = true;
			}
		}
		
		$this->getView()->assign('credential_verified', $verified);
		$this->getView()->assign('credential', $credential);
		
		return true;
	}
	
	public function registration()
	{
		$action = $this->input->get('action', 'check');
		$this->getView()->assign('action', $action);
		
		if($action == 'form'){
			return true;
		}
		
		if($action == 'register'){
			$args 		= $this->_getArgs();
			$email 		= $args['email'];			
			$password 	= $args['password'];
			$registered = false;
			
			$response = $this->_helper->register($email, urlencode($password));
			if(!(is_array($response) && isset($response['error']) && $response['error']== true)){
				$registered = true;
			}
			
			$this->getView()->assign('registered', $registered);
		}
	}
}