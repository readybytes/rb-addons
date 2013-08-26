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
Rb_Html::script(dirname(dirname(dirname(__FILE__))).'/media/js/angular/ui-router.js');
Rb_Html::script(dirname(dirname(dirname(__FILE__))).'/media/js/angular/config.js');
Rb_Html::script(dirname(dirname(dirname(__FILE__))).'/media/js/angular/controller.js');
Rb_Html::script(dirname(dirname(dirname(__FILE__))).'/media/js/angular/filters.js');
Rb_Html::script(dirname(dirname(dirname(__FILE__))).'/media/js/appmanager.js');
Rb_Html::stylesheet(dirname(dirname(dirname(__FILE__))).'/media/css/appmanager.css');
?>
<style>
	.rbappmanager-app-container{
		max-width : 1200px;
		margin: auto;
		background: #EEEEEE;
	}
	
	.rbappmanager-app-title{		
		margin: auto;
		background: #333333;
		padding: 30px 0px;
		color: #FFFFFF;
	}
	
	.rbappmanager-app-header{		
		border-bottom:1px solid #CCCCCC;		
	}
	
	.rbappmanager-app-header-content{
		max-width : 1200px;		
		margin: auto;	
	}
	
	.rbappmanager-border-right{				
		border-right: 1px solid #CCCCCC;
	}
	
	.rbappmanager-padding20{
		padding: 20px 0px;
	}
</style>

<script>
	var rbappmanager_data				= <?php echo json_encode($data);?>;
	var rbappmanager_items	 			= <?php echo json_encode($data['items']);?>;
	var rbappmanager_tag_items 			= <?php echo json_encode($data['tag_items']);?>;
	var rbappmanager_default_tag 		= <?php echo $data['tags'][$default_tag]['id'];?>;	
	var rbappmanager_added_items 		= <?php echo json_encode($added_items);?>;	
</script>

<div data-ng-app="rb_appmanager_app">
	<div class="rbappmanager-app-title text-center">
		<h1>Pay Invoice App Store</h1>
	</div>
	<div class="progress" style="height:5px;  margin-bottom: 0px;">
    	<div class="bar bar-success" style="width: 100%;"></div>
    </div>
    
	<div class="rbappmanager-app-header text-center">
		<div class="rbappmanager-app-header-content text-center">
			<div class="row-fluid">
				<div class="span2 rbappmanager-border-right rbappmanager-padding20">
					Show Filters<i class="icon-chevron-down"></i>			 
				</div>
				
				<div class="span6 rbappmanager-border-right rbappmanager-padding20">
					<div class="row-fluid">
						<input type="text" class="span10 search-query" data-ng-model="model_filter.description">				
					</div>
				</div>
				
				<div class="span2 rbappmanager-border-right rbappmanager-padding20">
					<i class="icon-shopping-cart"></i> Cart
				</div>
				
				<div class="span2 rbappmanager-padding20">
					MyApps
				</div>
			</div>
		</div>
	</div>
	
	<div ui-view>	
	</div>
	
</div>