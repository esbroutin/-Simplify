//Maintenance List Controller
angular
  .module('simplify')
  .controller('MaintenanceListCtrl', ['$scope','$state','$http','maintenanceService', function($scope, $state, $http, maintenanceService) {
  	console.log('state :' + JSON.stringify($state.current.name));

  //on load, we get the maintenance list

  maintenanceService.list().then(function(response){
    $scope.maintenances = response.data;
  });


}]);