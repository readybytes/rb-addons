<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYINVOICE
* @subpackage	Front-end
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Rb App Manager Helper
 * @author Gaurav Jain
 */
jimport('joomla.http.transport');
class RbappmanagerHelper extends Rb_Helper
{	
	public $json = array();
	
	public function set($data)
	{
		// $data should be like array('key' => 'any value');
		$model = Rb_Factory::getInstance('rbappmanager', 'model');
		return $model->save($data);
	}
	
	public function get($what = null)
	{
		static $config = null;
		if($config === null){			
			$model 		= Rb_Factory::getInstance('rbappmanager', 'model');
			$records 	= $model->loadRecords(array(), array(), false, 'key');
			
			$config = new stdClass();
			foreach($records as $record){
				$config->{$record->key} = $record->value;
			}
		}

		if($what == null){
			return $config;
		} 
		
		if(!isset($config->$what)){
			return '';
		}
		
		return $config->$what;
	}
	
	protected function _getServerUrl()
	{
		return JUri::root().'index.php?option=com_paymart&view=api&task=process';
	}
	
	protected function get_file_url($file_id)
	{
		return $this->_getServerUrl().'&resource=file&filter=file&id='.$file_id;
	}
	
	protected function _copyFileFromServer()
	{
		// XITODO : apply some calculation for not to ask on each request
		// XITODO : proper error handling for curl response
		
		$url 		 = $this->_getServerUrl().'';
		$url 		.= '&resource=item&filter=export';
				
		$link 		= new JURI($url);		
		$curl 		= new JHttpTransportCurl(new Rb_Registry());
		$response 	= $curl->request('GET', $link);
		$response	= json_decode($response->body);
		
		if($response->response_code != 200){
			 throw new Exception($response->response_data);
		}
		
		$file_url   = $response->response_data;
		
		$link 		= new JURI($file_url);		
		$response 	= $curl->request('GET', $link);
		if($response->code != 200){
			 throw new Exception(array('File URL is not correct or not accessible.'));		
		}
		
		return json_decode($response->body, true);
	}
	
	public function get_items($component_name, $user = null)
	{
		$this->json = $this->_copyFileFromServer();

		$installed_extenstions = $this->get_extensions();
		
		$component_version = $this->get_component_version($component_name);
		$component_version = explode(".", $component_version);
		$component_version = array_slice($component_version, 0, 3); //VVV IMP : consider only first three part of a version
		$criterias 		   = $this->get_criteria($component_name);
			
		$purchased_item = array();
		if(isset($user['buyer_id'])){
			$purchased_item = $this->get_purchased_item($user['buyer_id']);
		}
			
		foreach ($this->json['items'] as &$item){
			
			// find if this item is alread installed
			$entension_name = $item['type'].'_'.$item['folder'].'_'.$item['element'].'_'.$item['client_id'];
			
			if(isset($installed_extenstions[$entension_name])){
				$manifest_cache = json_decode($installed_extenstions[$entension_name]->manifest_cache, true);
				$item['installed_version'] = $manifest_cache['version'];
			}
			else{
				$item['installed_version'] = 0;
			}
			
			// find if this item is compatible with component name or not			
			if(isset($item['version'])){
				$item['compatible_file_id'] = $this->get_compatible_file($component_version, $item['version'], $criterias);
			}
						
			// set status of item, Like : active or expired or none
			$item['subscription_status'] = 'none';		
			if(isset($purchased_item[$item['item_id']])){
				$item['subscription_status'] = $purchased_item[$item['item_id']]; 
			}
		}
		
		// arrange app list according to tags		
		return $this->json;
	}
	
	public function create_invoice($data)
	{
		$url 		 = $this->_getServerUrl().'';
		$url 		.= '&resource=invoice';
		$link 		= new JURI($url);		
		$curl 		= new JHttpTransportCurl(new Rb_Registry());
		$response 	= $curl->request('POST', $link, $data);		
		$response	= json_decode($response->body, true);
		
		if($response['response_code'] != 201){
			 throw new Exception($response['response_data']);
		}
		
		return $response['response_data'];
	}
	
	public function get_invoices($userid)
	{
		$url 		 = $this->_getServerUrl().'';
		$url 		.= '&resource=invoice&filter=buyer&id='.$userid;
		
		$link 		= new JURI($url);		
		$curl 		= new JHttpTransportCurl(new Rb_Registry());
		$response 	= $curl->request('GET', $link);		
		$response	= json_decode($response->body, true);
		
		if($response['response_code'] != 200){
			if ($response['response_code'] == 204){
				$response['response_data'] = 'No Data to return'; // XITODO
			}
			  throw new Exception($response['response_data']);
		}
		
		return $response['response_data'];
	}
	
	public function get_purchased_item($userid)
	{
		// XITODO : Domain name checking
		$purchased_items = array();		
		
		try{
			$invoices = $this->get_invoices($userid);			
		}
		catch(Exception $e){			
			return $purchased_items;
		}
		
		$now = new Rb_Date();
		$now = $now->toUnix();
		
		foreach($invoices as $invoice_id => $invoice){
			// if invoice is paid
			if($invoice['status'] != 402){
				continue;
			}
			
			$paid_date  = $invoice['paid_date'];
			$paid_date = new Rb_Date($paid_date);				
			
			if(!isset($invoice['invoiceitems'])){
				continue;
			}
			
			$items = $invoice['invoiceitems'];
			
			foreach($items as $item){
				// if already active, then need not to do following
				if(isset($purchased_items[$item['item_id']]) && $purchased_items[$item['item_id']] == 'active'){
					continue;
				}
				
				$params 	= json_decode($item['params'], true);
				$item_data 	= $params['item'];
				$time 		= $item_data['time'];

				$date 	  = date_parse($paid_date->toString());
				$exp_time = $this->_addExpiration($date, $time);

				if($now <= $exp_time){
					$purchased_items[$item_data['item_id']] = 'active';
				}
				else{
					$purchased_items[$item_data['item_id']] = 'expired';
				}
			}
		}
		
		return $purchased_items;
	}
	
	protected function _addExpiration($date, $expirationTime)
  	{   
    	$timerElements = array('year', 'month', 'day', 'hour', 'minute', 'second');
    	   

    	$count = count($timerElements);
    	if($expirationTime != 0){
      		for($i=0; $i<$count ; $i++){
				$date[$timerElements[$i]] +=   intval(JString::substr($expirationTime, $i*2, 2), 10);
      		}
    	}
    	
    	return mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);    	
  	}
  	 
	public function get_accessible_items($userid)
	{
		$url 		 = $this->_getServerUrl().'';
		$url 		.= '&resource=item&filter=buyer&id='.$userid;
		
		$link 		= new JURI($url);		
		$curl 		= new JHttpTransportCurl(new Rb_Registry());
		$response 	= $curl->request('GET', $link);		
		$response	= json_decode($response->body, true);
		
		if($response['response_code'] != 200){
			if ($response['response_code'] == 204){
				$response['response_data'] = 'No Data to return'; // XITODO
			}
			  throw new Exception($response['response_data']);
		}
		
		return $response['response_data'];
	}
	
	public function get_user($email)
	{
		$url 		 = $this->_getServerUrl().'';
		$url 		.= '&resource=buyer&email='.trim($email);
		
		$link 		= new JURI($url);		
		$curl 		= new JHttpTransportCurl(new Rb_Registry());
		$response 	= $curl->request('GET', $link);		
		$response	= json_decode($response->body, true);
		
		if($response['response_code'] != 200){
			  throw new Exception($response['response_data']);
		}
		
		return $response['response_data'];
	}
	
	public function get_pay_url($invoice_id)
	{
		$url 		 = $this->_getServerUrl().'';
		$url 		.= '&resource=invoice&filter=payurl&id='.$invoice_id;
		$link 		= new JURI($url);		
		$curl 		= new JHttpTransportCurl(new Rb_Registry());
		$response 	= $curl->request('GET', $link);		
		$response	= json_decode($response->body, true);
		
		if($response['response_code'] != 200){
			  throw new Exception($response['response_data']);
		}
		
		return $response['response_data'];
	}
	
	public function is_compatible($version1, $version2)
	{
		// equal 	= 0
		// smaller 	= -1
		// greater 	= 1
		$flag = 0;
				
		foreach($version1 as $key => $value){
			if(is_numeric($version2[$key])){
				// check for lower
				if($flag !== 1 && intval($value) < intval($version2[$key])){
					$flag = -1;
					break;
				}
				
				if($flag !== 1 && intval($value) === intval($version2[$key])){
					$flag = 0;
				}
			
				if(intval($value) > intval($version2[$key])){
					$flag = 1;
				}
			}
			else{
				if($version2[$key] === 'STAR'){
					if($flag === 0){
						$flag = 1;
					}
					else{
						$flag = -1;
					}
				}
				elseif($version2[$key] === 'PLUS'){
					$flag = 1;
				}
					
			}
		}
		
		return $flag;
	} 
	
	public function get_compatible_file($component_version, $files, $criterias)
	{
		// arrange file according to version
		$arranged_files = array();
		foreach($files as $file){
			$arranged_files[$file['itemversion_id']] = $file['version_number'];
		}
		
		$found = false; 
		foreach($arranged_files as $file_id => $file_version){
			foreach($criterias as $column => $version){
				if(!isset($files[$file_id][$column]) || empty($files[$file_id][$column])){
					continue;					
				}
				
				if($this->is_compatible($component_version, $version) >= 0){
					$found = true;
					break; 
				}
			}
		}
		
		if($found){
			return $files[$file_id];
		}
		
		return false;
	}
	
	public function get_criteria($component)
	{
		if(empty($this->json)){
			$this->json = $this->getItems();
		}
		
		$component_compatibility = $this->json['compatibility'][$component];

		$columns 	= json_decode($component_compatibility['value'], true);
		$criterias 	= array();
		foreach ($columns as $key => $value){
			$criteria = str_replace($component, '', $key);
			$criteria = explode("_", $criteria);
			$criterias[$key] = $criteria;
		}
		
		return $criterias;
	} 
	
	public function get_component_version($component)
	{
		$query = new Rb_Query();
		$query->select('*')
				->from('#__extensions')
				->where(' `type` = "component" ', 'AND')
				->where(' `element` = "'.$component.'" ');
				
		$record = $query->dbLoadQuery()
						->loadObject();
						
		$manifest_cache = json_decode($record->manifest_cache);
		return $manifest_cache->version;		
	}
	
	public function get_extensions()
	{
		$sql = "SELECT concat( `type` , '_', `folder` , '_', `element` , '_', `client_id` ) as `extension`, `manifest_cache`
				FROM `#__extensions`";
		
		$db = Rb_Factory::getDbo();
		$db->setQuery($sql);
		return $db->loadObjectList('extension');
		
	}
}
