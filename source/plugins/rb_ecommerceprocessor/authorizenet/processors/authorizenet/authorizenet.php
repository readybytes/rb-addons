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
 * Authorize CIM Processor 
 * @author Neelam Soni
 */
class Rb_EcommerceProcessorAuthorizenet extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;
	
	// If Payment method support for refund then set it true otherwise set flase
	protected $_support_refund = true;
	
	public function __construct($config = array())
	{
		parent::__construct($config);

	}
	
	// Handle the request and call the function according to request type
	public function request(Rb_EcommerceRequest $request)
	{
		require_once dirname(__FILE__).'/AuthorizeNet.php'; 
	   
		// API credentials only need to be defined once
		(!defined("AUTHORIZENET_API_LOGIN_ID")) 	? define("AUTHORIZENET_API_LOGIN_ID", 		$this->getConfig()->api_login_id) : '';    // Add your API LOGIN ID
		(!defined("AUTHORIZENET_TRANSACTION_KEY")) 	? define("AUTHORIZENET_TRANSACTION_KEY", 	$this->getConfig()->transaction_key): ''; // Add your API transaction key
		(!defined("AUTHORIZENET_SANDBOX")) 			? define("AUTHORIZENET_SANDBOX", 			$this->getConfig()->sandbox): '';       // Set to false to test against production
		(!defined("TEST_REQUEST")) 					? define("TEST_REQUEST", "FALSE"): '';           // You may want to set to true if testing against production
		
		$type = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	// Build the form in front-end to submit payment credentials and user details to payment gateway
	protected function _request_build(Rb_EcommerceRequest $request)
	{				
		$response 					= new stdClass();
		$response->error 			= false;
		$response->type				= Rb_EcommerceRequest::BUILD_TYPE_HTML ;
		
		$response->data 			= new stdClass();
		$response->data->post_url 	= false;		
		$response->data->form		= Rb_HelperTemplate::renderLayout('gateway_authorizenet', null,  'plugins/rb_ecommerceprocessor/authorizenet/processors/authorizenet/layouts');
		
		return $response;
	}
	
	// Code to request payment; method used is AUTH_CAPTURE (Payment is settled if authorization succeeds in single request only)
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
		
		// Set request specific fields
		$transactionData = array(
								'type'				=> 'auth_capture' ,
								'amount'			=> number_format($payment_data->total , 2),
								'card_num'			=> trim($post_data->card_number),
								'exp_date'			=> trim($post_data->expiration_year.'-'.str_pad($post_data->expiration_month, 2, '0', STR_PAD_LEFT)),
								'card_code'			=> $post_data->card_code,
								'cust_id'			=> $user_data->id,
								'first_name'		=> $post_data->first_name,
								'last_name'			=> $post_data->last_name,
								'address'			=> $post_data->address,
								'city'				=> $post_data->city,
								'state'				=> $post_data->state,
								'country'			=> $post_data->country,
								'zip'				=> $post_data->zip,
								'email'				=> $post_data->email,
								'phone'				=> $post_data->mobile,
								'invoice_num'		=> $payment_data->invoice_number,
								'description'		=> $payment_data->item_name
		);
		
		$transaction = new AuthorizeNetAIM();
		$transaction->setSandbox($config->sandbox , 0);
		$transaction->setFields($transactionData);
		
		$aim_response 			= $transaction->authorizeAndCapture(); 
	  	
	    // if same notification came more than one time
		// Check if transaction already exists,if yes then do nothing and return
		$txn_id 		= isset($aim_response->transaction_id) 	? $aim_response->transaction_id : 0;
		$subscr_id		= 0;
		$parent_id		= 0;
		
		$transactions 	= $this->_getExistingTransaction($payment_data->invoice_id, $txn_id, $subscr_id, $parent_id);
		if (!empty($transactions) && is_array($transactions)) {
			foreach ($transactions as $transaction) {
    			if($transaction->response_code == $aim_response->response_code){
					return false;
				}
    		}
		}
		
		$response->data 		= $aim_response;
		$response->card_number	= $aim_response->account_number;
		return $response;		
	}
	
	// Code to refund any payment
	protected function _request_refund(Rb_EcommerceRequest $request)
	{
		$object 		= $request->toObject();		
		$processor_data = $object->processor_data;
		$post_data		= $object->post_data;		
		$config 		= $this->getConfig(false);
		
		$response = new stdClass();		
		
		$transactionData = array(
							'type'				=> 'credit' ,
							'amount'			=> number_format($processor_data->amount , 2),
							'card_num'			=> $processor_data->card_number,
							'trans_id'			=> $post_data->txn_id,
							'exp_date'			=> ''
							);
							
		$transaction = new AuthorizeNetAIM();
		$transaction->setSandbox($config->sandbox , 0);
		$transaction->setFields($transactionData);
		
		$response->data		= $transaction->credit();

		if(!$response->data->approved){
			$transactionData = array( 'type'			=> 'void',
									  'trans_id'		=> $post_data->txn_id);
			$response->data = $transaction->void();
		}
		
		return $response;
	}
	
	// Need to verify once we provide recurring support
//	protected function _request_cancel(Rb_EcommerceRequest $request)
//	{
//		$object 			= $request->toObject();			
//		$config 			= $this->getConfig(false);	
//		$processor_data 	= $object->processor_data;
//		$payment_data		= $object->payment_data;
//		$post_data			= $object->post_data;
//		
//		$response = new stdClass();		
//			
//		$arbInstance = new AuthorizeNetARB(AUTHORIZENET_API_LOGIN_ID, AUTHORIZENET_TRANSACTION_KEY);
//		
//		$arbInstance->setSandbox(false);
//	    if($this->getAppParam('sandbox', 0)){
//	    	$arbInstance->setSandbox(true);
//	    }
//	    
//	    $subscr_id		= 0;
//		$parent_id		= 0;
//	    $transactions 	= $this->_getExistingTransaction($payment_data->invoice_id, $post_data->txn_id, $subscr_id, $parent_id);
//		foreach($transactions as $transaction){
//	    	$subscriptionId = $transaction->get('gateway_subscr_id', 0);
//	    	if(!empty($subscriptionId)){
//	    		break;
//	    	}
//	    }
//		
//	    $arbInstance->setRefId($payment->getKey());
//   		$response->data 	= $arbInstance->cancelSubscription($subscriptionId);
//   		
//   		//$response->data 	= AuthorizeNetAIM::void($object->gateway_txn_id);
//			
//		
//		return $response;
//	}
	
	// Process the response
	public function process($aim_response)
	{	
		$data 			= $aim_response->data;
		
		/* Response code details
		*	1 -> This transaction has been approved.
		*	2 -> This transaction has been declined.
		*	3 -> There has been an error processing this transaction.
		*	4 -> This transaction is being held for review.
		*/
		switch($data->response_code)
		{
			case 1:
					return $this->_process_approved_response($aim_response);	
					
			case 2:					
			case 3:
			case 4:
			default:
					return $this->_process_error_notify_response($aim_response);
		}
	}
	
	// Transaction has occured successfully
	protected function _process_approved_response($aim_response)
	{
		$data								= $aim_response->data;
		
		// Setting the processor data that would be needed during refund.
		$processor_data 					= new stdClass();
	    $processor_data->amount			 	= $data->amount;
		$processor_data->card_number		= $aim_response->card_number;
		$processor_data->exp_date			= $aim_response->exp_date;
	    
		$response = new Rb_EcommerceResponse();
		$response->set('user_id', 			$data->customer_id)
				 ->set('amount', 			0)
				 ->set('invoice_id', 		$aim_response->request->invoice_id)
				 ->set('txn_id', 			$data->transaction_id)
				 ->set('gateway_subscr_id', 0)
				 ->set('gateway_parent_txn',0)
				 ->set('processor_data', 	$processor_data)
				 ->set('payment_status', 	Rb_EcommerceResponse::NOTIFICATION)
				 ->set('message',			'PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_TRANSACTION_NOTIFICATION')
				 ->set('params', 			$data);
		
		// If payment done successfully
		if($data->transaction_type === 'auth_capture'){
			$response->set('amount', $data->amount)
					 ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_TRANSACTION_PAYMENT_COMPLETED')
					 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE);
		}
		
		// If refund done successfully
		if($data->transaction_type === 'credit' || $data->transaction_type === 'void'){
			$response->set('amount', -$data->amount)
					 ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_TRANSACTION_PAYMENT_REFUNDED')
					 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_REFUND);
		}
		
		return $response;
	}
	
	// Some error has occurred during transaction 
	protected function _process_error_notify_response($aim_response)
	{
		$data = $aim_response->data;
		
		$response = new Rb_EcommerceResponse();
		$response->set('user_id', 			$data->customer_id)
				 ->set('amount', 			0)
				 ->set('invoice_id', 		$aim_response->request->invoice_id)
				 ->set('txn_id', 			$data->transaction_id)
				 ->set('gateway_subscr_id', 0)
				 ->set('gateway_parent_txn',0)
				 ->set('message',			'PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_TRANSACTION_NOTIFICATION')
				 ->set('payment_status', 	Rb_EcommerceResponse::FAIL)
				 ->set('params', 			$data);

		if($data->response_code){
				$response->set('message',	$data->response_reason_text);
		} else {
				$response->set('message', 	'PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_TRANSACTION_AUTHORIZENET_ERROR');
		}
		
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

}