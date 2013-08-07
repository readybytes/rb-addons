
var controllers = {};
	
controllers.AppController = function($scope){
	$scope.abc = [	
	              	{name : 'Name1'},
	              	{name : 'Name2'},
	              	{name : 'Name3'}		
			];
};
	
rb_appmanager_app.controller(controllers);
