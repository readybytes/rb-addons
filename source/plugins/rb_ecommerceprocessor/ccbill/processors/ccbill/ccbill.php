<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.CCBill
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * CCBill Processor 
 */

class Rb_EcommerceProcessorCCBill extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;

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
	
	public function get_invoice_number($response)
	{
		if(isset($response->data['X-invoice_number'])){
			return $response->data['X-invoice_number'];
		}
		
		return 0;
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		
		$build_type = $request->get('build_type', Rb_EcommerceRequest::BUILD_TYPE_XML);
		$object 						= $request->toObject();		
		$config 						= $this->getConfig();
		$payment_data 					= $object->payment_data;
		
		$exp_time 						= $this->getRecurrenceTime($payment_data->time[0]);
		$form_period 					= empty($exp_time)	? 2 : $exp_time;
		$form_price 					= number_format($payment_data->total, 2, ',', '');
		
		$form_data['saltKey'] 			= $config->salt_key;
		$form_data['currencyCode'] 		= $payment_data->currency;
		$form_data['clientAccnum'] 		= $config->client_account_number;
		$form_data['clientSubacc'] 		= $config->sub_account;
		$form_data['formName'] 			= $config->form_name;
		$form_data['formPeriod'] 		= $form_period;
		$form_data['formPrice'] 		= $form_price;
		
		$form_data['invoice_number']	= $payment_data->invoice_number;
		$form_data['type'] 			    =	'fixed';			
		if($payment_data->expiration_type == RB_ECOMMERCE_EXPIRATION_TYPE_RECURRING)
		{	
			$form_data['type']  			= 'recurring';
			$recurrenceCount 			= $payment_data->recurrence_count; 
			//for life time
			if($recurrenceCount == 0){
			 	$form_rebills = 99;
			}
			else {
				$form_rebills = ($recurrenceCount-1);
			}
			
			$all_prices = $payment_data->price;
			
       		$form_data['formRebills'] 			= $form_rebills;
			$form_data['formRecurringPrice'] 	= number_format($all_prices[0], 2, '.', '');
			$form_data['formRecurringPeriod'] 	= $form_period;		
		
			// For Trail 1
			if(count($all_prices) >= 2){
					$exp_time 							= $this->getRecurrenceTime($payment_data->time[1]);
					$form_period 						= empty($exp_time)	? 2 : $exp_time;				
		       		$form_recurringPrice 				= number_format($all_prices[1], 2, '.', '');
					
		       		
		       		
		       		$form_data['formRebills'] 			= $recurrenceCount;
					$form_data['formRecurringPrice'] 	= $form_recurringPrice;
					$form_data['formRecurringPeriod'] 	= $form_period;	
			}
			
			$parameter = array($form_price,$form_period,$form_recurringPrice,$form_rebills,$config->currency_value,$config->salt_key);
		}
		else {
			$parameter = array($form_price,$form_period,$config->currency_value,$config->salt_key);
		}		

		$form_digest = $this->calculateMD5($parameter);
		
		$form_data['formDigest'] 	= $form_digest;	
		
		$response 					= new stdClass();		
		$response->data 			= new stdClass();
		$response->data->post_url 	= 'https://bill.ccbill.com/jpost/signup.cgi';
		
		switch ($build_type) 
			{
				case Rb_EcommerceRequest::BUILD_TYPE_HTML :
					$response->type			=	Rb_EcommerceRequest::BUILD_TYPE_HTML ;
					$response->data->form	=	Rb_HelperTemplate::renderLayout('gateway_ccbill', $form_data,  'plugins/rb_ecommerceprocessor/ccbill/processors/ccbill/layouts');
					break;
					
				case Rb_EcommerceRequest::BUILD_TYPE_XML :
				default:
					$response->type 		= Rb_EcommerceRequest::BUILD_TYPE_XML ;
					$form 			= JForm::getInstance('rb_ecommerce.processor.ccbill', dirname(__FILE__).'/forms/'.$form_data['type'].'.xml');
					$form->bind($form_data); 
					$response->data->form	= $form;
			}
		
		return $response;
	}
	
	
	public function calculateMD5($parameters)
	{
	 	$finalString = '';
	 	foreach ($parameters as $parameter)
	 	{
	 		$finalString .= $parameter;
	 	}
	 	return md5($finalString);
	}
	
	public function process($cc_response)
	{
		$data 		= $cc_response->data;
		$response 	= new Rb_EcommerceResponse(); 
		
		$response->set('txn_id', 			isset($data['transactionId'])	? $data['transactionId'] 	: 0)
		 		 ->set('subscr_id', 		isset($data['subscriptionId']) 	? $data['subscriptionId'] 	:0)  
				 ->set('parent_txn', 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::NOTIFICATION)	
				 ->set('amount', 	 		0)
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_CCBILL_TRANSACTION_CCBILL_NOTIFICATION')		 
		 		 ->set('params', 			$data);
		
		if($data['eventType'] == 'NewSaleSuccess'){
 	    	$saltKey 	= $this->getConfig()->saltKey;
		 	$parameter  = array($data['subscriptionId'],'1',$saltKey);	
		 	
		 	//calculate the md5. It is calculated from 3 values(subscriptionId,1,Saltkey)
		    $form_digest = $this->calculateMD5($parameter);
				
			if(($data['dynamicPricingValidationDigest'] != $form_digest)){
				$response->set('payment_status',	Rb_EcommerceResponse::PAYMENT_FAIL)
						 ->set('message', 		    'PLG_RB_ECOMMERCEPROCESSOR_CCBILL_TRANSACTION_CCBILL_NOTIFICATION_NOT_RECIEVED_PROPERLY');
			
				return $response;
			}
	 	}
	 	
		$func_name	= isset($data['eventType']) ? '_on_process_'.JString::strtolower($data['eventType']) : 'EMPTY';
		if(method_exists($this, $func_name)){
			$this->$func_name($response, $data);
		}
	 	else{
	 		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_CCBILL_PROCESSOR_CCBILL_RESPONSE_MESSAGE_INVALID_EVENT_TYPE');
		}
		
		return $response;
    	
	}
	
	protected function _on_process__NewSaleSuccess(Rb_EcommerceResponse $response, Array $data)
 	{
 		$response->set('amount', 			$data['billedInitialPrice'])
	 			 ->set('payment_status' , 	Rb_EcommerceResponse::PAYMENT_COMPLETE)
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_CCBILL_TRANSACTION_CCBILL_PAYMENT_COMPLETED');
 	}
	 
 	protected function _on_process_RenewalSuccess(Rb_EcommerceResponse $response, Array $data)
  	{
	 	$response->set('amount', 			$data['billedAmount'])
	 			 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_COMPLETE)
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_CCBILL_TRANSACTION_CCBILL_RECUR_PAYMENT_COMPLETED');
	 }
	 
	 protected function _on_process_Cancellation(Rb_EcommerceResponse $response, Array $data) 
	 {
	   	$response->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_PENDING)
	   			 ->set('message', 			$data['reason']);	
	 }
	 
	 protected function _on_process_RenewalFailure(Rb_EcommerceResponse $response, Array $data) 
	 {
	 	$response->set('message', 			$data['reason'].' Decline code:-'.$data['failureCode'])
	 			 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_FAIL); 	
	 }
	 
     protected function _on_process_NewSaleFailure(Rb_EcommerceResponse $response, Array $data)
	 {
		$response->set('message', 			$data['failureReason'].' Decline code:-'. $data['failureCode'])
				 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_FAIL);
	 }
	 
	 protected function _on_process_refund(Rb_EcommerceResponse $response, Array $data)
	 {
	    $response->set('amount', 			-$data['amount'])
	    		 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_REFUND)
				 ->set('message', 			$data['reason']); 	
	}
	
 	public function getRecurrenceTime($exp_time)
	 {
		$exp_time['year']  	= isset($exp_time['year']) 		? intval($exp_time['year']) 	: 0;
		$exp_time['month'] 	= isset($exp_time['month']) 	? intval($exp_time['month']) 	: 0;
		$exp_time['day']   	= isset($exp_time['day']) 		? intval($exp_time['day']) 		: 0;
		$time = 0;

		if( $exp_time['year']){
			$time = ($exp_time['year'] * 365);
		}
		
		if($exp_time['month']){
			$time = $time + ($exp_time['month']*30);
		}

		if ($exp_time['day']){
			$time = $time + ($exp_time['day']);
		}
		
		return $time;		
	 }

}
