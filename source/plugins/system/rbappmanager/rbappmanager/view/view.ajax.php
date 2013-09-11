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
 * App Manager Html View
 * @author Gaurav Jain
 */
require_once dirname(__FILE__).'/view.php';
class PayInvoiceAdminViewRbappmanager extends PayInvoiceAdminBaseViewRbappmanager
{
	public function credential()
	{
		$ajax = Rb_Factory::getAjaxResponse();
		if($this->get('action') == 'check'){	
			if($this->get('credential_verified') == false){	
				$ajax->addScriptCall('rb.ui.dialog.create();');	
				$this->_setAjaxWinTitle(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_SET_CREDENTIAL_WINDOW_TITLE'));
				$this->_setAjaxWinBody($this->loadTemplate('set_credential'));
				$this->_addAjaxWinAction(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_VERIFY'), 'rbappmanager.credential.verify();', 'btn btn-info', 'id="rbappmanager-credential-verify" data-loading-text="Loading..."');
				$this->_addAjaxWinAction(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_CLOSE'), 'rb.ui.dialog.close();', 'btn');
				$this->_setAjaxWinAction();	
			}
		
			$ajax->sendResponse();
		}
		
		if($this->get('action') == 'verify'){
			$this->_setAjaxWinTitle(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_SET_CREDENTIAL_WINDOW_TITLE'));
			
			if($this->get('credential_verified') == true){
				// as credentials are verified
				// store them in database
				$credential = $this->get('credential');
				$this->_helper->set(array('email' 		=> $credential['email']));
				$this->_helper->set(array('password' 	=> $credential['password']));	
				
				$this->_setAjaxWinBody(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_SET_CREDENTIAL_WINDOW_BODY_VERIFICATION_SUCCESS'));	
				$ajax->addScriptCall('setTimeout(function(){rb.ui.dialog.close();}, 3000);');	
			}
			else{	
				$this->assign('verification_error', Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_CREDENTIAL_VERIFICATION_FAILED'));
				$this->_setAjaxWinBody($this->loadTemplate('set_credential'));
				$this->_addAjaxWinAction(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_VERIFY'), 'rbappmanager.credential.verify();', 'btn btn-info', 'id="rbappmanager-credential-verify" data-loading-text="Loading..."');
			}
			
			$this->_addAjaxWinAction(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_CLOSE'), 'rb.ui.dialog.close();', 'btn');
			$this->_setAjaxWinAction();
			$ajax->sendResponse();	
		}	
	}
	
	public function registration()
	{
		$ajax = Rb_Factory::getAjaxResponse();
		$action = $this->get('action');
		if($action == 'form'){
			$this->_setAjaxWinTitle(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_REGISTRATION_FORM_WINDOW_TITLE'));
			$this->_setAjaxWinBody($this->loadTemplate('registration_form'));
			$this->_addAjaxWinAction(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_REGISTER'), 'rbappmanager.registration.register();', 'btn btn-info', 'id="rbappmanager-registration-register" data-loading-text="Loading..."');
			$this->_addAjaxWinAction(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_CLOSE'), 'rb.ui.dialog.close();', 'btn');
			$this->_setAjaxWinAction();
		}
		
		if($action == 'register'){
			if($this->get('registered') == true){				
				$this->_setAjaxWinBody(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_REGISTRATION_WINDOW_BODY_REGISTRATION_SUCCESS'));	
				$ajax->addScriptCall('setTimeout(function(){rb.ui.dialog.close();}, 3000);');	
			}
			else{	
				$this->assign('registration_error', Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_REGISTRATION_WINDOW_BODY_REGISTRATION_FAILED'));
				$this->_setAjaxWinBody($this->loadTemplate('registration_form'));
				$this->_addAjaxWinAction(Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_REGISTER'), 'rbappmanager.registration.register();', 'btn btn-info', 'id="rbappmanager-registration-register" data-loading-text="Loading..."');
			}
		}
		$ajax->sendResponse();
		
	}	
}