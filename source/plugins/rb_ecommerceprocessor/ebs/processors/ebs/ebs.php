<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.EBS
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * EBS Processor 
 * @author Manisha Ranawat
 */
class Rb_EcommerceProcessorEbs extends Rb_EcommerceProcessor
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
		if(isset($response->data['MerchantRefNo'])){
			return $response->data['MerchantRefNo'];
		}
		
		return 0;
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$form 			= JForm::getInstance('rb_ecommerce.processor.ebs', dirname(__FILE__).'/forms/form.xml');

		$object 		= $request->toObject();		
		$payment_data	= $object->payment_data;
		$url_data 		= $object->url_data;
		
    	$mode			= 'TEST';
    	if($this->getConfig()->sandbox){
    		$mode		=	'LIVE';	
    	}
    	
    	$return_url  	 							= !empty($url_data->return_url) ? $url_data->return_url.'&DR={DR}' : $config->return_url.'&DR={DR}';
    	$hash         								= $this->getConfig()->secret_key."|".$this->getConfig()->account_id."|". number_format($payment_data->total, 2, '.', '')."|".$payment_data->invoice_number."|".$return_url."|".$mode;
		$secure_hash  								= md5($hash);
		
		$binddata['payment_data']['amount']			= number_format($payment_data->total, 2, '.', '');;
		$binddata['payment_data']['mode'] 			= $mode;
		$binddata['payment_data']['account_id']		= $this->getConfig()->account_id;	
		$binddata['payment_data']['reference_no']	= $payment_data->invoice_number;	
		$binddata['payment_data']['description']	= $payment_data->item_name;	
		$binddata['payment_data']['return_url']		= $return_url;	
		$binddata['payment_data']['secure_hash']	= $secure_hash;	
		$form->bind($binddata); 
		
		$response 									= new stdClass();
		$response->type 							= 'form';
		$response->data 							= new stdClass();
		$response->data->post_url 					= $this->getPostUrl();
		$response->data->form 						= $form;
		
		return $response;
	}
	
	protected function getPostUrl()
	{	
		$url  = 'https://testing.secure.ebs.in/pg/ma/sale/pay';
		if($this->getConfig()->sandbox){
			$url  = 'https://secure.ebs.in/pg/ma/sale/pay/';	
		}
		
		return $url;
	}
	
	
	public function process($ebs_response)
	{	
		// Rc43 file for encryption and decryption of response
		require_once 'Rc43.php';
		
		$response_data	= $ebs_response->data;
		$secret_key 	= $this->getConfig()->secret_key;
		$data			= array();
		 
		if(isset($data['DR'])) 
		{
		 	$DR 			= preg_replace("/\s/","+",$response_data['DR']);
		 	$rc4 			= new RBCrypt_RC4($secret_key);
	 	 	$QueryString 	= base64_decode($DR);
		 	$rc4->decrypt($QueryString);
		 	$QueryString 	= explode('&',$QueryString);
	
		 	foreach($QueryString as $param)
		 	{
		 		$param 					= explode('=',$param);
				$data[$param[0]] 		= urldecode($param[1]);
		 	}
		 }
		 
		return $this->_process_payment_response($data);
	}
	
	protected function _process_payment_response($data)
	{
		$response 		= new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($data['TransactionID']) 	? $data['TransactionID'] 	: 0)
				 ->set('subscr_id', 		isset($data['PaymentID']) 		? $data['PaymentID'] 		: 0)  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_FAIL)	
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_EBS_TRANSACTION_EBS_PAYMENT_FAILED')		 
		 		 ->set('params', 			$data);
		 
		if(!$data['ResponseCode'])
		{
			$response->set('amount', 		$data['Amount'])
					 ->set('message', 		'PLG_RB_ECOMMERCEPROCESSOR_EBS_TRANSACTION_EBS_PAYMENT_COMPLETED')
					 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE);
		}
		
		return $response;
	}
}
