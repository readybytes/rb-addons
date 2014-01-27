<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Eway
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Eway Processor 
 * @author Manisha Ranawat
 */
class Rb_EcommerceProcessorEway extends Rb_EcommerceProcessor
{
	protected $_location = __FILE__;
	
	/**
	 * @var SoapClient
	 */
	protected $_client   = null;

	// If Payment method support for refund then set it true otherwise set flase
	protected $_support_refund = true;
	
	public function request(Rb_EcommerceRequest $request)
	{
		$type 	= $request->get('type');
		$func 	= '_request_'.$type;
		return $this->$func($request);
	}
		
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$form = JForm::getInstance('rb_ecommerce.processor.eway', dirname(__FILE__).'/forms/form.xml');

		$object 								= $request->toObject();		
		$user_data								= $object->user_data;
		
		$binddata['payment_data']['first_name'] = $user_data->name;
		$binddata['payment_data']['email']		= $user_data->email;	
		$form->bind($binddata); 
		
		$response 								= new stdClass();
		$response->type 						= 'form';
		$response->error 						= false;
		$response->data 						= new stdClass();
		$response->data->post_url 				= false;
		$response->data->form 					= $form;
		
		return $response;
	}
	
	protected function _request_payment(Rb_EcommerceRequest $request)
	{
		$object 			= $request->toObject();		
		$processor_data 	= $object->processor_data;		
		$config 			= $this->getConfig(false);
		$user_data			= $object->user_data;
		$post_data			= $object->post_data;
		$payment_data		= $object->payment_data;
				
		try {
			$this->_initiate_gateway($config);
		} catch (SoapFault $e) {
          	$response->data = $e;
			return $response;
        }	
		
		// Step 1 :- Create Customer Profile and Customer Payment Profile
		if(!isset($processor_data->profileId) || !$processor_data->profileId){	
			return $this->__request_payment_create_profile($object, $config);
		} 
		else {
			return $this->__request_payment_create_transaction($object, $config);	
		}
	}
	
	protected function _request_refund(Rb_EcommerceRequest $request)
	{
		$object 			= $request->toObject();		
		$processor_data 	= $object->processor_data;	
		$payment_data 		= $object->payment_data;		
		$config 			= $this->getConfig(false);
		
		$response 			= new stdClass();
				
		try {
			$this->_initiate_gateway($config);
		} catch (SoapFault $e) {
          	$response->data = $e;
        }		
		
		if(isset($object->post_data->txn_id))
		{
			$profileId 	   		   			= $processor_data->profileId;
			$refund_amount 		   			= (number_format($payment_data->total, 2) *100);
			$gatewayTransactionId  			= $object->post_data->txn_id;
			$password						= strtotime('now');   //The refund password defined by us - this is NOT the password used to login to MYeWAY.  
        	$params 			   			= "<ewaygateway>
        										<ewayCustomerID>{$profileId}</ewayCustomerID>
        										<ewayOriginalTrxnNumber>{$gatewayTransactionId}</ewayOriginalTrxnNumber>
							    				<ewayTotalAmount>{$refund_amount}</ewayTotalAmount>
							    				<ewayCardExpiryMonth></ewayCardExpiryMonth>
							    				<ewayCardExpiryYear></ewayCardExpiryYear>
							    				<ewayOption1></ewayOption1>
							    				<ewayOption2></ewayOption2>
							    				<ewayOption3></ewayOption3>
							    				<ewayRefundPassword>{$password}</ewayRefundPassword></ewaygateway>
							    				";
        
			$response->data 				= $this->_client->__doRequest($params,"https://www.eway.com.au/gateway/xmlpaymentrefund.asp","POST","1.2");
			$response->data					= simplexml_load_string($response->data);
			
			// VVV IMP : Set transaction_refund variable to check that response generated for refund or not
			$response->transaction_refund 	= true;
		}

		return $response;
	}
	
	public function getPostUrl()
	{	
		$url  = 'https://www.eway.com.au/gateway/ManagedPaymentService/managedCreditCardPayment.asmx?WSDL';
		
		if($this->getConfig()->sandbox){
			$url  = 'https://www.eway.com.au/gateway/ManagedPaymentService/test/managedCreditCardPayment.asmx?WSDL';	
		}
		
		return $url;
	}
	
	//@todo add a check to see if SoapClient class exists, if not then use nusoap
    //This connects to the soap API so calls can be executed to send and receive data
    private function _initiate_gateway($config)
    {
        // Test account is static, so that customer_id, username and password are fixed for test account
        $ewayCustomerId 	= !empty($config->customer_id) ? $config->customer_id : '87654321'; 
        $ewayUsername 		= !empty($config->username)    ? $config->username    : "test@eway.com.au";
        $ewayPassword 		= !empty($config->password)    ? $config->password    : "test123";
        $url 				= $this->getPostUrl();

        $this->_client = new SoapClient($url,array("exceptions" => 1));     

        $args = array(
            'eWAYCustomerID'	=> $ewayCustomerId,
            'Username'			=> $ewayUsername,
            'Password'			=> $ewayPassword);

        $header = new SoapHeader( "https://www.eway.com.au/gateway/managedpayment", 'eWAYHeader', $args);
        $this->_client->__setSoapHeaders(array($header));
        return true;  
    }
    
 	protected function __request_payment_create_profile($object, $config, $url=false)
    {
    	$response 			= new stdClass();
		$response->error 	= false;	
		
		$post_data			= $object->post_data;
		$payment_data  	 	= $object->payment_data;
		$user_data			= $object->user_data;	
		
        $params = array(
            'Title'			=> isset($post_data->name_title) 		? $post_data->name_title  					: "",
            'FirstName'		=> isset($post_data->first_name) 		? $post_data->first_name  					: "",
            'LastName'		=> isset($post_data->last_name)  		? $post_data->last_name   					: "",
            'Address'		=> isset($post_data->address)    		? $post_data->address     					: "",
            'Suburb'		=> isset($post_data->city)       		? $post_data->city        					: "",
            'State'			=> isset($post_data->state)      		? $post_data->state      					: "",
            'Company'		=> isset($post_data->company)    		? $post_data->company     					: "",
            'PostCode'		=> isset($post_data->postcode)        	? $post_data->postcode           			: "",
            'Country'		=> isset($post_data->country)    		? strtolower($post_data->country) 			: "",
            'Email'			=> isset($post_data->email)      		? $post_data->email                 		: "",
            'Fax'			=> isset($post_data->fax)        		? $post_data->fax 							: "",
            'Phone'			=> isset($post_data->phone)      		? $post_data->phone 						: "",
            'Mobile'		=> isset($post_data->mobile)     		? $post_data->mobile                		: "",
            'CustomerRef'	=> $user_data->name,
            'JobDesc'		=> "",
            'Comments'		=> "",
            'URL'			=> "",
            'CCNumber'		=> isset($post_data->card_number) 		? trim($post_data->card_number)				: "",
            'CCNameOnCard'	=> isset($post_data->card_name)     	? $post_data->card_name						: "",
            'CCExpiryMonth'	=> isset($post_data->expiration_month) 	? $post_data->expiration_month				: "",
            'CCExpiryYear'	=> isset($post_data->expiration_year)   ? substr($post_data->expiration_year, -2) 	: ""
        );

        try {
            $response->data  = $this->_client->CreateCustomer($params);
        } 
        catch (SoapFault $e) {	
			$response->data = $e;
        }
        //managedcustomerID
        return $response;
    }
    
	public function process($eway_response)
	{	
		if($eway_response->data instanceof SoapFault){
			return $this->_process_connection_error($eway_response->data);
		}
		elseif($eway_response->data->CreateCustomerResult){
			return $this->_process_customer_response($eway_response->data);
		}
		elseif($eway_response->data->ewayResponse){
			return $this->_process_payment_response($eway_response->data->ewayResponse);
		}
		elseif($eway_response->transaction_refund){
			return $this->_process_refund_response($eway_response->data);
		}
		else {
			return $this->_process_error_response($eway_response);
		}
		
	}
    
	private function __request_payment_create_transaction($object, $config, $url = '')
	{
		$response 			= new stdClass();
		$response->error 	= false;	
		
		$payment_data   	= $object->payment_data;
		$processor_data	 	= $object->processor_data;	
		
		$params = array(
            'managedCustomerID'  	=> $processor_data->profileId,
            'amount'				=> (number_format($payment_data->total, 2) *100),
            'invoiceReference'		=> $payment_data->invoice_id,
            'invoiceDescription'	=> $payment_data->item_name
        );

        try {
            $response->data = $this->_client->ProcessPayment($params);
        } catch (SoapFault $e) {
           $response->data = $e;
        }

       	return $response;        
	}
	
	protected function _process_connection_error($eway_response)
	{
		$response = new Rb_EcommerceResponse();
    	
		$response->set('txn_id', 	 		0); 
    	$response->set('subscr_id',  		0);
    	$response->set('parent_txn', 		0);
		$response->set('amount', 	 		0);
		$response->set('payment_status', 	Rb_EcommerceResponse::FAIL);
		$response->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_EWAY_TRANSACTION_EWAY_CONNECTION_ERROR');
		$response->set('params', 			$eway_response);
		
		return $response;
	}
	
	protected function _process_customer_response($eway_response)
	{
		$processor_data 					= new stdClass();
        $processor_data->profileId 			= $eway_response->CreateCustomerResult;	       
        
		$response = new Rb_EcommerceResponse();   	
    	
		$response->set('txn_id', 	 		$eway_response->CreateCustomerResult);
    	$response->set('subscr_id',  		$eway_response->CreateCustomerResult);
    	$response->set('parent_txn', 		0);
    	$response->set('payment_status', 	Rb_EcommerceResponse::SUBSCR_START);
		$response->set('amount', 	 		0);
		$response->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_EWAY_TRANSACTION_EWAY_PROFILE_CREATED');
		$response->set('params', 			$eway_response);
		$response->set('processor_data', 	$processor_data);
	
		// IMP :::
		$response->set('next_request', true);
		$response->set('next_request_name', 'payment');
	
		return $response;
	}
	
	protected function _process_payment_response($eway_response)
	{
		$response = new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($eway_response->ewayTrxnNumber) ? $eway_response->ewayTrxnNumber : 0)
				 ->set('subscr_id', 		isset($eway_response->ewayTrxnNumber) ? $eway_response->ewayTrxnNumber : 0)  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::NOTIFICATION)	
				 ->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_EWAY_TRANSACTION_EWAY_NOTIFICATION')		 
		 		 ->set('params', 			$eway_response);
		 
		if($eway_response->ewayTrxnStatus == true)
		{
			$response->set('amount', 		($eway_response->ewayReturnAmount / 100))
					 ->set('message', 		'PLG_RB_ECOMMERCEPROCESSOR_EWAY_TRANSACTION_EWAY_PAYMENT_COMPLETED')
					 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE);
		}
		else {
			$response->set('amount', 		($eway_response->ewayReturnAmount / 100))
					 ->set('message', 		'PLG_RB_ECOMMERCEPROCESSOR_EWAY_TRANSACTION_EWAY_PAYMENT_FAILED')
					 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_FAIL);
		}
		return $response;
	}
	
	protected function _process_refund_response($eway_response)
	{
		$response = new Rb_EcommerceResponse();
		
		$response->set('txn_id', 			isset($eway_response['ewayTrxnNumber']) ? $eway_response['ewayTrxnNumber'] : 0)
 				 ->set('subscr_id', 		0)  
				 ->set('parent_txn', 		0)
				 ->set('payment_status',  	Rb_EcommerceResponse::NOTIFICATION)
				 ->set('params', 			$eway_response);
		
		if($eway_response['ewayTrxnStatus'] == true)
		{
			$response->set('amount',          	$eway_response['ewayReturnAmount'] / 100)
				 	 ->set('payment_status',  	Rb_EcommerceResponse::PAYMENT_REFUND)	
				 	 ->set('message',        	'PLG_RB_ECOMMERCEPROCESSOR_EWAY_TRANSACTION_EWAY_PAYMENT_REFUNDED')		 
	 	 			 ->set('params',         	$eway_response)
	 	 			 ->set('parent_txn', 		isset($eway_response['ewayTrxnNumber']) ? $eway_response['ewayTrxnNumber'] 			 : 0)
					 ->set('txn_id', 			isset($eway_response['ewayTrxnNumber']) ? $eway_response['ewayTrxnNumber'].'_refund' : 'refund');
		}
		
		return $response;
	}
	
	protected function _process_error_response($eway_response)
	{
		$response = new Rb_EcommerceResponse();
    	
		$response->set('txn_id', 	 		0); 
    	$response->set('subscr_id',  		0);
    	$response->set('parent_txn', 		0);
		$response->set('amount', 	 		0);
		$response->set('payment_status', 	Rb_EcommerceResponse::NOTIFICATION);
		$response->set('message', 			'PLG_RB_ECOMMERCEPROCESSOR_EWAY_TRANSACTION_EWAY_NOTIFICATION');
		$response->set('params', 			$eway_response);
		
		return $response;
	}
	
}
