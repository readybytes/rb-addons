// XITODO : where to put this constant
var rb_app_manager_limit_per_row = 3;
var controllers = {};
	
controllers.AppController = function($scope){	
	$scope.items 			= rbappmanager_items;
	$scope.tag_items 		= rbappmanager_tag_items;
	$scope.default_tag 		= rbappmanager_default_tag;	
	$scope.added_items 		= rbappmanager_added_items;
	
	$scope.fullview_rom_number = -1;
	
	$scope.buynow = function(item_id){
		$scope.items[item_id].status = "active_installed";
		return false;
	}
	
	$scope.templates = { 
			item: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/default_list_item.html',
			alert: {
				expired_installed 	: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/alert_expired_installed.html',
				expired_upgradable 	: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/alert_expired_upgradable.html'
			} 
	 };
		
	//function to find proper place to render this view
	$scope.find_last_item_block = function(item_id){
		
		var item_blocks = rb.jQuery('.rbappmanager-item:visible');
		
		var index_of_item = -1;
		for(var index = 0; index < item_blocks.length ; index++){			
			
			if(item_blocks[index].id == ('rbappmanager-item-'+item_id)){
				index_of_item = index;
				break;
			}
		}
		
		var remaining_items_to_check = rb_app_manager_limit_per_row - (index_of_item % rb_app_manager_limit_per_row) - 1;
		
		var new_row_number  = index_of_item / rb_app_manager_limit_per_row;
		var prev_row_number = $scope.fullview_rom_number;		
		$scope.fullview_rom_number = new_row_number;
		
		if(new_row_number != prev_row_number && rb.jQuery('#rbappmanager-fulldetail-view').is(':visible')){
			//rb.jQuery('#rbappmanager-fulldetail-view').slideUp(300);
		}
				
		var last_index = index_of_item; 
		
		for(; remaining_items_to_check > 0 ; remaining_items_to_check--){			
			if(typeof(item_blocks[last_index + 1]) == 'undefined'){
				break;
			}
			
			last_index++;
		}		
		
		rb.jQuery('#rbappmanager-fulldetail-view').insertAfter('#' + item_blocks[last_index].id);
		return true;
	}	
	
	$scope.getTemplatePath = function(item_id){
		var base_path = '../plugins/system/rbappmanager/rbappmanager/view/tmpl/';		
		
		switch($scope.items[item_id].status){
			case 'not_available': 
			case 'not_compatible':	
			
			case 'none_addedtocart' :
			case 'none_buynow' :
			case 'none_installed' :   // Rare Condition
			case 'none_upgradable' :  // Rare Condition
			
			case 'active_install' :
			case 'active_installed' :
			case 'active_upgradable' :
			
			case 'expired_install' :
			case 'expired_installed' :
			case 'expired_upgradable' :
					return base_path + 'default_list_item_' + $scope.items[item_id].status +'.html';
			default : return '';//base_path + 'default_list_item_buynow.html';		
		}		
	};
	
	$scope.getTestTemplatePath = function(item, status){
		var base_path = '../plugins/system/rbappmanager/rbappmanager/view/tmpl/';		
		
		switch(status){
			case 'not_available': 
			case 'not_compatible':	
			
			case 'none_addedtocart' :
			case 'none_buynow' :
			case 'none_installed' :   // Rare Condition
			case 'none_upgradable' :  // Rare Condition
			
			case 'active_install' :
			case 'active_installed' :
			case 'active_upgradable' :
			
			case 'expired_install' :
			case 'expired_installed' :
			case 'expired_upgradable' :
					return base_path + 'default_list_item_' + status +'.html';
			default : return '';//base_path + 'default_list_item_buynow.html';		
		}		
	};
};

controllers.DetailAppController = function($scope, $state){
	$scope.item = $scope.items[$state.params.item_id];
};
	
rb_appmanager_app.controller(controllers);
