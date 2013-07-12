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
	
	protected function _request_build(Rb_EcommerceRequest $request)
	{
		$form = JForm::getInstance('rb_ecommerce.processor.netcash', dirname(__FILE__).'/forms/form.xml');

		$object 					= $request->toObject();		
		$user_data					= $object->user_data;
		$payment_data				= $object->payment_data;
		$config 					= $this->getConfig(false);
		
		$binddata['m_1']  			= $config->user_name;
		$binddata['m_2']  			= $config->password;
		$binddata['m_3']  			= $config->pin;
		$binddata['p1']  			= $config->terminal_id;
		$binddata['p2']  			= $payment_data->invoice_number;
		$binddata['p3']  			= $payment_data->item_name;
		$binddata['p4']  			= $payment_data->total;
		$binddata['p10']  			= Rb_Route::_('index.php?option=com_payinvoice&view=invoice&task=cancel&processor=netcash');
		$binddata['Budget']  		= "N";
		$binddata['m_4']  			= $payment_data->invoice_number;
		$binddata['m_5']  			= $payment_data->invoice_number;
		$binddata['m_10']  			= Rb_Route::_('index.php?option=com_payinvoice&view=invoice&task=complete&processor=netcash');
		
		$form->bind($binddata);
		
		$response 					= new stdClass();
		$response->data 			= new stdClass();
		$response->data->post_url 	= 'https://gateway.netcash.co.za/vvonline/ccnetcash.asp';
		$response->data->form 		= $form;
		
		return $response;
		
	}
	
	public function process($data)
	{
		$response = new Rb_EcommerceResponse();
		
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
