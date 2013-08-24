<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Netcash
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Netcash Processor 
 * @author Manisha Ranawat
 */
class Rb_EcommerceProcessorNetcash extends Rb_EcommerceProcessor
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
		if(isset($response->data['m_5'])){
			return $response->data['m_5'];
		}
		
		return 0;
	}
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$form = JForm::getInstance('rb_ecommerce.processor.netcash', dirname(__FILE__).'/forms/form.xml');

		$object 					= $request->toObject();		
		$user_data					= $object->user_data;
		$url_data 					= $object->url_data;
		$payment_data				= $object->payment_data;
		$config 					= $this->getConfig(false);
		
		$binddata['m_1']  			= $config->user_name;
		$binddata['m_2']  			= $config->password;
		$binddata['m_3']  			= $config->pin;
		$binddata['p1']  			= $config->terminal_id;
		$binddata['p2']  			= $payment_data->invoice_number;
		$binddata['p3']  			= $payment_data->item_name;
		$binddata['p4']  			= number_format($payment_data->total, 2, '.', '');
		$binddata['p10']  			= !empty($url_data->cancel_url) ? $url_data->cancel_url : $config->cancel_url;
		$binddata['Budget']  		= "N";
		$binddata['m_4']  			= $payment_data->invoice_number;
		$binddata['m_5']  			= $payment_data->invoice_number;
		$binddata['m_10']  			= !empty($url_data->return_url) ? $url_data->return_url : $config->return_url;
		
		$form->bind($binddata);
		
		$response 					= new stdClass();
		$response->data 			= new stdClass();
		$response->data->post_url 	= 'https://gateway.netcash.co.za/vvonline/ccnetcash.asp';
		$response->data->form 		= $form;
		
		return $response;
		
	}
	
	public function process($net_response)
	{
		$data 		= $net_response->data;
		$response 	= new Rb_EcommerceResponse();

		$response->set('txn_id', 			isset($data['RETC']) ? $data['RETC'] : 0)
				 ->set('subscr_id', 		isset($data['RETC']) ? $data['RETC'] : 0)  
				 ->set('parent_txn', 		0)
				 ->set('amount', 	 		0)
				 ->set('payment_status', 	Rb_EcommerceResponse::PAYMENT_FAIL)			  
		 		 ->set('params', 			$data);
		
		if(isset($data['Reason]'])){
			$msg = $data['Reason'];
		}elseif($data['TransactionAccepted'] == false){
			$msg = Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_NETCASH_TRANSACTION_NETCASH_TRANSACTION_NOT_ACCEPTED');
		}
		else{
			$response->set('amount', 		$data['amount'])					 
					 ->set('payment_status', Rb_EcommerceResponse::PAYMENT_COMPLETE);
			$msg = Rb_Text::_('PLG_RB_ECOMMERCEPROCESSOR_NETCASH_TRANSACTION_NETCASH_TRANSACTION_ACCEPTED');
		}
		
		$response->set('message', $msg);		
		return $response;
	}
	
}
