if (typeof(rbappmanager)=='undefined'){
	var rbappmanager = {}
}

(function($){
// START : 	
// Scoping code for easy and non-conflicting access to $.
// Should be first line, write code below this line.	
rbappmanager.credential = {
	verify : function(){
				var url = "index.php?option=com_payinvoice&view=appmanager&task=credential&action=verify";
			
				var email 	 = $('#rbappmanager-credential-email').val();
				var password = $('#rbappmanager-credential-password').val();
				var arg = {'event_args' :{'email' : email, 'password' : password} };
				
				//$('#rbappmanager-credential-verify').text($('#rbappmanager-credential-verify').attr("data-loading-text"));
				//$('#rbappmanager-credential-verify').attr("disabled", "disabled");
				rb.ajax.go(url, arg);
	}
};

rbappmanager.item = {};

rbappmanager.cart = {
		add_request	:	function(item_id){							
							var args = {'event_args' : {'item_id' : item_id}};
							rb.ajax.go('index.php?option=com_payinvoice&view=appmanager&task=addtocart', args);
		},
		
		remove_request	:	function(item_id){							
							var args = {'event_args' : {'item_id' : item_id}};
							rb.ajax.go('index.php?option=com_payinvoice&view=appmanager&task=removefromcart', args);
		},
		
		add_item 	:	function(item_id){
							item = rbappmanager_items[item_id];		
							// disable the currently clicked button					
							$('#rbappmanager-item-addtocart-' + item.item_id).attr("disabled", "disabled");
							
							// add a hidden type field in "rbappmanager-cart-container" 
							var html = '<input type="hidden" id="rbappmanager-cart-item-'+ item.item_id +'" name="rbappmanager-cart-items[]" value="'+item.item_id+'" />';
							$(html).appendTo('#rbappmanager-cart-container');
							
							html =  '<li id="rbappmanager-cart-item-added-'+ item.item_id +'">';							
							html += '<a href="#" onClick="rb.ajax.go(\'index.php?option=com_payinvoice&view=appmanager&task=view&item_id='+item.item_id+'\'); return false;">';
							html += item.title;
							html += '<span class="rbappmanager-cart-item-price">';
							html += parseFloat(item.price).toFixed(2);
							html += '</span>';
							html += '</a>';
							html += '<span class="pull-right">';
							html += '<button type="button" class="close" onClick="rbappmanager.cart.remove_request('+item.item_id+'); return false;">×</button>';
							html += '</a>';
							html += '</span>';
							html += '<hr/>';
							html += '</li>';
		        		
							$(html).appendTo('#rbappmanager-cart-added-items');
							
							rbappmanager.cart.update_total();
		},
	
		remove_item : 	function(item_id){
							// enable the add to cart button of item
							$('#rbappmanager-item-addtocart-' + item_id).removeAttr("disabled");
							// remove hidden field
							$('#rbappmanager-cart-item-'+ item_id).remove();
							// remove li also
							$('#rbappmanager-cart-item-added-' + item_id).remove();
							
							rbappmanager.cart.update_total();
		},
		
		update_total:	function(){
							var total = 0.00;
							$('.rbappmanager-cart-item-price').each(function(){
								total += parseFloat($(this).text()); 
							});						
							
							$('#rbappmanager-cart-total').text(total.toFixed(2));							
		},
		
		checkout	:	function(){
							var item_ids = new Array();
							$('input[name^="rbappmanager-cart-items"]').each(function(){								
								item_ids.push($(this).val());
							});
							
							var args = {'event_args' : {'items' : item_ids}};
							rb.ajax.go('index.php?option=com_payinvoice&view=appmanager&task=checkout', args);	
		}
};

$(document).ready(function(){
	$('.rbappmanager-item-addtocart').click(function(){
			
	});
});

//ENDING :
//Scoping code for easy and non-conflicting access to $.
//Should be last line, write code above this line.
})(rb.jQuery);

