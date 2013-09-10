if (typeof(rbappmanager)=='undefined'){
	var rbappmanager = {}
}

(function($){
// START : 	
// Scoping code for easy and non-conflicting access to $.
// Should be first line, write code below this line.	

rbappmanager.cart = {
		add_item	:	function(item_id){	
							var args = {'event_args' : {'item_id' : item_id}};
							rb.ajax.go('index.php?option=com_payinvoice&view=rbappmanager&task=addToCart', args);
		},
		
		add_success : function(items){
						var scope = angular.element(document.getElementById('rbappmanager-list-ctrl')).scope();
						return scope.cart.add_success(items);
		},
		
		remove_item	:	function(item_id){			
							var args = {'event_args' : {'item_id' : item_id}};
							rb.ajax.go('index.php?option=com_payinvoice&view=rbappmanager&task=removeFromCart', args);
		},
		
		remove_success : function(items){			
							var scope = angular.element(document.getElementById('rbappmanager-list-ctrl')).scope();
							return scope.cart.remove_success(items);
		},
		
		checkout	: 	function(added_items){
							var args = {'event_args' : {'items' : added_items}};
							rb.ajax.go('index.php?option=com_payinvoice&view=rbappmanager&task=checkout', args);	
		},

	    redirect_to_pay : function(url){
						var return_url = window.location.href;
						window.location.href = url+"&return_url="+encodeURIComponent(return_url);
		}
};

rbappmanager.item = {
		install		: 	function(item_id, version_id){
						var	args = {'event_args': {'item_id': item_id, 'version_id' : version_id}};
						rb.ajax.go('index.php?option=com_payinvoice&view=rbappmanager&task=install', args);
		},
		
		install_response : function(response){
						response = JSON.parse(response);
						
						var scope = angular.element(document.getElementById('rbappmanager-list-ctrl')).scope();
						
						if(response.response_code == 200){
							return scope.install.success();
						}
						else {
							return scope.install.error(response);
						}				
		}
};

$(document).ready(function(){

});

//ENDING :
//Scoping code for easy and non-conflicting access to $.
//Should be last line, write code above this line.
})(rb.jQuery);

