//Maintenance CRUD Controller
angular
  .module('simplify')
  .controller('LicenseOverviewCtrl', ['$scope','$state','$http','licenseService', function($scope, $state, $http, licenseService){

//we call the overview function to build the gantt

		licenseService.getGantt().then(function(response){
			$scope.gantt = response.data;
			$scope.gantt = JSON.parse(angular.fromJson($scope.gantt));
		});

}]);