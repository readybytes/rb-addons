<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.2checkout
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * 2Checkout Processor 
 * @author Rimjhim Jain
 */
class Rb_EcommerceProcessor2checkout extends Rb_EcommerceProcessor
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
	
	public function get_invoice_number($co_response)
	{
		if(isset($co_response->data['vendor_order_id']) && !empty($co_response->data['vendor_order_id'])){
			return $co_response->data['vendor_order_id'];
		}
		return false;
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$object 		= $request->toObject();		
		$config 		= $this->getConfig();
		
		$url_data 		= $object->url_data;
		$payment_data 	= $object->payment_data;
		$user_data      = $object->user_data;
		$url_data 	    = $object->url_data;
		
		$form_data['sid'] 					= $config->sid;
		$form_data['invoice_number'] 		= $payment_data->invoice_number;
		$form_data['payment_key'] 			= $payment_data->invoice_number;
		$form_data['merchant_order_id'] 	= $payment_data->invoice_number;
		$form_data['x_invoice_num'] 		= $payment_data->invoice_number;
		$form_data['fixed'] 				= 'Y';
		$form_data['total'] 				= number_format($payment_data->total, 2, '.', ''); //only 2 digit after decimal is allowed
		$form_data['currency'] 				= $payment_data->currency;
		$form_data['return_url'] 			= !empty($url_data->return_url) ? $url_data->return_url.'&invoice_number='.$payment_data->invoice_number.'&notify=1' : $config->return_url.'&invoice_number='.$payment_data->invoice_number.'&notify=1';
		$form_data['cart_order_id'] 		= $payment_data->item_name;
		$form_data['cust_id'] 				= $user_data->id;
		$form_data['username'] 				= $user_data->username;
		$form_data['name'] 					= $user_data->name;
		$form_data['cart_brand_name'] 		= Rb_Factory::getApplication()->getCfg('sitename');
		$form_data['cart_version_name'] 	= 5;
		
		$form_path = dirname(__FILE__).'/forms/';
		
		if($payment_data->expiration_type == RB_ECOMMERCE_EXPIRATION_TYPE_RECURRING)
		{			
			$form = JForm::getInstance('rb_ecommerce.processor.2checkout', $form_path.'form_recurring.xml');
			
			$all_prices = $payment_data->price;
			
			$form_data['mode']				= '2CO'; // always should be 2CO
			$form_data['li_0_type']			= 'product'; // 0 is for sequence number of product(starting from 0), product,shipping,tax or coupon
	   		$form_data['li_0_name']     	= $payment_data->item_name;
	   		$form_data['li_0_quantity']		= 1; //  quantity should be 1 for now
	   		$form_data['li_0_price']		= number_format($all_prices[0], 2, '.', '');
			$form_data['li_0_tangible']		= "N";
			
			$time 							= $this->__get_recurrence_time($payment_data->time[0]);
			$form_data['li_0_recurrence'] 	= $time[0].' '.$time[1];
			$form_data['li_0_duration']		= $payment_data->recurrence_count;
				   	
	   		// for Trail 1
	   		if(count($all_prices) >= 2)
	   		{
	   			$start_price    				= $all_prices[0];	   			
	   			$regular_price 					= $all_prices[1];
	   			$startup_fee					= $regular_price - $start_price;
	   			
	   			$form_data['li_0_price'] 		= number_format($regular_price, 2, '.', '');
				$form_data['li_0_startup_fee']	= number_format($startup_fee, 2, '.', '');	
			}	
		}
		else {
			$form = JForm::getInstance('rb_ecommerce.processor.2checkout', $form_path.'form.xml');
		}		
		
		$form->bind($form_data);
		$response = new stdClass();
		
		$response->type				=	Rb_EcommerceRequest::BUILD_TYPE_HTML ;
		$response->data->form		=	Rb_HelperTemplate::renderLayout('gateway_2checkout', array('form' => $form, 'data' => $form_data),  'plugins/rb_ecommerceprocessor/2checkout/processors/2checkout/layouts');
		
		$response->data->post_url 	= $this->getPostUrl($config);
		
		return $response;
	}
	
	public function getPostUrl($config)
	{
		if ($config->sandbox) {
			$url = 'https://sandbox.2checkout.com/checkout/purchase';
		}else {
			if ($config->alternate_url) {
				$url = 'https://www2.2checkout.com/checkout/spurchase';
			} else {
				$url = 'https://www.2checkout.com/checkout/spurchase';
			}
		}
		
		return $url;
	}
	
	private function __get_recurrence_time($expTime)
	{
		$expTime['year'] 	= isset($expTime['year']) 	? intval($expTime['year'], 10) 	: 0;
		$expTime['month'] 	= isset($expTime['month']) 	? intval($expTime['month'], 10) : 0;
		$expTime['day'] 	= isset($expTime['day']) 	? intval($expTime['day'], 10)  	: 0;
		
		// days, if days are not zero then, convert whole time into days and convert it into weeks 
		if(!empty($expTime['day'])){
			$days  = $expTime['year'] * 365;
			$days += $expTime['month'] * 30;
			$days += $expTime['day'];
			
			$weeks = intval($days/7, 10);
			return array($weeks, 'Week');
		}
		
		// if months are not empty 
		if(!empty($expTime['month'])){
			$months  = $expTime['year'] * 12;
			$months += $expTime['month'];
			return array($months, 'Month');
		}
		
		// if years are not empty 
		if(!empty($expTime['year'])){
			return array($expTime['year'], 'Year');			
		}
	}
	
	public function process($co_response)
	{
		$data	  = $co_response->data;
		$response = new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			0)
		 		 ->set('subscr_id', 		isset($data['invoice_id']) 	? $data['invoice_id'] 	:0)  
				 ->set('parent_txn', 		isset($data['sale_id']) 	? $data['sale_id'] 		: 0)
				 ->set('payment_status', 	Rb_EcommerceResponse::NOTIFICATION)	
				 ->set('amount', 	 		0)
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_TRANSACTION_2CHECKOUT_NOTIFICATION')		 
		 		 ->set('params', 			$data);

		if(!$this->validateNotification($data)){
			$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_TRANSACTION_2CHECKOUT_INVALID_HASH_KEY');
			$response->set('payment_status', Rb_EcommerceResponse::PAYMENT_FAIL);	
			return $response;
		}

		// if same notification came more than one time
    	// check if transaction already exists
    	// if yes then do nothing and return
    	//$filter     = array();
    	//$filter['invoice_id']          = Rb_EcommerceAPI::invoice_get_id_from_number($data['vendor_order_id']);
    	//$filter['gateway_subscr_id']   = isset($data['invoice_id']) ? $data['invoice_id'] : 0;
    	//$filter['gateway_parent_txn']  = isset($data['sale_id']) ? $data['sale_id'] : 0;
    	//$filter['gateway_txn_id']      = 0;
    	
    	//$transactions = Rb_EcommerceAPI::transaction_get_records($filter);
    	
    	//if($transactions !== false){
    	//	foreach($transactions as $record){
    	//		$params = json_decode($record->params);
    	//		if($params->message_id === $data['message_id'] && !isset($data['item_type_2'])){
    	//			return false;
    	//		}
    	//	}
    	//}
		
		// if the Notification is from INS of 2checkout
		// then message type will be se	
		$func_name = 'invalidFunction';
		if(isset($data['message_type'])){
			$func_name = '_'.JString::strtolower($data['message_type']);
		}

		//XITODO : Log error
		if(method_exists($this, $func_name)){
			$this->$func_name($response, $data);
		}
		else{
			$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_RESPONSE_MESSAGE_INVALID_MESSAGE_TYPE');
		}
		
		$response->set('params', $data);
		return $response;		 		 
	}
	
	function validateNotification($post)
	{
		// if notification came from INS then need to do following
		if(isset($post['message_type'])){
			$string_to_hash = $post['sale_id'].$this->getConfig()->sid.$post['invoice_id'].$this->getConfig()->secret_word;
			$postKey = $post['md5_hash'];
		}
		else{
			if($this->getConfig()->sandbox) {
				$string_to_hash	= $this->getConfig()->secret_word.$this->getConfig()->sid."1".$post['total'];
			} else {
				$string_to_hash	= $this->getConfig()->secret_word.$this->getConfig()->sid.$post['order_number'].$post['total'];
			}
			$postKey = $post['key'];
		}
		
		$check_key = strtoupper(md5($string_to_hash));

		return (strcmp($check_key, $postKey) == 0);
	}
	
	protected function _order_created(Rb_EcommerceResponse $response, Array $data)
	{
		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_ORDER_CREATED_'.JString::strtoupper($data['invoice_status']));
		
		// after refund is processed 2checkout again send a notification having paid as well as refund variables.
		// so if such notification comes, just make a 0 amount transaction.
		if(isset($data['item_type_2']) && !empty($data['item_type_2'])){
			return;
		}

		if($this->getConfig()->activation == 'OrderCreation'){	
			if(in_array($data['invoice_status'], array('approved', 'deposited','pending'))){
				// pending, declined, approved, deposited
				if($data['recurring'] == 0){
					$response->set('amount', $data['item_list_amount_1'])
							 ->set('payment_status' , Rb_EcommerceResponse::PAYMENT_COMPLETE)
							 ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_TRANSACTION_2CHECKOUT_COMPLETED');
				}
			
			// change status if its recurring order creation 
			// otherwise do nothing
			if(isset($data['recurring']) && $data['recurring']){
				$response->set('amount', $data['item_rec_list_amount_1'])
						 ->set('payment_status' , Rb_EcommerceResponse::PAYMENT_COMPLETE)
			 		     ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_TRANSACTION_2CHECKOUT_COMPLETED');
			}
		  }
		}
	}
	
	protected function _fraud_status_changed(Rb_EcommerceResponse $response, Array $data)
	{
		if($this->getConfig()->activation == 'FraudStatus')
		{
			// fail, wait, pass, empty
			if($data['fraud_status'] == 'pass'){
				
				if($data['recurring'] == 0){
					$response->set('amount', $data['invoice_list_amount']);
					$response->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE);
					$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_TRANSACTION_2CHECKOUT_COMPLETED');
				}
				
				if(isset($data['recurring']) && $data['recurring']){
					$response->set('amount', $data['item_rec_list_amount_1']);
					$response->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE);
					$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_TRANSACTION_2CHECKOUT_COMPLETED');
				}
			}	
		}
		
		$status = isset($data['fraud_status']) ? $data['fraud_status'] : Rb_EcommerceResponse::NONE; 
		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_FRAUD_STATUS_CHANGED_'.JString::strtoupper($status));

	}
	
	protected function _ship_status_changed(Rb_EcommerceResponse $response, Array $data)
	{
		//not_shipped, shipped, or empty (if intangible / does not need shipped)		
		if(isset($data['ship_status'])){
			$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_SHIP_STATUS_CHANGED_'.JString::strtoupper($data['ship_status']));
		}
	}
	
	protected function _invoice_status_changed(Rb_EcommerceResponse $response, Array $data)
	{
		if($this->getConfig()->activation == 'OrderCreation')
		{
			// at the time or order creation, if invoice is in pending status, we activate subscription
			// if that actiavted subscription get declined, make a negative transaction.
			 if(strtolower($data['invoice_status']) == 'declined'){
			 	if($data['recurring'] == 0){
					$response->set('amount', -$data['invoice_list_amount']);
					$response->set('payment_status', Rb_EcommerceResponse::PAYMENT_REFUND);
				}
				
				if(isset($data['recurring']) && $data['recurring']){
					$response->set('amount', -$data['item_rec_list_amount_1']);
					$response->set('payment_status', Rb_EcommerceResponse::PAYMENT_REFUND);
				}
			}
 		}
		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_STATUS_INVOICE_STATUS_CHANGED_'.JString::strtoupper($data['invoice_status']));
	}
	
	protected function _refund_issued(Rb_EcommerceResponse $response, Array $data)
	{
		if(isset($data['item_type_2'])){
			$response->set('amount', -$data['item_list_amount_1'])
				 	 ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_STATUS_REFUND_ISSUED')
				 	 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_REFUND);
		}
	}
	
	protected function _recurring_installment_success(Rb_EcommerceResponse $response, Array $data)
	{
		$response->set('amount', $data['item_rec_list_amount_1'])
				 ->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_STATUS_RECURRING_INSTALLMENT_SUCCESS');
	}
	
	protected function _recurring_installment_failed(Rb_EcommerceResponse $response, Array $data)
	{
		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_STATUS_RECURRING_INSTALLMENT_FAILED')
		 	     ->set('payment_status', Rb_EcommerceResponse::PAYMENT_PENDING);
	}
	
	protected function _recurring_stopped(Rb_EcommerceResponse $response, Array $data)
	{
		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_STATUS_RECURRING_STOPPED');
	}

	protected function _recurring_complete(Rb_EcommerceResponse $response, Array $data)
	{
		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_STATUS_RECURRING_COMPLETE');
	}
	
	protected function _recurring_restarted(Rb_EcommerceResponse $response, Array $data)
	{
		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_2CHECKOUT_PROCESSOR_2CHECKOUT_TRANSACTION_STATUS_RECURRING_RESTARTED');
		$response->set('payment_status', Rb_EcommerceResponse::NONE);
	}
}