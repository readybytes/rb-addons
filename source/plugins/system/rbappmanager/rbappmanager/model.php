<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYINVOICE
* @subpackage	Back-end
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * App Manager Model
 * @author Gaurav Jain
 */
class Rb_ModelRbappmanager extends Rb_Model
{
	public function getTable($tableName=null)
	{
		// support for parameter
		if($tableName===null)
			$tableName = $this->getName();

		$table	= Rb_Factory::getInstance($tableName,'Table', 'RB_');
		if(!$table)
			$this->setError(Rb_Text::_('NOT_ABLE_TO_GET_INSTANCE_OF_TABLE'.':'.$this->getName()));

		return $table;
	}
	
	function save($data = array())
	{		
		$keys 	= array_keys($data);
		$db 	= PayInvoiceFactory::getDbo();
		$delete = " DELETE FROM `#__rb_appmanager` WHERE `key` IN ('".implode("', '", $keys)."')" ;
		
		$db->setQuery($delete)
		   ->query();
		
		
		$query  =  "INSERT INTO `#__rb_appmanager` (`key`, `value`) VALUES ";
		$queryValue = array();
		
		foreach ($data as $key => $value){
			if(is_array($value)){
				$value  = json_encode($value);
			}

			$queryValue[] = "(".$db->quote($key).",". $db->quote($value).")";
		}

		$query .= implode(",", $queryValue);
		
		return $db->setQuery($query)
		   		  ->query();
		
	}
}

class Rb_ModelformRbappmanager extends Rb_Modelform
{
	
}