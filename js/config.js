//Configuration - définition de la gestion des erreurs cot� serveur
simplify.config(['$httpProvider', function($httpProvider) {
  $httpProvider.interceptors.push('LoginInterceptor');
}]);