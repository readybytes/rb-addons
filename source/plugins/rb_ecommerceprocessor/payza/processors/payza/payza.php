<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Payza
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Payza Processor 
 */

class Rb_EcommerceProcessorPayza extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;
	
	static $info = null;	
	
	public function request(Rb_EcommerceRequest $request)
	{
		$type = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	public function get_invoice_number($response)
	{	
		if(isset($response->data['invoice_number'])){
			return $response->data['invoice_number'];
		}
				
		$token = $response->data['token'];
				
		if(!$token){
			return false;
		}
		
		$test = false;
		if(isset($response->data['test'])){
			$test = (bool) $response->data['test'];
		}

		$response = $this->_getResponse($token, $test);
		if(!$response){
			return false;
		}		
		
		if(isset($response['apc_1'])){
			return $response['apc_1'];
		}
	
		return false;
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$object 						= $request->toObject();		
		$config 						= $this->getConfig();
		$payment_data 					= $object->payment_data;
		$url_data 	                    = $object->url_data;
		
		$form_data['ap_merchant']       = $this->getConfig()->merchant;
		$form_data['ap_itemname']       = $payment_data->item_name;
		$form_data['ap_currency']       = $payment_data->currency;
		$form_data['ap_returnurl']      = !empty($url_data->return_url) ? $url_data->return_url.'&invoice_number='.$payment_data->invoice_number : $config->return_url.'&invoice_number='.$payment_data->invoice_number;
		$form_data['ap_cancelurl']      = !empty($url_data->cancel_url) ? $url_data->cancel_url : $config->cancel_url;
		$form_data['ap_description']	= $payment_data->item_name;
		$form_data['apc_1']				= $payment_data->invoice_number;
		
		if($payment_data->expiration_type == RB_ECOMMERCE_EXPIRATION_TYPE_RECURRING)
		{			
			$form 							= JForm::getInstance('rb_ecommerce.processor.payza', dirname(__FILE__).'/forms/recurring.xml');
			$form_data['ap_purchasetype']   = 'Subscription';
			
			$all_prices		= $payment_data->price;
			$regular_index 	= 0;
			// Trail 1
			if($all_prices >=2){
				$time	 							= $this->__get_recurrence_count($payment_data->time[0]);
				$form_data['ap_trialamount']		= number_format($all_prices[0], 2, '.', '');
				$form_data['ap_trialtimeunit']		= $time[1];
				$form_data['ap_trialperiodlength']	= $time[0];
				$regular_index						= 1;
			}
			$time 							= $this->__get_recurrence_count($payment_data->time[$regular_index]);
			$form_data['ap_amount']			= number_format($all_prices[$regular_index], 2, '.', '');
			$form_data['ap_timeunit']		= $time[1];
			$form_data['ap_periodlength']	= $time[0];
			$form_data['ap_periodcount']	= $payment_data->recurrence_count;
		}
		else {
			$form 							= JForm::getInstance('rb_ecommerce.processor.payza', dirname(__FILE__).'/forms/form.xml');
			$form_data['ap_purchasetype']   = 'Item';
			$form_data['ap_amount']       	= number_format($payment_data->total, 2, '.', '');
		}
		
		$form->bind($form_data);
		
		$response 					= new stdClass();		
		$response->data 			= new stdClass();
		$response->data->post_url 	= $this->getPostUrl();
		
    	switch ($object->build_type) 
		{
			case Rb_EcommerceRequest::BUILD_TYPE_HTML :
				$response->type			=	Rb_EcommerceRequest::BUILD_TYPE_HTML ;
				$response->data->form	=	Rb_HelperTemplate::renderLayout('gateway_payza', array('form' => $form, 'data' => $form_data),  'plugins/rb_ecommerceprocessor/payza/processors/payza/layouts');
				break;
				
			case Rb_EcommerceRequest::BUILD_TYPE_XML :
			default:
				$response->type 		= Rb_EcommerceRequest::BUILD_TYPE_XML ;
				$response->data->form	= $form;
		}
		return $response;
	}
	
	public function getPostUrl()
	{
		$url = 'https://secure.payza.com/checkout';
		if($this->getConfig()->sandbox)
		{
			$url = 'https://sandbox.Payza.com/sandbox/payprocess.aspx'; 
		}
		return $url;
	}
	
	public function process($payza_response)
	{
		$data		= $this->_getResponse($payza_response->data['token'], $this->getConfig()->sandbox);
		$response 	= new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($data['ap_referencenumber']) 				? $data['ap_referencenumber'] 			  	: 0)
				 ->set('subscr_id', 		isset($data['ap_subscriptionreferencenumber']) 	? $data['ap_subscriptionreferencenumber'] 	: 0)  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_FAIL)	
				 ->set('message', 		    'PLG_RB_ECOMMERCEPROCESSOR_PAYZA_TRANSACTION_PAYZA_PAYMENT_FAILED')		 
		 		 ->set('params', 			$data);
		 		 
		if('item' === JString::strtolower($data['ap_purchasetype'])){	
			return $this->_process_payment_response($response, $data);
		}
		
		if('subscription' === JString::strtolower($data['ap_purchasetype'])){
			if ($data['ap_status'] == "Subscription-Payment-Success"){
				return $this->_process_payment_response($response, $data);
			} 
		}
		elseif ($data['ap_status'] == "Subscription-Canceled")
		{
			$response->set('message', 		'PLG_RB_ECOMMERCEPROCESSOR_PAYZA_TRANSACTION_PAYZA_SUBSCRIPTION_CANCEL');
			$response->set('payment_status', Rb_EcommerceResponse::SUBSCR_CANCEL);
		}
		else{
			$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYZA_TRANSACTION_PAYZA_'.JString::strtoupper($data['ap_status']));
		}

		return $response;
	}
	
	protected function _process_payment_response(Rb_EcommerceResponse $response, Array $data)
	{
		if(JString::strtolower($this->getConfig()->merchant) !== JString::strtolower($data['ap_merchant'])){
			$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYZA_INVALID_MERCHANT');
		}
			
		// check for recurring ang non-recurring
		if('success' !== JString::strtolower($data['ap_status']) && 'subscription-payment-success' !== JString::strtolower($data['ap_status'])){
			$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYZA_TRANSACTION_PAYZA_PAYMENT_FAILED');
		}
		else {
			$response->set('amount', 			$data['ap_totalamount']);
			$response->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_COMPLETE);
			$response->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_PAYZA_TRANSACTION_PAYZA_PAYMENT_COMPLETED');
		}
	
		return $response;
	}
	
	protected function __get_recurrence_count($expTime)
	{
		$rawTime = str_split($expTime, 2);
		$expTime = array();
		$expTime['year']    = intval(array_shift($rawTime));
		$expTime['month']   = intval(array_shift($rawTime));
		$expTime['day']     = intval(array_shift($rawTime));
		
		// years
		if(!empty($expTime['year'])){
			if($expTime['year'] >= 5){
				return array(5, 'Year');
			}
			
			if($expTime['year'] >= 2){
				return array($expTime['year'], 'Year');
			}
			
			// if months is not set then return years * 12 + months
			if(isset($expTime['month']) && $expTime['month']){
				return array($expTime['year'] * 12 + $expTime['month'], 'Month');
			}				
			
			return array($expTime['year'], 'Year');
		}
		
		// if months are set
		if(!empty($expTime['month'])){
			// if days are empty
			if(empty($expTime['day'])){
				return array($expTime['month'], 'Month');
			}
			
			// if total days are less or equlas to 90, then return days
			//  IMP : ASSUMPTION : 1 month = 30 days
			$days = $expTime['month'] * 30;
			if(($days + $expTime['day']) <= 90){
				return array($days + $expTime['day'], 'Day');
			}
			
			// other wise convert it into weeks
			return array(intval(($days + $expTime['day'])/7, 10), 'W');
		}
		
		// if only days are set then return days as it is
		if(!empty($expTime['day'])){
			return array(intval($expTime['day'], 10), 'Day');
		}
	}
	
	protected function _getResponse($token, $test)
	{
		if(self::$info === null)
		{
			$response = '';
			if(empty($token)){
				return $response;
			}
		
			$url = "https://secure.payza.com/ipn2.ashx";
			if($test){
				$url  = "https://sandbox.Payza.com/sandbox/IPN2.ashx";
			}
		
			// get the token from Alertpay
			$token = urlencode($token);
		
			//preappend the identifier string "token=" 
			$token = 'token='.$token;
		
			/**
			 * 
			 * Sends the URL encoded TOKEN string to the Alertpay's IPN handler
			 * using cURL and retrieves the response.
			 * 
			 * variable $response holds the response string from the Alertpay's IPN V2.
			 */
					
			$ch = curl_init();
		
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $token);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
			$response = curl_exec($ch);
		
			curl_close($ch);
			
			if(strlen($response) <= 0){
				return false;
			}
			
			if(urldecode($response) == "INVALID TOKEN"){
				return false;
			}
		
			//urldecode the received response from Alertpay's IPN V2
			$response = urldecode($response);
			
			//split the response string by the delimeter "&"
			$aps = explode("&", $response);
			
			//define an array to put the IPN information
			$info = array();
			
			foreach ($aps as $ap)
			{
				//put the IPN information into an associative array $info
				$ele = explode("=", $ap);
				self::$info[$ele[0]] = $ele[1];
			}
		}
		
		return self::$info;
		
		}
}
