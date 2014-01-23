<?php

/**
* @copyright	Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Mes
* @contact		support+payinvoice@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Mes Processor 
 * @author Manisha Ranawat
 */
class Rb_EcommerceProcessorMes extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;

	// If Payment method support for refund then set it true otherwise set flase
	protected $_support_refund = true;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!class_exists('TpgTransaction')){
			require_once dirname(__FILE__).'/include.php';
		}		
	}
	
	public function request(Rb_EcommerceRequest $request)
	{
		$type = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{			
		$form = JForm::getInstance('rb_ecommerce.processor.mes', dirname(__FILE__).'/forms/form.xml');

		$object 											= $request->toObject();		
		$config 											= $this->getConfig();
		$payment_data 										= $object->payment_data;
		
		$binddata['payment_data']['transaction_amount']		= number_format($payment_data->total, 2, '.', '');
		$binddata['payment_data']['invoice_number']			= $payment_data->invoice_number;	
		$form->bind($binddata); 
		
		$response 											= new stdClass();
		$response->type 									= 'form';
		$response->error 									= false;
		$response->data 									= new stdClass();
		$response->data->object_data						= $object;
		$response->data->post_url 							= false;
		$response->data->form 								= $form;
		
		return $response;
	}
	
	public function _defineCredentials()
	{
		define("HOSTURI", 		"https://cert.merchante-solutions.com/mes-api/tridentApi");
		define("PROFILEID", 	$this->getConfig()->profile_id);
		define("PROFLIEKEY", 	$this->getConfig()->profile_key);
	}
	
	protected function _request_payment(Rb_EcommerceRequest $request)
	{
		$object 		= $request->toObject();			
		$config 		= $this->getConfig(false);	
		$processor_data = $object->processor_data;
		
		// Step 1 :- Stroe Card data at Mes and get Card_id
		if(!isset($processor_data->cardId) || !$processor_data->cardId){	
			return $this->__request_payment_store_card($object, $config);
		} 
		else {
			return $this->__request_payment_create_transaction($object, $config);	
		}

	}	
	
 	protected function __request_payment_store_card($object, $config)
 	{
 		$this->_defineCredentials();
		
 		$response 			= new stdClass();
 		$post_data			= $object->post_data;
		
 		$storeCard	= new TpgStoreData(PROFILEID, PROFLIEKEY);
		$storeCard->setRequestField('card_number',   $post_data->card_number);
		$storeCard->setRequestField('card_exp_date', $post_data->exp_month.substr($post_data->exp_year, -2));
		$storeCard->setHost(HOSTURI);
		$storeCard->execute();
		
		$response->data	= $storeCard;
		
		// VVV IMP : Set card_id variable to check that response generated for store card data (same as profile creation)
		$response->card_id	= false;
		if ($storeCard->isApproved()){
			$response->card_id	= true;
		} 
		
		return $response;
 	}
	
 	protected function __request_payment_create_transaction($object, $config)
 	{
 		$this->_defineCredentials();
 		
		$response 			= new stdClass();
 		$payment_data   	= $object->payment_data;
		$processor_data	 	= $object->processor_data;	
		$post_data			= $object->post_data;
		
		$transaction	= new TpgSale(PROFILEID, PROFLIEKEY);
		$transaction->setRequestField('card_id', $processor_data->cardId);
		
		if(!empty($post_data->cardholder_street_address) && !empty($post_data->cardholder_zip)){
			$transaction->setAvsRequest($post_data->cardholder_street_address, $post_data->cardholder_zip);
		}
		
		if(!empty($post_data->invoice_number)){
			$transaction->setRequestField('invoice_number', $post_data->invoice_number);
		}
		
		if(!empty($post_data->cvv2)){
			$transaction->setRequestField('cvv2', $post_data->cvv2);
		}
		
		// Set in-case of recurring payment
		if($payment_data->expiration_type == RB_ECOMMERCE_EXPIRATION_TYPE_RECURRING){
			$transaction->setEcommInd('3');
		}
		
		$transaction->setRequestField('transaction_amount', $post_data->transaction_amount);
		
		$transaction->setHost( HOSTURI );
		$transaction->execute();
		
		$response->data		= $transaction;
		
		// VVV IMP : Set transaction_payment variable to check that response generated for payment
		$response->transaction_payment	= false;
		if($transaction->isApproved()){
			$response->transaction_payment	= true;
		}
		
		return $response;
 	}
 	
	protected function _request_refund(Rb_EcommerceRequest $request)
	{
		$object 			= $request->toObject();		
		$processor_data 	= $object->processor_data;	
		$payment_data 		= $object->payment_data;		
		$config 			= $this->getConfig(false);
		
		$response 			= new stdClass();

		$this->_defineCredentials();
		
		if(isset($object->post_data->txn_id))
		{
			$tranId 			= $object->post_data->txn_id;
			$refund_amount  	= number_format($payment_data->total, 2);
						
			$refundTransaction	= new TpgRefund(PROFILEID, PROFLIEKEY, $tranId);
			$refundTransaction->setHost(HOSTURI );
			$refundTransaction->execute();
			
			$response->data		= $refundTransaction;
			
			// VVV IMP : Set transaction_refund variable to check that response generated for refund or not
			$response->transaction_refund 	= false;
			if($refundTransaction->isApproved()){
				$response->transaction_refund 	= true;
			}
		}

		return $response;
	}
 	
	public function process($mes_response)
	{	
		if($mes_response->card_id){
			return $this->_process_card_response($mes_response->data);
		}
		elseif($mes_response->transaction_payment){
			return $this->_process_payment_response($mes_response->data);
		}
		elseif($mes_response->transaction_refund){
			return $this->_process_refund_response($mes_response->data);
		}
		else {
			return $this->_process_error_response($mes_response->data);
		}
		
	}
	
	protected function _process_card_response($mes_response)
	{
		$processor_data 					= new stdClass();
        $processor_data->cardId 			= $mes_response->ResponseFields['transaction_id'];	       
        
		$response = new Rb_EcommerceResponse();   	
    	
		$response->set('txn_id', 	 		isset($mes_response->ResponseFields['transaction_id']) ? $mes_response->ResponseFields['transaction_id'] : 0);
    	$response->set('subscr_id',  		isset($mes_response->ResponseFields['transaction_id']) ? $mes_response->ResponseFields['transaction_id'] : 0);
    	$response->set('parent_txn', 		0);
    	$response->set('payment_status', 	Rb_EcommerceResponse::SUBSCR_START);
		$response->set('amount', 	 		0);
		$response->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_MES_TRANSACTION_MES_STORE_CARD_DATA');
		$response->set('params', 			$mes_response->ResponseFields);
		$response->set('processor_data', 	$processor_data);
	
		// IMP :::
		$response->set('next_request', 		true);
		$response->set('next_request_name', 'payment');
	
		return $response;
	}
	
	protected function _process_payment_response($mes_response)
	{
		$response = new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($mes_response->ResponseFields['transaction_id']) 	? $mes_response->ResponseFields['transaction_id'] 	: 0)
				 ->set('subscr_id', 		isset($mes_response->ResponseFields['transaction_id']) 	? $mes_response->ResponseFields['transaction_id'] 	: 0)  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_COMPLETE)	
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_MES_TRANSACTION_MES_PAYMENT_COMPLETED')		 
		 		 ->set('params', 			$mes_response->ResponseFields);
		 
		return $response;
	}
	
	protected function _process_error_response($mes_response)
	{
		$response = new Rb_EcommerceResponse();
    	
		$response->set('txn_id', 	 		isset($mes_response->ResponseFields['transaction_id']) ? $mes_response->ResponseFields['transaction_id'] : 0); 
    	$response->set('subscr_id',  		0);
    	$response->set('parent_txn', 		0);
		$response->set('amount', 	 		0);
		$response->set('payment_status', 	Rb_EcommerceResponse::FAIL);
		$response->set('message', 			$mes_response->ResponseFields['auth_response_text']);
		$response->set('params', 			$mes_response->ResponseFields);
		
		return $response;
	}
	
	protected function _process_refund_response($mes_response)
	{
		$response = new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($mes_response->ResponseFields['transaction_id']) ? $mes_response->ResponseFields['transaction_id'].'_refund' : 'refund')
 				 ->set('subscr_id', 		0)  
				 ->set('parent_txn', 		isset($mes_response->ResponseFields['transaction_id']) ? $mes_response->ResponseFields['transaction_id'] : 0)
				 ->set('payment_status',  	Rb_EcommerceResponse::PAYMENT_REFUND)
				 ->set('amount',          	0)
			 	 ->set('message',        	'PLG_RB_ECOMMERCEPROCESSOR_MES_TRANSACTION_MES_PAYMENT_REFUNDED')		 
 	 			 ->set('params',         	$mes_response->ResponseFields);
 	 			 		
		return $response;
	}
}
