var rb_appmanager_app = angular.module('rb_appmanager_app', ['ui.state']);

var $AnchorScrollProvider = function() {
  this.$get = ['$window', '$location', '$rootScope', function($window, $location, $rootScope) {
    function scroll() {
    }
    return scroll;
  }];
}

rb_appmanager_app.provider('$anchorScroll', $AnchorScrollProvider);

rb_appmanager_app.config(function($stateProvider, $urlRouterProvider){
    
    // For any unmatched url, send to /route1
    $urlRouterProvider.otherwise("/app")
    
    $stateProvider
      .state('app', {
          url: "/app",
          controller: 	'AppController',
          templateUrl: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/default_list.html'
      })
        .state('app.list', {
            url: "/{item_id}",
            templateUrl: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/fullview.html',
            controller: 	'DetailAppController'
        })        
      .state('test', {
          url: "/test",
          controller: 	'AppController',
          templateUrl: '../plugins/system/rbappmanager/rbappmanager/view/tmpl/test_list.html'
      })
  });