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
        $form                       = JForm::getInstance('rb_ecommerce.processor.payfast', dirname(__FILE__).'/forms/form.xml');

        $object                     = $request->toObject();     
        $user_data                  = $object->user_data;
        $payment_data               = $object->payment_data;
        $url_data                   = $object->url_data;
        $config                     = $this->getConfig(false);
        
        if($config->sandbox) 
        {            
            $binddata['merchant_id']    ='10000103';
            $binddata['merchant_key']   = '479f49451e829';
        }
        else
        {            
            $binddata['merchant_id']    = $config->merchant_id;
            $binddata['merchant_key']   = $config->merchant_key;
        }
        
        $binddata['return_url']     = !empty($url_data->return_url) ? $url_data->return_url.'&invoice_number='.$payment_data->invoice_number : $config->return_url.'&invoice_number='.$payment_data->invoice_number;
        $binddata['cancel_url']     = !empty($url_data->cancel_url) ? $url_data->cancel_url.'&invoice_number='.$payment_data->invoice_number : $config->cancel_url.'&invoice_number='.$payment_data->invoice_number;
        $binddata['notify_url']     = !empty($url_data->notify_url) ? $url_data->notify_url : $config->notify_url;
        $binddata['m_payment_id']   = $payment_data->invoice_number;
        $binddata['amount']         = number_format($payment_data->total, 2, '.', '');
        $binddata['item_name']      = $payment_data->item_name;

        $sigString = '';
        foreach( $binddata as $key => $val )
        {
            if(!empty($val))
            {
              $sigString .= $key .'='. urlencode( $val  ) .'&';
            }
        }
        // Remove last ampersand
        $getString = substr( $sigString, 0, -1 );

        $binddata['signature'] = md5( $getString );
        $form->bind($binddata);
                
        $response                   = new stdClass();
        $response->data             = new stdClass();
        $response->data->post_url   = $this->getPostUrl();        

		$response->type			=	Rb_EcommerceRequest::BUILD_TYPE_HTML ;
		$response->data->form	=	Rb_HelperTemplate::renderLayout('gateway_payfast', $form,  'plugins/rb_ecommerceprocessor/payfast/processors/payfast/layouts');

        return $response;   
    }
    
    public function getPostUrl()
    {
        $url    = 'https://www.payfast.co.za/eng/process';
        if($this->getConfig()->sandbox){
            $url    = 'https://sandbox.payfast.co.za/eng/process';
        }
        
        return $url;
    }
    
    public function process($payfast_response)
    {
        $data       = $payfast_response->data;
        $response   = new Rb_EcommerceResponse();
        
        $response->set('txn_id',            isset($data['pf_payment_id'])   ? $data['pf_payment_id']    : 0)
                 ->set('subscr_id',         0)  
                 ->set('parent_txn',        0)
                 ->set('amount',            0)
                 ->set('payment_status',    Rb_EcommerceResponse::PAYMENT_FAIL) 
                 ->set('message',           'PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_TRANSACTION_PAYFAST_PAYMENT_FAILED')      
                 ->set('params',            $data);
                 
        $validationData = $payfast_response->__post;         
        if($this->isValidIPN($validationData) == false){
             $response->set('message', 'PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_TRANSACTION_PAYFAST_INVALID_IPN');
        } 
        else {
            $func_name  = isset($data['payment_status']) ? 'process_on_payment_'.JString::strtolower($data['payment_status']) : 'EMPTY';
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
            $response->set('amount',            $data['amount_gross'])
                     ->set('payment_status',    Rb_EcommerceResponse::PAYMENT_COMPLETE)
                     ->set('message',           'PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_TRANSACTION_PAYFAST_PAYMENT_COMPLETED');
        }else {
            $response->set('message',   $errors);
        }   
    }
    
/* Validates the notifictaion */
    function _validateNotification(array $data)
    {
        $errors = array();
        
        // find the required data from post-data, and match with payment and check reciever email must be same.
        if($this->getConfig()->merchant_id != $data['merchant_id'] && !$this->getConfig()->sandbox) {
            $errors[] = JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYFAST_INVALID_PAYFAST_MERCHANT_ID');
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
    	// if test case execution then no need to Validate IPN (Only for unit test case)
    	if(defined('RBTEST_BASE')){
    		return true;
    	}   

        if($this->getConfig()->proxy_server)
        {
	        // Variable initialization
	        $validHosts = array(
	            'www.payfast.co.za',
	            'sandbox.payfast.co.za',
	            'w1w.payfast.co.za',
	            'w2w.payfast.co.za',
	         );
	
	        $validIps = array();
	
	        foreach( $validHosts as $pfHostname )
	        {
	            $ips = gethostbynamel( $pfHostname );
	
	            if( $ips !== false )
	                $validIps = array_merge( $validIps, $ips );
	        }
	
	        // Remove duplicates
	        $validIps = array_unique( $validIps );
	     
	        if( !in_array( $_SERVER['REMOTE_ADDR'], $validIps ) )
	        {
	            return false;
	        }
        }

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
               
     	$link     	= new JURI($this->getValidationUrl()); 
        $curl     	= new JHttpTransportCurl(new Rb_Registry());
     	$response   = $curl->request('POST', $link, $returnString);     
	    
	    $lines = explode( "\r\n", $response->body );
		$verifyResult = trim( $lines[0] );
	 
		if( strcasecmp( $verifyResult, 'VALID' ) != 0 ){
			return false;
		}
		
		return true;        
    }

    
    /* Get Validation URL */
    private function getValidationUrl()
    {
        $url    = 'https://www.payfast.co.za/eng/query/validate';
        if($this->getConfig()->sandbox) {
            $url = 'https://sandbox.payfast.co.za/eng/query/validate';
        }
        return $url;
    }
}
