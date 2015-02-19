//Maintenance List Controller
angular
  .module('simplify')
  .controller('MaintenanceListCtrl', ['$scope','$state','$http','maintenanceService', function($scope, $state, $http, maintenanceService) {

  maintenanceService.list().then(function(response){
    $scope.maintenances = response.data;
  });


}]);