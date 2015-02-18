//license Basic Controller
console.log('License Basic Controller'); 
angular
  .module('simplify')
  .controller('LicenseCtrl', ['$scope','$state','$http', function($scope, $state, $http){
  	console.log('state :' + JSON.stringify($state.current.name));
 
}]);