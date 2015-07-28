<?php

/**
 * @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * @package		Rb_EcommerceProcessor
 * @subpackage	Pin
 * @contact		support@readybytes.in
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Payza Processor 
 */

class Rb_EcommerceProcessorPin extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;
	
	static $info = null;	
	
	public function request(Rb_EcommerceRequest $request)
	{
		$type = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$response 					= new stdClass();
		$response->type 			= 'form';
		$response->error 			= false;
		$response->data 			= new stdClass();
		$response->data->post_url 	= false;
	
		$response->type				=	Rb_EcommerceRequest::BUILD_TYPE_HTML ;
		$response->data->form		=	Rb_HelperTemplate::renderLayout('gateway_pin', null,  'plugins/rb_ecommerceprocessor/pin/processors/pin/layouts');
		
		return $response;
	}
	
	protected function _request_payment(Rb_EcommerceRequest $request)
	{
		$object = $request->toObject();			
		$config = $this->getConfig(false);	
		$processor_data = $object->processor_data;
			
		if(!isset($processor_data->token) || !$processor_data->token){			
			return $this->_request_payment_create_customer($object, $config);
		}
		else{
			return $this->_request_payment_create_transaction($object, $config);
		}
		
	}	
	
	protected function _request_payment_create_customer($object, $config)
	{
		$processor_data = $object->processor_data;
		$user_data		= $object->user_data;
		$post_data		= $object->post_data;
		$payment_data	= $object->payment_data;
		
		$response = new stdClass();
		$response->error = false;	
		
		try{
			list($url, $str) = $this->_prepareCustomerNameValuePair($post_data, $user_data,$config);
			$secret_key 	 = $this->getSecretKey($config);	
			$result 		 = $this->execute($url, $str, $secret_key);
			$response->data	 = $result; 
		}catch (Exception $e){
			$response->data  = $e;
		}
			
		return $response;
	}
	
	private function _prepareCustomerNameValuePair($data, $userData,$config)
	{
		$nvp_str  = '';
		$nvp_str .= 'card[number]='.str_replace(" ","",$data->card_number).'&';
		$nvp_str .= 'card[cvc]='.$data->card_code.'&';
		$nvp_str .= 'card[expiry_month]='.$data->expiration_month.'&';
		$nvp_str .= 'card[expiry_year]='.$data->expiration_year.'&';
		$nvp_str .= 'card[name]='.$data->card_name.'&';
		$nvp_str .= 'email='.$userData->email.'&';
		$nvp_str .= 'card[address_line1]='.$data->address.'&';
		$nvp_str .= 'card[address_country]='.$data->country.'&';
		$nvp_str .= 'card[address_state]='.$data->state.'&';
		$nvp_str .= 'card[address_city]='.$data->country.'&';
		$nvp_str .= 'card[address_postcode]='.$data->zip;
		
		$url = $this->getApiUrl('customers',$config);
		
		return array($url, $nvp_str);
	}
	
	protected function _request_payment_create_transaction($object, $config)
	{
		$response = new stdClass();
		$response->error = false;	
		
		try{
			list($url, $str) = $this->_prepareChargeNameValuePair($object,$config);
			$secret_key 	 = $this->getSecretKey($config);		
			$result	  		 = $this->execute($url, $str, $secret_key);
			$response->data  = $result;
		}catch (Exception $e){
			$response->data  = $e;
		}
		
		return $response;
	}
	
	private function _prepareChargeNameValuePair($request,$config)
	{
		$token		 = $request->processor_data->token;
		$paymentData = $request->payment_data;
		$userData	 = $request->user_data;
		
		$amount 	 = $paymentData->total * 100; // IMP: convert into cents
		$ipaddress	 = $userData->ip_address;

		$nvp_str  = '';
		$nvp_str .= 'amount='.intval($amount).'&';
		$nvp_str .= 'currency='. $paymentData->currency.'&';
		$nvp_str .= 'description='.$paymentData->item_name.'&';
		$nvp_str .= 'email='.$userData->email.'&';
		$nvp_str .= 'ip_address='.$userData->ip_address.'&';
		$nvp_str .= 'customer_token='.$token.'&';
		$nvp_str .= 'invoice_number='.$payment_data->invoice_number;
		
		$url = $this->getApiUrl('charges',$config);
		
		return array($url, $nvp_str);
	}	
	
	public function process($pin_response)
	{
		// some errors are there
		if($pin_response->data instanceof Exception || $pin_response->data['error']){
			return $this->_process_error_response($pin_response->data);
		}
		
		if($pin_response->data['response']['amount'] && $pin_response->data['response']['success']){
			return $this->_process_charge_response($pin_response->data['response']);
		}
		
		if($pin_response->data['response']['token']){
			return $this->_process_customer_response($pin_response->data['response']);
		}
				
	}
	
	protected function _process_customer_response($pin_response)
	{
		$processor_data = new stdClass();
        $processor_data->token 			= $pin_response['token'];	       
       
		$response = new Rb_EcommerceResponse();   	
    	$response->set('txn_id', 	 0);
    	$response->set('subscr_id',  $pin_response['token']);
    	$response->set('parent_txn', 0);
    	$response->set('payment_status', Rb_EcommerceResponse::SUBSCR_START);
		$response->set('amount', 	 0);
		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PIN_TRANSACTION_PROFILE_CREATED');
		$response->set('params', $pin_response);
		$response->set('processor_data', $processor_data);
		
		// IMP :::
		$response->set('next_request', true);
		$response->set('next_request_name', 'payment');
		return $response;
	}
	
	protected function _process_charge_response($pin_response)
	{		
		$response = new Rb_EcommerceResponse();
		$response->set('txn_id', 	isset($pin_response['token'])   ? $pin_response['token'] : 0)
				 ->set('subscr_id', isset($pin_response['token']) 	? $pin_response['token'] : 0)  
				 ->set('parent_txn', 0)
				 ->set('amount', 	 0)
				 ->set('payment_status', Rb_EcommerceResponse::NOTIFICATION)	
				 ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PIN_TRANSACTION_NOTIFICATION')		 
				 ->set('params', $pin_response);
				 
		if($pin_response['success']){
			$response->set('amount', ($pin_response['amount'] / 100)) 
					 ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PIN_TRANSACTION_PAYMENT_COMPLETED')
					 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE);
		}
		return $response;
	}
	
	protected function _process_error_response($pin_response)
	{
		$response = new Rb_EcommerceResponse();
    	$response->set('txn_id', 	 0);
    	$response->set('subscr_id',  0);
    	$response->set('parent_txn', 0);
    	$response->set('payment_status', Rb_EcommerceResponse::FAIL);
		$response->set('amount', 	 0);
		$response->set('message', $pin_response['messages'][0]['code']." : ".$pin_response['messages'][0]['message']);
		$response->set('params', $pin_response);
		return $response;
	}
	
	private function getApiUrl($type = 'charges',$config)
	{		
		$api_url = "https://api.pin.net.au/1/$type";
		if($config->sandbox){
			$api_url = "https://test-api.pin.net.au/1/$type";
		}
		
		return $api_url;
	}
	
	private function getSecretKey($config)
	{
		if($config->sandbox){
			return $config->sandbox_secret_key;
		}
		
		return $config->secret_key;
	}
	
	private function execute($url, $nvp_str, $secret_key)
	{
 		$ch = curl_init();
 		curl_setopt($ch, CURLOPT_URL,$url);
 		curl_setopt($ch, CURLOPT_POST, 1);
 		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvp_str);
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
 		curl_setopt($ch, CURLOPT_USERPWD, "$secret_key:");
 		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
 		
 		$response = curl_exec($ch);
 		curl_close($ch);
           
        $result = json_decode($response, true);
        return $result;
	}
}
