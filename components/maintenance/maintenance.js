//maintenance Basic Controller
console.log('Maintenance Basic Controller'); 
angular
  .module('simplify')
  .controller('MaintenanceCtrl', ['$scope','$state','$http', function($scope, $state, $http){
  	console.log('state :' + JSON.stringify($state.current.name));

    $scope.hello = 'Aloha !';
 
}]);