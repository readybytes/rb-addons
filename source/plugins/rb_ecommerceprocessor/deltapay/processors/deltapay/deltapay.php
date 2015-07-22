<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.DeltaPay 
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * DeltaPay  Processor 
 * @author Manisha Ranawat
 */
class Rb_EcommerceProcessorDeltapay  extends Rb_EcommerceProcessor
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
		if(isset($response->data['Param1'])){
			return $response->data['Param1'];
		}
		
		return 0;
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{	
		$object 	= $request->toObject();	
		$guids 		= $this->_getGuids($object);
				
		$response 					= new stdClass();
		$response->data 			= new stdClass();
		$response->data->post_url 	= 'https://www.deltapay.gr/entry.asp';
		
		$response->type				= Rb_EcommerceRequest::BUILD_TYPE_HTML ;
		$form						= array();
		$form['currency']  			= $object->payment_data->currency;
		$form['Guid1']				= (empty($guids))?'':$guids['guid1'];
		$response->data->form		= Rb_HelperTemplate::renderLayout('gateway_deltapay', $form,  'plugins/rb_ecommerceprocessor/deltapay/processors/deltapay/layouts');
				
		return $response;	
	}
	
	function  _getGuids($data)
	{
		$payment_data	= $data->payment_data;
		$user_data		= $data->user_data;

		//Deltapay asks always for two demicals and have to use ',' as decimal seperator
		$amount       	= number_format($payment_data->total, '2', ',', '');
    	$merchantId   	= $this->getConfig()->merchant_id;
    	$currencyCode 	= $payment_data->currency;
    	$name		 	= $user_data->name;
    	$email			= $user_data->email;
    	$invoice_number	= $payment_data->invoice_number;
    	
    	if($currencyCode != 'EUR'){
    		return array();
    	}
    	
    	$currencyCode	= '978';
		
		$url 			= "https://www.deltapay.gr/getguid.asp";
		$post_data 		= "MerchantCode=".$merchantId."&Charge=".$amount."&CurrencyCode=".$currencyCode."&CardHolderName=".$name."&CardHolderEmail=".$email."&Param1=".$invoice_number;
		
		$httpResponse	= $this->curl_request($url, $post_data);
		
		//create array of parameter
		$array_httpResponse 		= explode('<br>',$httpResponse);
		
		$result['guid1'] 			= isset($array_httpResponse[0])?$array_httpResponse[0]:'';
		$result['guid2'] 			= isset($array_httpResponse[1])?$array_httpResponse[1]:'';
		$result['error_message']	= isset($array_httpResponse[2])?$array_httpResponse[2]:'';
		
		return $result;
	}
	
	public function curl_request($url, $post_data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// Set the API operation, version, and API signature in the request.
		
		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		
		// Get response from the server.
		return curl_exec($ch);
	}
	
	public function process($delta_response)
	{
		$data		= $delta_response->data;
		
		$response 	= new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($data['DeltaPayId']) ? $data['DeltaPayId'] : 0)
				 ->set('subscr_id', 		0)  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::NOTIFICATION)	
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_DELTAPAY_TRANSACTION_DELTAPAY_NOTIFICATION')		 
		 		 ->set('params', 			$data);
		 		 
		$func_name = isset($data['Result']) ? '_process_deltapay_result_'.JString::strtoupper($data['Result']) : 'EMPTY';
		
		if(method_exists($this, $func_name)){
			$result = $this->$func_name($response, $data);
		}
		
		return $response;
	}
	
	function _process_deltapay_result_1($response, $data)
	{
		$amount  = str_replace(',','.',$data['Charge']);
		
		$response->set('amount',			$amount)
				 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_COMPLETE)
		 		 ->set('message',			'PLG_RB_ECOMMERCEPROCESSOR_DELTAPAY_TRANSACTION_PAYMENT_COMPLETED');		 			
	}
	
	function _process_deltapay_result_2($response, $data)
	{
		$response->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_FAIL)
				 ->set('message',			$data['ErrorMessage']);
	}
	
}
