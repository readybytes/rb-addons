// XITODO : where to put this constant
var rb_app_manager_limit_per_row = 3;
var controllers = {};
	
controllers.AppController = function($scope){	
	$scope.items 			= rbappmanager_items;
	$scope.tag_items 		= rbappmanager_tag_items;
	$scope.default_tag 		= rbappmanager_default_tag;	
	$scope.added_items 		= rbappmanager_added_items;
	
	$scope.fullview_rom_number = -1;
	
	$scope.templates = { 
			item: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/default_list_item.html',			 		
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
	
	$scope.getFooterTemplatePath = function(item){
		var base_path = '../plugins/system/rbappmanager/rbappmanager/view/tmpl/';		
		
		// Condition : Not Compatible
		if(typeof(item.compatible_file_id) == 'undefined' || !item.compatible_file_id){
			return base_path + 'default_list_item_notcompatible.html';
		}
		else if(item.subscription_status == 'none' && !item.installed_version){
			// Condition : Not Installed, Not Purchased, Not AddedToCart
			if(typeof($scope.added_items[item.item_id]) == 'undefined'){
				return base_path + 'default_list_item_buynow.html';
			}
			
			// Condition : Not Installed, Not Purchased, AddedToCart
			return base_path + 'default_list_item_addedtocart.html';
		}	
		// Condition : Installed and Purchased and Upgradable
		// Condition : Installed and Purchased and Not Upgradable
		else if(item.installed_version && item.subscription_status == 'active'){
			// XITODO:
			var upgradable = false;
			if(upgradable){
				return base_path + 'default_list_item_upgradable.html';
			}
			else{
				return base_path + 'default_list_item_installed.html';
			}
		}
		// Condition : Not Installed and Purchased
		else if(!item.installed_version && item.subscription_status == 'active'){
			return base_path + 'default_list_item_purchased.html';
		}
		else if(item.installed_version && item.subscription_status == 'expired'){
			// XITODO:
			var upgradable = false;
			if(upgradable){
				return base_path + 'default_list_item_upgrade_expired.html';
			}
			else{
				return base_path + 'default_list_item_installed_expired.html';
			}
		}
	};
};

controllers.DetailAppController = function($scope, $state){
	$scope.item = $scope.items[$state.params.item_id];
};
	
rb_appmanager_app.controller(controllers);
