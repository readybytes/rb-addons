<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		App Manager
* @contact		team@readybytes.in
*/

defined('_JEXEC') or die('Restricted access');

if(!defined('RB_FRAMEWORK_LOADED')){
	JLog::add('RB Frameowork not loaded', JLog::ERROR);
	return false;
}
			
class plgSystemRbappmanager extends JPlugin
{
	protected $autoloadLanguage = true;
	
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		
		// set some required variables in instance of plugin ($this)
		$this->app 		= JFactory::getApplication();
		$this->input 	= $this->app->input;
	}	

	
	public function onRbControllerCreation($option, $view, $controller, $task, $format)
	{
		if($controller === 'rbappmanager'){			
			require_once dirname(__FILE__).'/'.$this->_name.'/view/view.'.$format.'.php';
			require_once dirname(__FILE__).'/'.$this->_name.'/controller.php';
			require_once dirname(__FILE__).'/'.$this->_name.'/model.php';
			require_once dirname(__FILE__).'/'.$this->_name.'/table.php';
			require_once dirname(__FILE__).'/'.$this->_name.'/helper.php';
		}
	}
}
