
var controllers = {};
	
controllers.AppController = function($scope){	
	$scope.items 		= rbappmanager_items;
	$scope.tag_items 	= rbappmanager_tag_items;
	$scope.default_tag 	= rbappmanager_default_tag;
	
	$scope.templates = { 
			 				item: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/default_list_item.html',
			 		
		 					footer : {
		 							buynow : '',
		 							installed : ''
	 							}
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
