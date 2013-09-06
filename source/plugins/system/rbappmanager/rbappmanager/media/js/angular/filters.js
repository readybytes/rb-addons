rb_appmanager_app.filter('item_filter', function() {	
	  return function(items, searchText) {	   
	    if (searchText == undefined) {
	        return items;
	    }
	    
		var result = {};
	    for(var index in items) {	    	 
	    	if(typeof(searchText.description) != 'undefined'){
	    		var searchRegx = new RegExp(searchText.description, "i");
	    		 
	    		if(items[index].title.search(searchRegx) != -1){
	    			result[index] = items[index];
	    			continue;
	    		}
	    		
	    		if(items[index].description.search(searchRegx) != -1){
	    			result[index] = items[index];
	    			continue;
	    		}
	    	}
	    }

	    return result;
	  }
	});


rb_appmanager_app.filter('myapp_invoice_filter', function() {	
	  return function(invoices) {	    
		var result = {};
	    for(var index in invoices) {
	    	if(invoices[index].status != rb_app_manager_invoice_status_paid && invoices[index].status != rb_app_manager_invoice_status_inprocess){
	    		continue;
	    	}
	    	
	    	result[index] = invoices[index];
	    }

	    return result;
	  }
	});


rb_appmanager_app.filter('fullview_tag_filter', function() {	
	  return function(tags, item_id, tag_items) {	    
		var result = {};
	    for(var index in tags) {
	    	if(typeof(tag_items[tags[index].id]) != 'undefined' && tag_items[tags[index].id].indexOf(item_id) > -1){
	    		result[index] = tags[index];
	    	}    	
	    }

	    return result;
	  }
	});