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
 * App Manager Table
 * @author Gaurav Jain
 */
class Rb_TableRbappmanager extends Rb_Table
{
	public function __construct($table = '#__rb_appmanager', $key = 'key', $db = null)
	{
		parent::__construct($table, $key, $db);
	}
}