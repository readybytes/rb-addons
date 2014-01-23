<?php

/**
* @copyright	Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Moneybookers
* @contact		support+payinvoice@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Moneybookers Processor 
 * @author Manisha Ranawat
 */
class Rb_EcommerceProcessorMoneybookers extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;
	
	// If Payment method support for refund then set it true otherwise set flase
	protected $_support_refund = true;
	
	// supported langauges for moneyBookers
	private $_supportedLanguages  = array('da','de','en','fo','fr','kl','it','no','nl','pl','ru','sv');
	
	// MoneyBookers transaction states
	private $txnStates	 = array('MONEYBOOKERS_TRANSACTION_STATE_PROCESSED'		=> 2, 
						       	 'MONEYBOOKERS_TRANSACTION_STATE_PENDING'		=> 0,
							   	 'MONEYBOOKERS_TRANSACTION_STATE_CANCELLED'		=> -1,
							   	 'MONEYBOOKERS_TRANSACTION_STATE_FAILED'		=> -2,
							   	 'MONEYBOOKERS_TRANSACTION_STATE_CHARGEBACK'	=> -3 );

	//moneybookers transaction states
	const MONEYBOOKERS_TRANSACTION_STATE_PROCESSED	 = 2;
	const MONEYBOOKERS_TRANSACTION_STATE_PENDING	 = 0;
	const MONEYBOOKERS_TRANSACTION_STATE_CANCELLED	 = -1;
	const MONEYBOOKERS_TRANSACTION_STATE_FAILED	 	 = -2;
	const MONEYBOOKERS_TRANSACTION_STATE_CHARGEBACK	 = -3;
	
	
	public function get_invoice_number($response)
	{
		if(isset($response->data['transaction_id'])){
			return $response->data['transaction_id'];
		}
		
		return 0;
	}
	
	public function request(Rb_EcommerceRequest $request)
	{
		$type = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$object 								= $request->toObject();		
		$config 								= $this->getConfig();
		$payment_data 							= $object->payment_data;
		
		$lang    								= $payment_data->language;
		$language								= 'en';
		if(in_array($lang['language'], $this->_supportedLanguages)){
			$language	= $lang['language'];
		}
		
		$form_data['pay_to_email'] 				= $config->merchant_email;
		$form_data['language'] 					= $language;
		$form_data['amount'] 					= number_format($payment_data->total, 2, '.', '');		
		$form_data['currency'] 					= $payment_data->currency;
		$form_data['status_url'] 				= !empty($url_data->notify_url) ? $url_data->notify_url : $config->notify_url;
		$form_data['recipient_description']		= $config->merchant_email;
		$form_data['return_url'] 				= !empty($url_data->return_url) ? $url_data->return_url.'&invoice_number='.$payment_data->invoice_number : $config->return_url.'&invoice_number='.$payment_data->invoice_number;
		$form_data['cancel_url'] 				= !empty($url_data->cancel_url) ? $url_data->cancel_url.'&invoice_number='.$payment_data->invoice_number : $config->cancel_url.'&invoice_number='.$payment_data->invoice_number;
		$form_data['transaction_id'] 			= $payment_data->invoice_number;
		$form_data['detail1_text'] 				= $payment_data->item_name;
		$form_data['status_url2'] 				= 'mailto:'.$config->merchant_email;
		
		if($payment_data->expiration_type == RB_ECOMMERCE_EXPIRATION_TYPE_RECURRING)
		{			
			$form 								= JForm::getInstance('rb_ecommerce.processor.moneybookers', dirname(__FILE__).'/forms/recurring.xml');
			$time 			  			 		= $this->__get_recurrence_time($payment_data->time[0]);
			$all_prices 						= $payment_data->price;
			
			$form_data['rec_end_date'] 			= $this->__calculate_end_date($payment_data, $payment_data->time);
			$form_data['rec_amount'] 			= number_format($all_prices[0], 2, '.', '');
			$form_data['rec_cycle'] 			= $time[1];		
			$form_data['rec_period'] 			= $time[0];

		}
		else {
			$form = JForm::getInstance('rb_ecommerce.processor.moneybookers', dirname(__FILE__).'/forms/form.xml');
		}
		
		$form->bind($form_data);
		
		$response 					= new stdClass();	
		$response->type 			= 'form';	
		$response->data 			= new stdClass();
		$response->data->post_url 	= 'https://www.moneybookers.com/app/payment.pl';
		$response->data->form 		= $form;
		
		return $response;
	}
	
	protected function _request_refund(Rb_EcommerceRequest $request)
	{
		$object 			= $request->toObject();		
		$payment_data 		= number_format($payment_data->total, 2, '.', '');
		$response 			= new stdClass();	
		
		$action				= 'prepare';
		$email				= $this->getConfig()->merchant_email;
		$password			= JString::strtolower(md5($this->getConfig()->password));
		
		if(isset($object->post_data->txn_id))
		{
			$mb_transaction_id 	= $object->post_data->txn_id;
			$amount				= number_format($payment_data->total, 2);
					
			$url 				= "https:www.moneybookers.com/app/payment.pl";
			$post_data			= "action=$action&email=$email&password=$password&mb_transaction_id=$mb_transaction_id&amount=$amount";
			
			$curl				= new JHttpTransportCurl(new Rb_Registry());
			$http_response		= $curl->request('POST', $url, $post_data);
			$http_response		= simplexml_load_string($http_response);
			
			if(isset($http_response['sid']) && $http_response['sid']){
				$url							= 'https://www.moneybookers.com/app/refund.pl';
				$action     					= 'refund';
				$sid            				= $http_response['sid'];
				$post_data 						= "action=$action&sid=$sid";
				
				$response->data					= $this->request('POST', $url, $post_data);
				$response->data					= simplexml_load_string($response->data);
				$response->refund_transaction 	= true;
				
			}else {
				$response->data					= $http_response['error']['error_msg'];
				$response->refund_transaction 	= false;
			}
		}
		
		return $response;
	}	
	
	public function process($mb_response)
	{
		$data		= $mb_response->data;
		
		if($mb_response->refund_transaction){
			$this->_process_refund_response($mb_response);
		}
		else {
			$this->_process_payment_response($data);	
		}
	}
	
	protected function _process_payment_response($data)
	{
		$response 	= new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($data['mb_transaction_id']) 	? $data['mb_transaction_id'] 	: 0)
				 ->set('subscr_id', 		isset($data['customer_id'])			? $data['customer_id']			: $data['mb_transaction_id'])  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::FAIL)	
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_MONEYBOOKERS_TRANSACTION_MONEYBOOKERS_NOTIFICATION')		 
		 		 ->set('params', 			$data);
			
		if(in_array($data['status'], array(self::MONEYBOOKERS_TRANSACTION_STATE_PROCESSED, self::MONEYBOOKERS_TRANSACTION_STATE_CHARGEBACK))){	
			$this->_process_payment_notification($response, $data);
		}
		else {
			if($data['status']){
				$response->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_MONEYBOOKERS_TRANSACTION_'.array_search($data['status'], $this->txnStates));
			}		 
		}
		return $response;
	}
	
	protected function _process_payment_notification(Rb_EcommerceResponse $response, $data)
	{
		$response 	    = new Rb_EcommerceResponse();
		
		$secretWord 	= $this->getConfig()->merchant_secret_word;
		$md5Content 	= $data['merchant_id'].$data['transaction_id'].strtoupper(md5($secretWord)).$data['mb_amount'].$data['mb_currency'].$data['status'];
		$md5string		= JString::strtoupper( md5($md5Content) );
		
		if($this->getConfig()->merchant_email !== JString::strtolower($data['pay_to_email'])){
			$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_MONEYBOOKERS_TRANSACTION_MONEYBOOKERS_INVALID_MERCHANT');
		}
		
		elseif((!in_array($data['status'], array(self::MONEYBOOKERS_TRANSACTION_STATE_PROCESSED, self::MONEYBOOKERS_TRANSACTION_STATE_CHARGEBACK)) ) || $data['md5sig'] != $md5string){
			$response->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_FAIL);
			$response->set('message',			'PLG_RB_ECOMMERCEPROCESSOR_MONEYBOOKERS_TRANSACTION_MONEYBOOKERS_PAYMENT_FAILED');
		}
		
		elseif( self::MONEYBOOKERS_TRANSACTION_STATE_FAILED == $data['status']){
			if(isset($data['failed_reason_code'])){
				$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_MONEYBOOKERS_TRANSACTION_MONEYBOOKERS_PAYMENT_FAILED_REASON_CODE_'.$data['failed_reason_code']);				
			}
		}
		else {
			$response->set('amount', 		 $data['amount'])
				 	 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE)
				 	 ->set('message',  		 'PLG_RB_ECOMMERCEPROCESSOR_MONEYBOOKERS_TRANSACTION_MONEYBOOKERS_PAYMENT_COMPLETED');
		}
		
		return $response;
	}
	
	protected function _process_refund_response($mb_response)
	{
		$data	  	= $mb_response->data;
		$response 	= new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($data['mb_transaction_id']) ? $data['mb_transaction_id'].'_refund' : 'refund')
				 ->set('subscr_id', 		isset($data['customer_id'])			? $data['customer_id']			: $data['mb_transaction_id'])  
				 ->set('parent_txn', 		isset($data['mb_transaction_id']) ? $data['mb_transaction_id'] : 0)
				 ->set('amount', 			0)
				 ->set('payment_status',  	Rb_EcommerceResponse::FAIL)
				 ->set('params', 			$data);
				 
		if($mb_response->refund_transaction){
			if($data['status'] == self::MONEYBOOKERS_TRANSACTION_STATE_PROCESSED)
			{
				$response->set('amount',          	$data['mb_amount'])
				 	 	 ->set('payment_status',  	Rb_EcommerceResponse::PAYMENT_REFUND)	
				 	 	 ->set('message',        	'PLG_RB_ECOMMERCEPROCESSOR_MONEYBOOKERS_TRANSACTION_MONEYBOOKERS_PAYMENT_REFUNDED');
		 	}
		}
	 	else {
		 	$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_MONEYBOOKERS_TRANSACTION_MONEYBOOKERS_AUTHORISATION_FAILED');
		 }
	}
	
	protected function __get_recurrence_time($expTime)
	{
		$expTime['year']	= isset($expTime['year']) 	? intval($expTime['year']) 	: 0;
		$expTime['month']	= isset($expTime['month']) 	? intval($expTime['month']) : 0;
		$expTime['day'] 	= isset($expTime['day']) 	? intval($expTime['day']) 	: 0;
		
		// if only days are set then return days as it is
		if(!empty($expTime['day'])){
			$days = $expTime['day'];
			
			if(!empty($expTime['month'])){
				$days += $expTime['month'] * 30;

				if(!empty($expTime['year'])){
					$days += $expTime['year'] * 365;
				}
			}
			return array($days, 'day');
		}
		
		// if months are set
		if(!empty($expTime['month'])){
			$month = $expTime['month'];
			
			if(!empty($expTime['year'])){
				$month += $expTime['year'] * 12 ;
			}
			
			return array($month,'month');
		}
		
		// years
		if(!empty($expTime['year'])){		
			return array($expTime['year'], 'year');
		}
		
		// XITODO : what to do if not able to convert it
		return false;
	}
	
	protected function __calculate_end_date($data, $expiration)
	{
		$startDate 			= new Rb_Date('now');
		$endDate   			= $startDate;
		$recurrence_count  	= $data->recurrence_count;
		
		// IMP : add 10 years when recurrence count is 0
		if(intval($recurrence_count) === 0){
			$endDate = $endDate->addExpiration('100000000000');
			return $endDate->toFormat('%d/%m/%Y');
		}
		
		for($i=0; $i< $recurrence_count; $i++){
			$endDate   = $endDate->addExpiration($expiration);
		}
		
		return $endDate->toFormat('%d/%m/%Y');
	}
}
