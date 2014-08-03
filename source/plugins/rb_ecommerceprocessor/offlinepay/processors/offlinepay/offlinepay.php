<?php

/**
* @copyright	Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.OfflinePay
* @contact		support_payinvoice@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * OfflinePay Processor 
 * @author Manisha Ranawat
 */
class Rb_EcommerceProcessorOfflinepay extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;
	
	// If Payment method support for refund then set it true otherwise set flase
	protected $_support_refund = true;
	
	public function __construct($config = array())
	{
		parent::__construct($config);		
	}
	
	public function request(Rb_EcommerceRequest $request)
	{
		$type = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$form = JForm::getInstance('rb_ecommerce.processor.offlinepay', dirname(__FILE__).'/forms/form.xml');

		$object	 										= $request->toObject();		
		$user_data										= $object->user_data;
		$payment_data									= $object->payment_data;
		
		$binddata['payment_data']['bank_name'] 			= $this->getConfig()->bank_name;
		$binddata['payment_data']['account_number']		= $this->getConfig()->account_number;
		$binddata['payment_data']['invoice_number']		= $payment_data->invoice_number;
		$binddata['payment_data']['amount']				= $payment_data->total;
		$form->bind($binddata); 
		
		$response 										= new stdClass();
		$response->type 								= 'form';
		$response->data 								= new stdClass();
		$response->data->post_url 						= false;
		$response->data->form 							= $form;
		
		return $response;
	}
	
	protected function _request_payment(Rb_EcommerceRequest $request)
	{	
		$object 						= $request->toObject();			
		$config 						= $this->getConfig(false);	
		$processor_data 				= $object->processor_data;
		
		$response 						= new stdClass();
		$response->error 				= false;

		if(!isset($processor_data->invoiceId) || !$processor_data->invoiceId){			
			$response->data  = $object->post_data;
		}else {
			$response->data	= $processor_data;
		}
		
		return $response;
	}	
	
	public function process($offline_response)
	{
		if ($offline_response->data->invoiceId){
			return $this->_process_payment_completed($offline_response->data);
		}
		elseif($offline_response->transaction_refund){
			return $this->_process_refund_response($offline_response);
		}
		else{
			return $this->_process_payment_request($offline_response->data);
		}
	}
	
	protected function _process_payment_request($offline_response)
	{         
		$processor_data 			= new stdClass();
        $processor_data->invoiceId 	= $offline_response->invoice_number;	 
        
		$response 					= new Rb_EcommerceResponse();   

		$txn_id						= rand(1000, 999999);
		if($offline_response->id){
			$txn_id   = $offline_response->id; 
		}
		$processor_data->txn_id	=	$txn_id;
    	
		$response->set('txn_id', 	 		$txn_id);
    	$response->set('subscr_id',  		$txn_id);
    	$response->set('parent_txn', 		0);
    	$response->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_PENDING);
		$response->set('amount', 	 		0);
		$response->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_OFFLINEPAY_TRANSACTION_OFFLINEPAY_PAYMENT_INPROCESS');
		$response->set('params', 			$offline_response);
		$response->set('processor_data', 	$processor_data);
	
		return $response;
	}
	
	protected function _process_payment_completed($offline_payment)
	{
		$response = new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			$offline_payment->txn_id)
				 ->set('subscr_id', 		$offline_payment->txn_id)  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_COMPLETE)	
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_OFFLINEPAY_TRANSACTION_OFFLINEPAY_PAYMENT_COMPLETED')		 
		 		 ->set('params', 			$offline_payment);
		 		 
		return $response;
	}
	
	protected function _request_refund(Rb_EcommerceRequest $request)
	{
		$object 		= $request->toObject();			
		$config 		= $this->getConfig(false);	
		$processorData	= $object->processor_data;	
		$response 		= new stdClass();
		
		// IMP : Set transaction_refund variable to check that response generated for refund or not
		$response->transaction_refund 	= false;
		if(isset($object->post_data->txn_id)){
			$response->transaction_refund 	= true;
		}
		$response->txn_id	= $processorData->txn_id;
		
		return $response;
	}
	
	protected function _process_refund_response($offline_response)
	{
		$response = new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			$offline_response->txn_id.'_refund')
 				 ->set('subscr_id', 		0)  
				 ->set('parent_txn', 		$offline_response->txn_id)
				 ->set('payment_status',  	Rb_EcommerceResponse::PAYMENT_REFUND)
				 ->set('amount',          	0)
			 	 ->set('message',        	'PLG_RB_ECOMMERCEPROCESSOR_OFFLINEPAY_TRANSACTION_OFFLINEPAY_PAYMENT_REFUNDED')		 
 	 			 ->set('params',         	$offline_response->data);
 	 			 		
		return $response;
	
	}
}
