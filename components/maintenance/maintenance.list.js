//Maintenance List Controller
angular
  .module('simplify')
  .controller('MaintenanceListCtrl', ['$scope','$state','$http','maintenanceService', function($scope, $state,$http, maintenanceService ) {
  //on load, we get maintenances list

	//list
	$scope.listMaintenance = function(maintenance){
	  maintenanceService.list().then(function(response){
	  	$scope.maintenances = response.data; 
	    $scope.qView.resolve(); //Notice the availability of data
	    // console.log('list maintenances : ' + JSON.stringify($scope.maintenances));
	  });
	}
	//go to detailed maintenance view
	$scope.viewMaintenance = function(maintenance){

		// console.log('maintenance : ' + JSON.stringify(maintenance.ID));
	  $state.go('viewMaintenance',{recordId:maintenance.ID}); 

	}

	//search function
	$scope.searchMaintenance = function () {
			if ($scope.maintenanceSearch == '' || $scope.maintenanceSearch == 'undefined') {
				$scope.listMaintenance();
			}else{
				maintenanceService.list($scope.maintenanceSearch).then(function(response){
					$scope.maintenances = response.data;
					// console.log('maintenances : ' + JSON.stringify($scope.maintenances));
				});	
			}
	};	
	
	$scope.listMaintenance();

}]);