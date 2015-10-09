<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.bluepay
* @contact		team@readybytes.in
*/

// no direct access
if(!defined( '_JEXEC' )){
	die( 'Restricted access' );
}

/** 
 * Bluepay 
 * @author Garima Agal
 */
class Rb_EcommerceProcessorBluepay extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;
	protected $_support_refund = true;
	public function __construct($config = array())
		{
			parent::__construct($config);
		}
		
	
// Handle the request and call the function according to request type
	public function request(Rb_EcommerceRequest $request)
	{
		require_once dirname(__FILE__).'/BluePay.php'; 
	   
		// API credentials only need to be defined once
		(!defined("BLUEPAY_ACCOUNT_ID")) 	? define("BLUEPAY_ACCOUNT_ID", 	$this->getConfig()->account_id) : '';    // Add your API LOGIN ID
		(!defined("BLUEPAY_SECRET_KEY")) 	? define("BLUEPAY_SECRET_KEY", 	$this->getConfig()->secret_key): ''; // Add your API transaction key
		
		$isSandbox = ($this->getConfig()->sandbox)?'TEST':'LIVE';
		
		(!defined("BLUEPAY_SANDBOX")) 		? define("BLUEPAY_SANDBOX", 	$isSandbox): '';       // Set to false to test against production
		
		$type = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	public function _request_refund(Rb_EcommerceRequest $request)
	{
		$response = new stdClass();
		$object = $request->toObject();
		
		$config = $this->getConfig(false);	
		$paymentRefund = new BluePay(BLUEPAY_ACCOUNT_ID,BLUEPAY_SECRET_KEY,BLUEPAY_SANDBOX);

	    $paymentRefund->refund($object->post_data->txn_id, $object->post_data->amount);
	
	    $paymentRefund->process();
	    
	    $response->data	= $paymentRefund;
	    $response->type = "refund";
	    $response->amount 		= number_format($object->post_data->amount , 2);
		
		return $response;
	}
	
	// Build the form in front-end to submit payment credentials and user details to payment gateway
	protected function _request_build(Rb_EcommerceRequest $request)
	{				
		$response 					= new stdClass();
		$response->error 			= false;
		$response->type				= Rb_EcommerceRequest::BUILD_TYPE_HTML ;
		
		$response->data 			= new stdClass();
		$response->data->post_url 	= false;		
		$object 					= $request->toObject();		
		
		$response->data->form		= Rb_HelperTemplate::renderLayout('gateway_bluepay', $object->user_data,  'plugins/rb_ecommerceprocessor/bluepay/processors/bluepay/layouts');
		
		return $response;
	}
	
protected function _request_payment(Rb_EcommerceRequest $request)
	{
		// This code is for non-recurring payment only

		$object 			= $request->toObject();		
		$processor_data 	= $object->processor_data;		
		$config 			= $this->getConfig(false);
		$user_data			= $object->user_data;
		$post_data			= $object->post_data;
		$payment_data		= $object->payment_data;
		
		// Response
		$response 			= new stdClass();
		$response->error 	= false;
		
		$payment = new BluePay(BLUEPAY_ACCOUNT_ID,BLUEPAY_SECRET_KEY,BLUEPAY_SANDBOX);
		
		// Set request specific fields
		$transactionData = array(
								'firstName'			=> $post_data->first_name,
								'lastName' 			=> $post_data->last_name, 
								'addr1'				=> $post_data->address,
								'city'				=> $post_data->city,
								'state'				=> $post_data->state,
								'country'			=> $post_data->country,
								'zip'				=> $post_data->zipcode,
								'email'				=> $post_data->email,
								'phone'				=> $post_data->phone_number,
		);
		
		
		$payment->setCustomerInformation($transactionData);
		
		$payment->setCustomerInformation($transactionData);

		$payment->setInvoiceID($payment_data->invoice_number);
		
		$payment->setCCInformation(array(
									    'cardNumber' => trim($post_data->card_number), // Card Number: 4111111111111111
									    'cardExpire' => trim($post_data->expiration_month.substr($post_data->expiration_year, -2)), // Card Expire: 12/15
									    'cvv2' 		 => $post_data->card_code // Card CVV2: 123
									));
									
//		$payment->auth(number_format($payment_data->total , 2)); // Card authorization
									 
		$payment->sale(number_format($payment_data->total , 2, '.', '')); 
		 
		// Makes the API request with BluePAy
		$payment->process();
		
		$transactionId = $payment->getTransID();
		$status 	   = $payment->getStatus();
	  	
	    // if same notification came more than one time
		// Check if transaction already exists,if yes then do nothing and return
		$txn_id 		= isset($transactionId) 	? $transactionId : 0;
		$subscr_id		= 0;
		$parent_id		= 0;
		
		$transactions 	= $this->_getExistingTransaction($payment_data->invoice_id, $txn_id, $subscr_id, $parent_id);
		if (!empty($transactions) && is_array($transactions)) {
			foreach ($transactions as $transaction) {
    			if($transaction->response_code == $status){
					return false;
				}
    		}
		}
		
		$response->data 		= $payment;
		$response->type 		= "credit";
		$response->amount 		= number_format($payment_data->total , 2);
		return $response;		
	}
	
	
	
	protected function _getExistingTransaction($invoiceid, $txn_id, $subscr_id, $parent_txn)
		{
			// if all arguments are empty or then return exists
			if(empty($txn_id) && empty($subscr_id) && empty($parent_txn)){
				return true;
			}
			
			$filter = array();
			$filter['invoice_id']			= $invoiceid;
			$filter['gateway_txn_id'] 		= $txn_id; 
			$filter['gateway_subscr_id'] 	= $subscr_id;
			$filter['gateway_parent_txn'] 	= $parent_txn;
			
			$result = Rb_EcommerceAPI::transaction_get_records($filter, array());
	
			if(count($result)){
				return $result;
			}
			
			return false;
		}
		
	
	// Process the response
	public function process($response)
	{	
		$data 			= $response->data;
		$type			= $response->type;
		/* Response code details
		*	'Approved' -> This transaction has been approved.
		*	'Declined' -> This transaction has been declined.
		*	'Error'    -> There has been an error processing this transaction.
		*/
		if(!$data instanceof BluePay){
			return $response;
		}
		
		$status  = strtolower($data->getStatus());
		if($type == "refund")
		{
			switch($status)
			{
				case 'approved':
						return $this->_process_approved_refund_response($response);	
						
				case 'declined':					
				case 'error':
				default:
					return $this->_process_error_notify_refund_response($response);
			}
		}
		
		switch($status)
		{
			case 'approved':
					return $this->_process_approved_response($response);	
					
			case 'declined':					
			case 'error':
			default:
					return $this->_process_error_notify_response($response);
		}
	}
	
	// Transaction has occured successfully
	protected function _process_approved_response($req_response)
	{
		$payment = $req_response->data;
		$notification = $this->getParams($payment);
		$response = new Rb_EcommerceResponse();
		$response->set('amount', 			$req_response->amount)
				 ->set('txn_id', 			$payment->getTransID())
				 ->set('gateway_subscr_id', 0)
				 ->set('gateway_parent_txn',0)
				 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_COMPLETE)
				 ->set('message',			$payment->getMessage())
				 ->set('params', 			$notification);
		
		return $response;
	}
	
	// Some error has occurred during transaction 
	protected function _process_error_notify_response($req_response)
	{
		$payment = $req_response->data;
		$notification = $this->getParams($payment);
		
		$response = new Rb_EcommerceResponse();
		$response->set('amount', 			0)
				 ->set('txn_id', 			$payment->getTransID())
				 ->set('gateway_subscr_id', 0)
				 ->set('gateway_parent_txn',0)
				 ->set('message',			$payment->getMessage())
				 ->set('payment_status', 	Rb_EcommerceResponse::FAIL)
				 ->set('params', 			$notification);
	
		return $response;
	}
	
	protected function _process_approved_refund_response($req_response)
	{
		$payment = $req_response->data;
		$notification = $this->getParams($payment);
		
		$response = new Rb_EcommerceResponse();
		$response->set('amount', 			-$req_response->amount)
				 ->set('txn_id', 			$payment->getTransID())
				 ->set('gateway_subscr_id', 0)
				 ->set('gateway_parent_txn',0)
				 ->set('message',			$payment->getMessage())
				 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_REFUND)
				 ->set('params', 			$notification);
				
		return $response;
	}
	
	protected function _process_error_notify_refund_response($req_response)
	{
		$payment = $req_response->data;
		$notification = $this->getParams($payment);
		
		$response = new Rb_EcommerceResponse();
		$response->set('amount', 			0)
				 ->set('txn_id', 			$payment->getTransID())
				 ->set('gateway_subscr_id', 0)
				 ->set('gateway_parent_txn',0)
				 ->set('message',			$payment->getMessage())
				 ->set('payment_status', 	Rb_EcommerceResponse::FAIL)
				 ->set('params', 			$notification);
				
		return $response;
	}
	
	public function getParams($payment)
	{
		$params  = $payment->getResponse();
		$params  = strstr($params, '?');
		$params	 = explode("&", $params);
		
		foreach ($params as $param){
				$data = explode("=", $param);
				$data[0] = trim($data[0],'?');
				$notification[$data[0]] = urldecode($data[1]);
		}
		return $notification;
	}
}