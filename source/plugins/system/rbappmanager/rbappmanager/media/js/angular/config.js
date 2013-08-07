var rb_appmanager_app = angular.module('rb_appmanager_app', []);

rb_appmanager_app.config(function($routeProvider){
	$routeProvider
		.when('/app',
				{
					controller: 	'AppController',
					templateUrl:	'../plugins/system/rbappmanager/rbappmanager/view/tmpl/default_list.html'
				}
		)
		.otherwise({redirectTo: '/app'});
});