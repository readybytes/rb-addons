
var controllers = {};
	
controllers.AppController = function($scope){	
	$scope.items 			= rbappmanager_items;
	$scope.tag_items 		= rbappmanager_tag_items;
	$scope.default_tag 		= rbappmanager_default_tag;	
	$scope.added_items 		= rbappmanager_added_items;
	
	$scope.templates = { 
			 				item: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/default_list_item.html',
			 		
		 					footer : {
		 							buynow : '../plugins/system/rbappmanager/rbappmanager/view/tmpl/default_list_item_footer_buynow.html',
		 							installed : ''
	 							}
	 };
	
	$scope.getFooterTemplatePath = function(item_id){
		var base_path = '../plugins/system/rbappmanager/rbappmanager/view/tmpl/';		
		
		// Condition : Not Compatible
		if(typeof($scope.items[item_id].compatible_file_id) == 'undefined' || !$scope.items[item_id].compatible_file_id){
			return base_path + 'default_list_item_notcompatible.html';
		}
		else if($scope.items[item_id].subscription_status == 'none' && !$scope.items[item_id].installed_version){
			// Condition : Not Installed, Not Purchased, Not AddedToCart
			if(typeof($scope.added_items[item_id]) == 'undefined'){
				return base_path + 'default_list_item_buynow.html';
			}
			
			// Condition : Not Installed, Not Purchased, AddedToCart
			return base_path + 'default_list_item_addedtocart.html';
		}	
		// Condition : Installed and Purchased and Upgradable
		// Condition : Installed and Purchased and Not Upgradable
		else if($scope.items[item_id].installed_version && $scope.items[item_id].subscription_status == 'active'){
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
		else if(!$scope.items[item_id].installed_version && $scope.items[item_id].subscription_status == 'active'){
			return base_path + 'default_list_item_purchased.html';
		}
		else if($scope.items[item_id].installed_version && $scope.items[item_id].subscription_status == 'expired'){
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

controllers.DetailAppController = function($scope){
	$scope.items = [	
	              	{ 
	              		title : '2checkout', description : '2Checkout is your trusted international online payment solution and authorized reseller for thousands of tangible and digital products and services. 2CO support buyers and sellers from all around the world and are able to accept a wide variety of payment cards including Visa, MasterCard, American Express, Discover/Novus, Diners Club and JCB, as well as all derivatives of these brands. 2Checkout also accepts Electronic Check Draft, also known as ACH or Digital Check, and BillMeLater.',
	              		version : '2.4.2',	price : '$10',	time  : '1 month',	tag :	'Payment Gateway',
	              		hit :	802, last_date :'12 Jan 2013'
	              	}
			];
	
};
	
rb_appmanager_app.controller(controllers);
