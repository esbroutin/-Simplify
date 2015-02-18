//Maintenance CRUD Controller
angular
  .module('simplify')
  .controller('MaintenanceCrudCtrl', ['$scope','$state','$http','maintenanceService', function($scope, $state, $http, maintenanceService){
  	console.log('state :' + JSON.stringify($state.current.name));

$scope.testClick = function(){

}


}]);