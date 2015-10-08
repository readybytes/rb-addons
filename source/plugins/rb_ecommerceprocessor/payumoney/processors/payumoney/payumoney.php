<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RB Ecommerce Package
* @subpackage	Rb_EcommerceProcessor.PayUMoney
* @contact		support@readybytes.in
*/

// no direct access
if(!defined( '_JEXEC' )){
	die( 'Restricted access' );
}

/** 
 * PayUMoney Processor 
 * @author Neelam Soni
 */
class Rb_EcommerceProcessorPayumoney extends Rb_EcommerceProcessor
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
		$type = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	// Build the form in front-end to submit user details to payment gateway
	protected function _request_build(Rb_EcommerceRequest $request)
	{			
		$object 			= $request->toObject();
		$config 			= $this->getConfig(false);
		$user_data			= $object->user_data;
		$post_data			= $object->post_data;
		$payment_data		= $object->payment_data;
		
		// Generate random transaction id
  		$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
		
		$firstSplitArr = array("name"		 => "splitID1",
							   "value"		 => number_format($payment_data->total , 2),
							   "merchantId"	 => $config->merchant_id,
							   "description" => "test description",
							   "commission"  => "2");
		$paymentPartsArr = array($firstSplitArr);	
		$finalInputArr = array("paymentParts" => $paymentPartsArr);
		$productinfo = json_encode($finalInputArr);
		
		$hash_string		= $config->merchant_key.'|'.
							  $txnid.'|'.
							  number_format($payment_data->total , 2).'|'.
							  $productinfo.'|'.
							  ($user_data->name?$user_data->name:'guest').'|'.
							  ($user_data->email?$user_data->email:'guest').'|'.
							  $user_data->id.'||||||||||'.
							  $config->merchant_salt;

		
		$form_data						= array();
		$form_data['key']				= $config->merchant_key;
		$form_data['txnid']				= $txnid;
		$form_data['amount']			= number_format($payment_data->total , 2);
		$form_data['productinfo']		= $productinfo;
		$form_data['firstname']			= $user_data->name?$user_data->name:'guest'; //Todo::In case of guest checkout, user data should be initialized.
		$form_data['email']				= $user_data->email?$user_data->email:'guest'; //Todo::In case of guest checkout, user data should be initialized.
		$form_data['phone']				= $user_data->phone;
		$form_data['udf1']				= $user_data->id;
		$form_data['surl']				= $post_data->return_url.'&invoice_number='.$payment_data->invoice_number.'&notify=1';
		$form_data['furl']				= $post_data->return_url.'&invoice_number='.$payment_data->invoice_number.'&notify=1';
		$form_data['curl']				= $post_data->cancel_url;
		$form_data['hash']				= strtolower(hash('sha512', $hash_string));
		$form_data['service_provider']	= 'payu_paisa';
		 
		$response 					= new stdClass();
		$response->error 			= false;
		$response->type				= Rb_EcommerceRequest::BUILD_TYPE_HTML ;		
		
		$response->data 			= new stdClass();
		$response->data->post_url 	= $this->getPostUrl()."_payment";		
		$response->data->form		= Rb_HelperTemplate::renderLayout('gateway_payumoney', $form_data,  'plugins/rb_ecommerceprocessor/payumoney/processors/payumoney/layouts');
		
		return $response;
	}
	
	// Code to refund any payment
	protected function _request_refund(Rb_EcommerceRequest $request)
	{
		$object 		= $request->toObject();			
		$config 		= $this->getConfig(false);	
		$processorData	= $object->processor_data;
		$payment_data	= $object->payment_data;
		$response 		= new stdClass();
		
		// IMP : Set transaction_refund variable to check that response generated for refund or not
		$response->transaction_refund 	= false;
		if(isset($object->post_data->txn_id)){
			$response->transaction_refund 	= true;
		}
		$response->txn_id	= $processorData->txn_id;
		$response->amount	= number_format($payment_data->total , 2);
		return $response;
	}
	
	public function process($payu_response)
	{
		$data 		= $payu_response->data;		
		$status		= isset($data['status']) ? $data['status'] : 'refund';
		
		// Setting the processor data that would be needed during refund.
		$processor_data 					= new stdClass();
	    $processor_data->amount			 	= $data['amount'];
		$processor_data->card_number		= $data['cardnum'];
		$processor_data->txn_id				= $data['txnid'];
		
		$response = new Rb_EcommerceResponse();
		$response->set('user_id', 			$data['udf1'])
				 ->set('amount', 			0)
				 ->set('invoice_id', 		$this->get_invoice_number($payu_response))
				 ->set('txn_id', 			$data['txnid'])
				 ->set('gateway_subscr_id', 0)
				 ->set('gateway_parent_txn',0)
				 ->set('processor_data', 	$processor_data)
				 ->set('payment_status', 	Rb_EcommerceResponse::NOTIFICATION)
				 ->set('message',			'PLG_RB_ECOMMERCEPROCESSOR_PAYUMONEY_TRANSACTION_NOTIFICATION')
				 ->set('params', 			$data);
		
		switch($status){
			case 'success' :
				// If payment done successfully
				if($data['unmappedstatus'] === 'captured' && $data['field9'] === 'SUCCESS'){
					$response->set('amount', $data['amount'])
							 ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYUMONEY_TRANSACTION_PAYMENT_COMPLETED')
							 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE);
				}
				break;
				
			case 'pending' :
			case 'failure' :
				// If transaction is pending or failed
				if($data['unmappedstatus'] === 'pending' || $data['unmappedstatus'] === 'failed'){
					$response->set('message', $data['field9'].'-'.$data['error_Message'])
							 ->set('payment_status', Rb_EcommerceResponse::FAIL);
				}
				break;
				
			case 'refund'  :
				// If it is refund request
				if($payu_response->transaction_refund){
					$response->set('amount', -$payu_response->amount)
							 ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYUMONEY_TRANSACTION_PAYUMONEY_REFUND')
						 	 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_REFUND);
				}
				break;
				
			default		   :
				$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYUMONEY_TRANSACTION_PAYUMONEY_ERROR')
						 ->set('payment_status', Rb_EcommerceResponse::FAIL);
		}
		
		return $response;
	}
	
	public function get_invoice_number($response)
	{
		if(isset($response->data['invoice_number'])){
			return $response->data['invoice_number'];
		}
		
		return 0;
	}

	// Function to get the base post url
	protected function getPostUrl()
	{
		$subdomain  = $this->getConfig()->sandbox  ? 'test' : 'secure';
        return 'https://' . $subdomain . '.payu.in/';		
	}
}