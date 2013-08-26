<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.PayFast
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * PayFast  Processor 
 * @author Manisha Ranawat
 */
class Rb_EcommerceProcessorPayfast  extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;
	
	public function __construct($config = array())
	{
		parent::__construct($config);		
	}
	
	public function get_invoice_number($response)
	{	
		if(isset($response->data['invoice_number'])){
			return $response->data['invoice_number'];
		}
		
		if(isset($response->data['m_payment_id'])){
			return $response->data['m_payment_id'];
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
		$form 						= JForm::getInstance('rb_ecommerce.processor.payfast', dirname(__FILE__).'/forms/form.xml');

		$object 					= $request->toObject();		
		$user_data					= $object->user_data;
		$payment_data				= $object->payment_data;
		$url_data 					= $object->url_data;
		$config 					= $this->getConfig(false);
		
		$binddata['merchant_id']  	= $config->merchant_id;
		$binddata['merchant_key']  	= $config->merchant_key;
		$binddata['amount']  		= number_format($payment_data->total, 2, '.', '');
		$binddata['item_name']  	= $payment_data->item_name;
		$binddata['m_payment_id']  	= $payment_data->invoice_number;
		$binddata['return_url']  	= !empty($url_data->return_url) ? $url_data->return_url.'&invoice_number='.$payment_data->invoice_number : $config->return_url.'&invoice_number='.$payment_data->invoice_number;
		$binddata['notify_url']  	= !empty($url_data->notify_url) ? $url_data->notify_url : $config->notify_url;
		$binddata['cancel_url']  	= !empty($url_data->cancel_url) ? $url_data->cancel_url.'&invoice_number='.$payment_data->invoice_number : $config->cancel_url.'&invoice_number='.$payment_data->invoice_number;
		$form->bind($binddata);
				
		$response 					= new stdClass();
		$response->data 			= new stdClass();
		$response->data->post_url 	= $this->getPostUrl();
		$response->data->form 		= $form;
		
		return $response;	
	}
	
	public function getPostUrl()
	{
		$url	= 'https://www.payfast.co.za/eng/process';
		if($this->getConfig()->sandbox){
			$url	= 'https://sandbox.payfast.co.za/eng/process';
		}
		
		return $url;
	}
	
	public function process($payfast_response)
	{
		$data		= $payfast_response->data;
		$response 	= new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($data['pf_payment_id']) 	? $data['pf_payment_id'] 	: 0)
				 ->set('subscr_id', 		0)  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status',	Rb_EcommerceResponse::PAYMENT_FAIL)	
				 ->set('message', 		    'PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_TRANSACTION_PAYFAST_PAYMENT_FAILED')		 
		 		 ->set('params', 			$data);
		 		 
 		$validationData = $payfast_response->__post; 		 
 		if($this->isValidIPN($validationData) == false){
			 $response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_TRANSACTION_PAYFAST_INVALID_IPN');
		} 
		else {
			$func_name 	= isset($data['payment_status']) ? 'process_on_payment_'.JString::strtolower($data['payment_status']) : 'EMPTY';
			if(method_exists($this, $func_name)){
				$this->$func_name($response, $data);
			}
			else{
				$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_TRANSACTION_INVALID_TRANSACTION_TYPE_OR_PAYMENT_STATUS');	 
			}
		}
		return $response;
	}
	
	protected function process_on_payment_complete($response, array $data)
	{
		$errors = $this->_validateNotification($data);
		
		if(empty($errors)){
			$response->set('amount', 			$data['amount_gross'])
					 ->set('payment_status',	Rb_EcommerceResponse::PAYMENT_COMPLETE)
					 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_TRANSACTION_PAYFAST_PAYMENT_COMPLETED');
		}else {
			$response->set('message', 	$errors);
		}	
	}
	
/* Validates the notifictaion */
	function _validateNotification(array $data)
    {
    	$errors = array();
    	
		// find the required data from post-data, and match with payment and check reciever email must be same.
    	if($this->getConfig()->merchant_id != $data['merchant_id']) {
            $errors[] = Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_INVALID_PAYFAST_MERCHANT_ID');
        }
        return $errors;
    }
    
	protected function process_on_payment_failed($response, array $data)
	{
		$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_TRANSACTION_PAYFAST_PAYMENT_FAILED');
	}
	
	
	/* Validates the incoming data */
	private function isValidIPN($data)
	{				
		foreach($data as $key => $val ) 
		{
			if($key == 'm_payment_id') $returnString = '';
			if(! isset($returnString)) continue;
			if($key == 'signature') continue;
			$returnString .= $key . '=' . urlencode($val) . '&';
		}

		$returnString = substr($returnString, 0, -1);
		
		if(md5($returnString) != $data['signature']) {
			return false;
		}
	
		$header 	 = "POST /eng/query/validate HTTP/1.0\r\n";
		$header 	.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header 	.= "Content-Length: " . strlen($returnString) . "\r\n\r\n";
		
		$fp 		 = fsockopen($this->getValidationUrl(), 443, $errno, $errstr, 10);
		
		if (!$fp) {
			// HTTP ERROR
			return false;
		} else {
			fputs($fp, $header . $returnString);
			while(! feof($fp)) 
			{
				$res = fgets($fp, 1024);
				if (strcmp($res, "VALID") == 0) {
					fclose($fp);
					return true;
				}
			}
		}
		
		fclose($fp);
		return false;
	}

	
	/* Get Validation URL */
	private function getValidationUrl()
	{
		$url	= 'ssl://www.payfast.co.za';
		if($this->getConfig()->sandbox) {
			$url = 'ssl://sandbox.payfast.co.za';
		}
		return $url;
	}
}
