// XITODO : where to put this constant
var rb_app_manager_limit_per_row 		= 3;
var rb_app_manager_invoice_status_paid 	= 402;
var rb_app_manager_invoice_status_inprocess	= 404;

var controllers = {};
	
controllers.AppController = function($scope){	
	$scope.items 			= rbappmanager_items;
	$scope.tag_items 		= rbappmanager_tag_items;
	$scope.default_tag 		= rbappmanager_default_tag;	
	$scope.added_items 		= rbappmanager_added_items;
	$scope.invoices 		= rbappmanager_invoices;
	$scope.config 			= rbappmanager_config;
	
	$scope.fullview_rom_number = -1;
	
	$scope.buynow = function(item_id){
		$scope.items[item_id].status = "active_installed";
		return false;
	};
	
	$scope.templates = { 
			item: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/default_list_item.html',
			myapps: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/myapps.html',
			alert: {
				expired_installed 	: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/alert_expired_installed.html',
				expired_upgradable 	: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/alert_expired_upgradable.html'
			} ,
			cart : '../plugins/system/rbappmanager/rbappmanager/view/tmpl/cart.html'
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
	};	
	
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
	
	$scope.cart = {};
	
	$scope.cart.addToCart = function(item_id){
		rbappmanager.cart.add_item(item_id);
	};

	$scope.cart.add_success = function(items){
		$scope.added_items = items;
		$scope.$apply();	
	
		rb.jQuery('#rbappmanager-cart').modal('show');
	};
	
	$scope.cart.removeFromCart = function(item_id){
		rbappmanager.cart.remove_item(item_id);
	};
	
	$scope.cart.remove_success = function(items){
		$scope.added_items = items;
		$scope.$apply();		
		rb.jQuery('#rbappmanager-cart').modal('show');
	};
	
	$scope.cart.checkout = function(added_items)
	{
		rbappmanager.cart.checkout(added_items);
	};	

	// XITODO : use directives
	$scope.fromJson = function(json){	
			return angular.isString(json) ? rb.jQuery.parseJSON(json): json;
	};
	

	$scope.mysql_to_date = function(dateString){
		// Split timestamp into [ Y, M, D, h, m, s ]
		var time = dateString.split(/[- :]/);
		// Apply each element to the Date function
		return new Date(time[0], time[1]-1, time[2], time[3], time[4], time[5]);			
	};
	
	$scope.add_expiration = function(dateString, expiration){		
		// Split timestamp into [ Y, M, D, h, m, s ]
		var time = dateString.split(/[- :]/);
		
		var exp = Array();		
		for(var count = 0; count <= 5 ; count++){
			time[count] = parseInt(time[count]) + parseInt(expiration.slice(count*2, (count*2)+2));			 
		}		
		
		return new Date(time[0], time[1]-1, time[2], time[3], time[4], time[5]);
	};
	
	$scope.get_item_status = function(status, paid_date, expiration){
		if(expiration == '000000000000'){
			return 'active';
		}
		
		if(status == rb_app_manager_invoice_status_inprocess){
			return 'inprocess';
		}
		
		if(status == rb_app_manager_invoice_status_paid){
			var expiration_date = $scope.add_expiration(paid_date, expiration);
			var now = new Date();
			
			if(expiration_date.getTime() > now.getTime()){
				return 'active';
			}
			
			return 'expired';
		}
		
		return '-';
	};
};

controllers.DetailAppController = function($scope, $state){
	$scope.item = $scope.items[$state.params.item_id];
};
	
rb_appmanager_app.controller(controllers);
