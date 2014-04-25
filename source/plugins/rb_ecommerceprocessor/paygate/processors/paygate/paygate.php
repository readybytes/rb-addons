<?php

/**
* @copyright	Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Paygate
* @contact		support@readybytes.in
*/

// no direct access
if(!defined( '_JEXEC' )){
	die( 'Restricted access' );
}

/** 
 * Paygate Processor 
 * @author Bhavya Shaktawat
 */
class Rb_EcommerceProcessorPaygate extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;
	
	//paygate transaction status
	const TRANSACTION_STATUS_NOT_DONE				 = 0;
	const TRANSACTION_STATUS_APPROVED				 = 1;
	const TRANSACTION_STATUS_DECLINED		 		 = 2;
	const TRANSACTION_STATUS_SUCCESSFULLY_RECEIVED	 = 5;
	const SUCCESSFUL_REASON_CODE 					 = 990017;
	const CANCEL_REASON_CODE 						 = 990028;

	// XITODO : move this to parent
	public function request(Rb_EcommerceRequest $request)
	{
		$type 	 = $request->get('type');
		$func = '_request_'.$type;
		return $this->$func($request);
	}
	
	public function get_invoice_number($response)
	{
		if(isset($response->data['REFERENCE'])){
			return $response->data['REFERENCE'];
		}

		return 0;
	}
	
		
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$object 		= $request->toObject();		
		$config 		= $this->getConfig();
		$url_data 		= $object->url_data;
		$payment_data 	= $object->payment_data;
		
		//V.IMP : there is no notify url in case of paygate
		//all the data will be returned on return url so for processing this set notify=1 in return url
		$form_data['RETURN_URL'] 		= !empty($url_data->return_url) ? $url_data->return_url : $config->return_url.'&processor=paygate&notify=1&invoice_number='.$payment_data->invoice_number;				
		
		$form_data['PAYGATE_ID'] 		= $this->getConfig()->paygate_id;
		$form_data['REFERENCE'] 		= $payment_data->invoice_number;
		
		//IMP : multiply by 100, because payment gateway does not support decimal	
		$form_data['AMOUNT'] 			= number_format($payment_data->total, 2) * 100 ; 
		$form_data['CURRENCY'] 			= $payment_data->currency;
		$form_data['TRANSACTION_DATE'] 	= date('%Y-%m-%d %H:%M');
		$form_data['CHECKSUM']			= md5($form_data['PAYGATE_ID']."|".$form_data['REFERENCE']."|".$form_data['AMOUNT']."|".
										   $form_data['CURRENCY']."|".$form_data['RETURN_URL']."|".$form_data['TRANSACTION_DATE']."|".
										   $this->getConfig()->encryption_key);
		
		$form_path 						= dirname(__FILE__).'/forms/';	
		$form 							= JForm::getInstance('rb_ecommerce.processor.paygate', $form_path.'form.xml');		
		$form->bind($form_data);
		
		$response 						= new stdClass();		
		$response->data 				= new stdClass();
		$response->data->post_url 		= $this->getPostUrl();;
		$response->data->form 			= $form;
		
		return $response;
	}
	
	protected function getPostUrl()
	{
        return 'https://www.paygate.co.za/paywebv2/process.trans';
	}
	
	public function process($raw_response)
	{		
		$data		= $raw_response->data;	
		$response 	= new Rb_EcommerceResponse(); 
    	
    	$response->set('txn_id', 			isset($data['TRANSACTION_ID']) ? $data['TRANSACTION_ID'] 	: 0)
    			 ->set('subscr_id', 		isset($data['TRANSACTION_ID']) ? $data['TRANSACTION_ID'] 	: 0)
    			 ->set('parent_txn',		0)
				 ->set('amount', 			0)
				 ->set('payment_status',	Rb_EcommerceResponse::PAYMENT_FAIL)
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_PAYGATE_TRANSACTION_PAYMENT_FAILED');
		
		return $this->_process_payment_response($response, $data);
	}	
	
	protected function _process_payment_response(Rb_EcommerceResponse $response, Array $data)
	{
		$CHECKSUM			=	$data['CHECKSUM'];
		$calculatedChecksum = $this->_calculateChecksum($data);
		
		//if the calculated checksum does not match 
	    //the PayGate checksum in the response, then results should be rejected.
		if($calculatedChecksum != $CHECKSUM){
			$data['processor_errors'][] = 'PLG_RB_ECOMMERCEPROCESSOR_PAYGATE_TRANSACTION_INVALID_HASH';
		}
		
		//check the merchant paygateid
		$merchantId = $this->getConfig()->paygate_id;
        if($merchantId != $data['PAYGATE_ID']){
        	$data['processor_errors'][] = 'PLG_RB_ECOMMERCEPROCESSOR_PAYGATE_TRANSACTION_INVALID_MERCHANT_PAYGATEID';
        }

		if( $data['RESULT_CODE'] != self::SUCCESSFUL_REASON_CODE ){
			$data['processor_errors'][] = 'PLG_RB_ECOMMERCEPROCESSOR_PAYGATE_TRANSACTION_PAYMENT_FAILED_REASON_CODE_'.$data['RESULT_CODE'];
		}
		
		if( $data['RISK_INDICATOR'] != 'AX' ){
			$data['processor_errors'][] = 'PLG_RB_ECOMMERCEPROCESSOR_PAYGATE_TRANSACTION_PAYMENT_RISK_INDICATOR';
		}
		
		$amountReturned  = $data['AMOUNT'] / 100 ;
		
		switch ($data['TRANSACTION_STATUS']){
			case 1 :
			case 5 : $response->set('amount', 			$amountReturned);		
					 $response->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_COMPLETE);
					 break;
					 
			case 0 : 
			case 2 :
			
			default: $response->set('payment_status', Rb_EcommerceResponse::PAYMENT_FAIL);
		}
		
		//set the transaction message as per status
		if(isset($data['TRANSACTION_STATUS'])){
			$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYGATE_TRANSACTION_STATUS_'.$data['TRANSACTION_STATUS']);
		}
		
		else {
			$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYGATE_TRANSACTION_NO_STATUS');
		}
				
		$response->set('params', $data);
		return $response;
	}
	
	protected function _calculateChecksum($data)
	{
		$encryption_key 	=   $this->getConfig()->encryption_key;
		
		$PAYGATE_ID			=	$data['PAYGATE_ID'];
	    $REFERENCE			=	$data['REFERENCE'];
	    $AMOUNT				=	$data['AMOUNT'];
	    $TRANSACTION_STATUS =	$data['TRANSACTION_STATUS'];
	    $RESULT_CODE		=	$data['RESULT_CODE'];
	    $RESULT_DESC		=	$data['RESULT_DESC'];
	    $AUTH_CODE			=	$data['AUTH_CODE'];
	    $TRANSACTION_ID		=	$data['TRANSACTION_ID'];
	    $RISK_INDICATOR		=	$data['RISK_INDICATOR'];
	    
	    $checksum_source    = $PAYGATE_ID."|".$REFERENCE."|".$TRANSACTION_STATUS."|"
	    				      .$RESULT_CODE."|".$AUTH_CODE."|".$AMOUNT."|".$RESULT_DESC."|"
	    				      .$TRANSACTION_ID."|";
	    				      
		if ($RISK_INDICATOR){
			$checksum_source .= $RISK_INDICATOR."|";
		}
		
	    $checksum_source .= $encryption_key;
	    
	    return md5($checksum_source);
	}
}
