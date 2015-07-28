<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.ccavenue
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or	die( 'Restricted access' );

/** 
 * ccavenue Processor 
 *
 */
class Rb_EcommerceProcessorCcavenue extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;

	// XITODO : move this to parent
	public function request(Rb_EcommerceRequest $request)
	{
		$type 	 = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	public function get_invoice_number($response)
		{
			if(isset($response->data['invoice_number'])){
				return $response->data['invoice_number'];
			}
			
			if(isset($response->data['merchant_param1'])){
					return $response->data['merchant_param1'];
				}
			return 0;
		}
	
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$object = $request->toObject();		
		$config = $this->getConfig();
		
		$url_data 		= $object->url_data;
		$payment_data 	= $object->payment_data;
		
		
		$data = array();
		
		// common parameters
		$data['merchant_id'] 		= $this->getConfig()->merchant_id;
		$data['order_id'] 			= $payment_data->invoice_number;
		$data['currency'] 			= $payment_data->currency;
		$data['amount'] 			= $payment_data->total;	
		$data['redirect_url'] 		= !empty($url_data->return_url) ? $url_data->return_url.'&invoice_number='.$payment_data->invoice_number.'&notify=1': $config->return_url.'&invoice_number='.$payment_data->invoice_number.'&notify=1';
		$data['cancel_url'] 		= !empty($url_data->cancel_url) ? $url_data->cancel_url.'&invoice_number='.$payment_data->invoice_number : $config->cancel_url.'&invoice_number='.$payment_data->invoice_number;

		//RB_TODO:-  right now ccavenue supports only english language. In future we need to correct it.
		$data['language']   		= "EN";
		$data['merchant_param1']	= $payment_data->invoice_number;
		$form_path = dirname(__FILE__).'/forms/';
        
		$form_data['encRequest']  	= $this->initiatePaymentRequest($data);
		
		$form_data['access_code'] 	= $this->getConfig()->access_code;			//Shared by CCAVENUES

		$response->type				=	Rb_EcommerceRequest::BUILD_TYPE_HTML ;
		$form						=	Rb_HelperTemplate::renderLayout('gateway_ccavenue' , $form_data,  'plugins/rb_ecommerceprocessor/ccavenue/processors/ccavenue/layouts');	
		
		$response 					= new stdClass();		
		$response->data 			= new stdClass();
		$response->data->post_url 	= $this->getPostUrl();
		$response->data->form 		= $form;
		
		return $response;
	}
	

 public function getPostUrl()
    {
        $url    = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';	
        if($this->getConfig()->sandbox){
        	$url =  "https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction";
           
        }
        
        return $url;
    }
		
	public function initiatePaymentRequest($form_data)
			{
				require_once 'Crypto.php';
				//error_reporting(0);
		
				$merchant_data						= '';
				$working_key						= $this->getConfig()->encyption_key;		//Shared by CCAVENUES
				$access_code						= $this->getConfig()->access_code;			//Shared by CCAVENUES
				$merchant_id						= $this->getConfig()->merchant_id;		//Shared by CCAVENUES
					
				foreach ($form_data as $key => $value){
		 				$merchant_data .= $key.'='.urlencode($value).'&';
				}
				
				$encrypted_data		= encrypt($merchant_data,$working_key);
				return $encrypted_data;
			}
	
	
	
	public function process($response)
	{	
			// Decrypt response sent by CcAvenue server
			list($orderStatus, $decryptResponse)	= $this->validateData($response);
			
			return $this->_processPayment($orderStatus, $decryptResponse);
	}
	
	
	public function _processPayment( $orderStatus, $data)
	{
		
		$response = new Rb_EcommerceResponse(); 
    	
    	$response->set('txn_id', 	 isset($data['tracking_id']) 		? $data['tracking_id'] 		 : 0);
    	$response->set('subscr_id',  isset($data['Customer_identifier']) 		? $data['Customer_identifier'] 	 : 0);
    	$response->set('parent_txn', 0);    	
		$response->set('amount', 	 0);
		$response->set('params', $data);
	
			$error	= '';
			if($orderStatus === "Success")
			{
				$message = JText::_('PLG_RB_ECOMMERCEPROCESSOR_CCAVENUE_PAYMENT_PROCESSED');

				$response->set('message', $message)
						 ->set('amount', 	$data['amount'])
						 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE);	
			}
			else if($orderStatus === "Aborted"){
				$error = JText::_('PLG_RB_ECOMMERCEPROCESSOR_CCAVENUE_TRANSACTION_ABORTED');
				$response->set('message', $error)
						 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_FAIL);	
			}
			else if($orderStatus === "Failure"){
				$error = JText::_('PLG_RB_ECOMMERCEPROCESSOR_CCAVENUE_TRANSACTION_FAILED');
				$response->set('message', $error)
						 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_FAIL);	
			}
			else{
				$error = JText::_('PLG_RB_ECOMMERCEPROCESSOR_CCAVENUE_TRANSACTION_ILLEGAL');
				$response->set('message', $error)
						 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_FAIL);	
			}
			
			return $response;
		}
		
		public function validateData($res)
		{
			require_once 'Crypto.php';
			//error_reporting(0);
			
			$workingKey			= $this->getConfig()->encyption_key;		//Working Key should be provided here.
			$encResponse		= $res->data["encResp"];								//This is the response sent by the CCAvenue Server
			$rcvdString			= decrypt($encResponse,$workingKey);				//Crypto Decryption used as per the specified working key.
			$order_status		= "";
			$decryptValues		= explode('&', $rcvdString);
			$dataSize			= sizeof($decryptValues);
		
			for($i = 0; $i < $dataSize; $i++) 
			{
				$information		= explode('=',$decryptValues[$i]);
				if($i==3){
					$order_status 	= $information[1];
				}
			}
			
			for($i = 0; $i < $dataSize; $i++) 
			{
				$information					= explode('=',$decryptValues[$i]);
				$response[$information[0]]	 	= urldecode($information[1]);
			}
	
			return array($order_status, $response);
		}
	
}