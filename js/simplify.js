//Angular APPLICATION FILE
var simplify = angular.module('simplify', ['ui.router','ui.bootstrap','ngResource','ui.select','ngSanitize','ngAnimate','angular-loading-bar','angularMoment','gantt']);

if(navigator.appVersion.indexOf("Chrome")==-1 && navigator.appVersion.indexOf("Firefox") ==-1){
  alert("Merci d'utiliser Google Chrome ou Firefox pour un fonctionnement optimal");
}

//Utiliser pour accéder à l'objet $state depuis les templates - permet de connaitre le menu en cours
simplify.run(function ($rootScope, $state, $stateParams) {
    $rootScope.$state = $state;
    $rootScope.$stateParams = $stateParams;
});

simplify.factory('LoginInterceptor', ['$q','$rootScope','$location','errorService',function ($q,$rootScope,$location,errorService) {  
    var LoginInterceptor = {
        responseError: function(response) {
            // Session has expired
            if (response.status == 403 || response.status == 401){
              $rootScope.$state.go('forbiddenAccess',{goto:$location.path()});
            }
            return $q.reject(response);
        }
    };
    return LoginInterceptor;
}]);

simplify.config(['$httpProvider', function($httpProvider) {  
    $httpProvider.interceptors.push('LoginInterceptor');
}]);

simplify.constant('angularMomentConfig', {
    preprocess: 'unix', // optional
    timezone: 'Europe/London' // optional
});