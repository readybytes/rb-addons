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

Rb_Html::script(dirname(dirname(dirname(__FILE__))).'/media/js/angular/angular.js');
Rb_Html::script(dirname(dirname(dirname(__FILE__))).'/media/js/angular/config.js');
Rb_Html::script(dirname(dirname(dirname(__FILE__))).'/media/js/angular/controller.js');
Rb_Html::script(dirname(dirname(dirname(__FILE__))).'/media/js/appmanager.js');
Rb_Html::stylesheet(dirname(dirname(dirname(__FILE__))).'/media/css/appmanager.css');
?>
<div data-ng-app="rb_appmanager_app">
	<div data-ng-view="app">
		
	</div>
</div>