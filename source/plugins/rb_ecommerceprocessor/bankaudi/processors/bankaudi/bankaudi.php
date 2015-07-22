<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Bankaudi
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Bankaudi Processor 
 */
class Rb_EcommerceProcessorBankaudi extends Rb_EcommerceProcessor
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
		$merchTxnRef       = $this->null2unknown(addslashes($response->__get["merchTxnRef"]));
		if($merchTxnRef){
			return $merchTxnRef;
		}else {
			return 0;
		}
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$form = JForm::getInstance('rb_ecommerce.processor.bankaudi', dirname(__FILE__).'/forms/form.xml');

		$object 					= $request->toObject();		
		$user_data					= $object->user_data;
		$payment_data				= $object->payment_data;
		$url_data 					= $object->url_data;
		$config 					= $this->getConfig(false);
		
		$data['accessCode']  		= $config->access_code;
		$data['merchant']  			= $config->merchant_id;
		$data['merchTxnRef']  		= $payment_data->invoice_number;
		$data['orderInfo']  		= $payment_data->item_name;
		$data['amount']  			= intval((number_format($payment_data->total, 2, '.', '') * 100));
		$data['returnURL']  		= $url_data->return_url.'&invoice_number='.$payment_data->invoice_number.'&notify=1';
			
		ksort($data);
		
		$md5HashData				= $config->secret_hash;;
		$vpcURL 					= '';
		foreach($data as $key => $value) 
		{
		    // create the md5 input and URL leaving out any fields that have no value
		    if (strlen($value) > 0 && ($key == 'accessCode' || $key == 'merchTxnRef' || $key == 'merchant' || $key == 'orderInfo' || $key == 'amount' || $key == 'returnURL'))
		    {
		        // this ensures the first paramter of the URL is preceded by the '?' char
		        if (!isset($appendAmp) || $appendAmp == 0) 
		        {
		            $vpcURL 	.= urlencode($key) . '=' . urlencode($value);
		            $appendAmp 	 = 1;
		        } else {
		            $vpcURL 	.= '&' . urlencode($key) . "=" . urlencode($value);
		        }
		        $md5HashData 	.= $value;
		    }
		}	
		$newHash 					= $vpcURL."&vpc_SecureHash=" . strtoupper(md5($md5HashData));	
		$post_url					= 'https://gw1.audicards.com/TPGWeb/payment/prepayment.action?'.$newHash;
				
		$response 					= new stdClass();
		$response->data 			= new stdClass();
		$response->data->post_url 	= $post_url;
		
		$response->type				=	Rb_EcommerceRequest::BUILD_TYPE_HTML ;
		$response->data->form		=	Rb_HelperTemplate::renderLayout('gateway_bankaudi', $form,  'plugins/rb_ecommerceprocessor/bankaudi/processors/bankaudi/layouts');	
		
		return $response;	
	}
	
	public function process($bankaudi_response)
	{
		$response 	= new Rb_EcommerceResponse();
		
		$secretHash = $this->getConfig()->secret_hash;
		$errors 	= array();
			
		// needs GET data only		
		$data 		= $bankaudi_response->__get;
		$cookies 	= JFactory::getApplication()->input->cookie->getArray();
		foreach($cookies as $key => $value){
			if(isset($data[$key])) unset($data[$key]);
		}

		if(isset($data['option'])) 		unset($data['option']);
		if(isset($data['view'])) 		unset($data['view']);
		if(isset($data['task'])) 		unset($data['task']);
		if(isset($data['processor'])) 		unset($data['processor']);
		if(isset($data['invoice_number'])) 	unset($data['invoice_number']);
		if(isset($data['notify'])) 		unset($data['notify']);
		
		$response->set('txn_id', 			isset($data['vpc_TransactionNo']) ? $data['vpc_TransactionNo'] : 0)
				 ->set('subscr_id', 		0)  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::FAIL)			  
		 		 ->set('params', 			$data);
		 		 
		
		$vpc_Txn_Secure_Hash 	= addslashes($data["vpc_SecureHash"]);
		unset($data["vpc_SecureHash"]); 
		ksort($data);
		
		// set a flag to indicate if hash has been validated
		$errorExists = false;
		
		//check if the value of response code is valid
		if (strlen($secretHash) > 0 && addslashes($data["vpc_TxnResponseCode"]) != "7" && addslashes($data["vpc_TxnResponseCode"]) != "No Value Returned")
		 {	
			//creat an md5 variable to be compared with the passed transaction secure hash to check if url has been tampered with or not
		    $md5HashData = $secretHash;
	
			//creat an md5 variable to be compared with the passed transaction secure hash to check if url has been tampered with or not
		    $md5HashData_2 = $secretHash;
	
		    // sort all the incoming vpc response fields and leave out any with no value
		    foreach($data as $key => $value) 
		    {
		        if ($key != "vpc_SecureHash" && strlen($value) > 0 && $key != 'action' ) 
		        {
					$hash_value 	 = str_replace(" ",'+',$value);
					$hash_value 	 = str_replace("%20",'+',$hash_value);
					$md5HashData_2 	.= $value;
		            $md5HashData 	.= $hash_value;
		            
		        }
	   		}

		    //if transaction secure hash is the same as the md5 variable created 
		    if (strtoupper($vpc_Txn_Secure_Hash) != strtoupper(md5($md5HashData)) && strtoupper($vpc_Txn_Secure_Hash) != strtoupper(md5($md5HashData_2)))
		    {
		    	$response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_BANKAUDI_INVALID_HASH');
		    }
		} 
		
		//the the fields passed from the url to be displayed
		$amount          = $this->null2unknown(addslashes($data["amount"])/100);
		$txnResponseCode = $this->null2unknown(addslashes($data["vpc_TxnResponseCode"]));
			
		// if $txnResponseCode == 0, it means transaction successfull
		if(!is_numeric($txnResponseCode) ||  (int)$txnResponseCode !== 0)
		{			
			$response->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_FAIL)
				     ->set('message',  			$this->getResponseDescription($txnResponseCode));
			return $response;
		}
		
		//store the response in the payment AND save the payment
		$data["vpc_SecureHash"] 	= $vpc_Txn_Secure_Hash;
		
		$response->set('payment_status', 	 Rb_EcommerceResponse::PAYMENT_COMPLETE)
				 ->set('amount', 			$amount)
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_BANKAUDI_PAYMENT_COMPLETED');
				 
		return $response;
	}
	
	function getResponseDescription($responseCode) 
	{
	    switch ($responseCode) {
	        case "0" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_SUCCESSFUL');break;
	        case "?" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_STATUS_IS_UNKNOWN'); break;
	        case "1" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_UNKNOWN_ERROR'); break;
	        case "2" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_BANK_DECLINED_TRANSACTION'); break;
	        case "3" 	: $result   = Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_NO_REPLY_FROM_BANK'); break;
	        case "4" 	: $result   = Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_EXPIRED_CARD'); break;
	        case "5" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_INSUFFICIENT_FUNDS'); break;
	        case "6" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_ERROR_COMMUNICATING_WITH_BANK'); break;
	        case "7" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_PAYMENT_SERVER_SYSTEM_ERROR'); break;
	        case "8" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_TYPE_NOT_SUPPORTED'); break;
	        case "9" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_BANK_DECLINED_TRANSACTION'); break;
	        case "A" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_ABORTED'); break;
	        case "C" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_CANCELLED'); break;
	        case "D" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_DEFERRED_TRANSACTION'); break;
	        case "E" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_INVALID_CREDIT_CARD'); break;
	        case "F" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_3D_SECURE_AUTHENTICATION_FAILED'); break;
	        case "I" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_CARD_SECURITY_CODE_VERIFICATION_FAILED'); break;
	        case "G" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_INVALID_MERCHANT'); break;
	        case "L" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_SHOPPING_TRANSACTION_LOCKED'); break;
	        case "N" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_CARDHOLDER_IS_NOT_ENROLLED'); break;
	        case "P" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_IS_BEING_PROCESSED'); break;
	        case "R" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_WAS_NOT_PROCESSED'); break;
	        case "S" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_DUPLICATE_SESSIONID'); break;
	        case "T" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_ADDRESS_VERIFICATION_FAILED'); break;
	        case "U" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_CARD_SECURITY_CODE_FAILED'); break;
	        case "V"	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_ADDRESS_VERIFICATION_AND_CARD_SECURITY_CODE_FAILED'); break;
	        case "X" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_CREDIT_CARD_BLOCKED'); break;
	        case "Y" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_INVALID_URL'); break;                
	        case "B" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_WAS_NOT_COMPLETED'); break;                
	        case "M" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_PLEASE_ENTER_ALL_REQUIRED_FIELDS'); break;                
	        case "J" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_ALREADY_IN_USE'); break;
	        case "BL" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_CARD_BIN_LIMIT_REACHED'); break;                
	        case "CL" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_CARD_LIMIT_REACHED'); break;                
	        case "LM" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_MERCHANT_AMOUNT_LIMIT_REACHED'); break;                
	        case "Q" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_IP_BLOCKED'); break;                
	        case "R" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_TRANSACTION_WAS_NOT_PROCESSED'); break;                
	        case "Z" 	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_BIN_BLOCKED'); break;

	        default  	: $result 	= Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_BANKAUDI_TRANSACTION_MSG_UNABLE_TO_BE_DETERMINED'); 
	    }
	    return $result;
	}
	
	//function to display a No Value Returned message if value of field is empty
	function null2unknown($data) 
	{
	    if ($data == "") 
	        return false;
	     else 
	        return $data;
	}

}