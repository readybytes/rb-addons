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
<style>
	.rbappmanager-container{
		max-width : 1200px;
		margin: auto;
		background: #EEEEEE;
	}
</style>

<script>
	var rbappmanager_data				= <?php echo json_encode($data);?>;
	var rbappmanager_items	 			= <?php echo json_encode($data['items']);?>;
	var rbappmanager_tag_items 			= <?php echo json_encode($data['tag_items']);?>;
	var rbappmanager_default_tag 		= <?php echo $data['tags'][$default_tag]['id'];?>;	
	var rbappmanager_added_items 		= <?php echo json_encode($added_items);?>;	
</script>

<div data-ng-app="rb_appmanager_app" class="rbappmanager-container">
	<div data-ng-view="app">
		<div class="rbappmanager-container">
			
		</div>
	</div>
</div>