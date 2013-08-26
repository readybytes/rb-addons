rb_appmanager_app.filter('item_filter', function() {	
	  return function(items, searchText) {	   
	    if (searchText == undefined) {
	        return items;
	    }
	    
		var result = [];
	    for(var index in items) {	    	 
	    	if(typeof(searchText.description) != 'undefined'){
	    		var searchRegx = new RegExp(searchText.description, "i");
	    		 
	    		if(items[index].title.search(searchRegx) != -1){
	    			result.push(items[index]);
	    			continue;
	    		}
	    		
	    		if(items[index].description.search(searchRegx) != -1){
	    			result.push(items[index]);
	    			continue;
	    		}
	    	}
	    }

	    return result;
	  }
	});
