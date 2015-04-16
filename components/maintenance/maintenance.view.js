//Maintenance CRUD Controller
angular
  .module('simplify')
  .controller('MaintenanceViewCtrl', ['$scope',
								'$state',
								'$stateParams',
								'$http',
								'$rootScope',
								'$modal',
								'maintenanceService',
								'providerService',
								'brandService', function($scope, $state,$stateParams, $http, $rootScope, $modal, maintenanceService, providerService, brandService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: maintenanceService }));

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listMaintenance', {reload: true});
	}
	
/***************************
*
*VIEW HARDWARE MODE (state : VIEW)
*
****************************/

	//we get details for selected maintenance
	maintenanceService.get($stateParams.recordId).then(function(response){
		$scope.maintenance = response.data;
	});

	$scope.edit = function(){

		$scope.editButton = 1;
	}
	
	//update current maintenance data
	$scope.save = function(){

		maintenanceService.update($scope.maintenance).then(function(response){
			$scope.newMaintenanceId = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newMaintenanceId;
			str = str.replace('"','');
			$scope.newMaintenanceId = str.replace('"',''); 
			$rootScope.listAlerts ();
			$state.go('viewMaintenance',{recordId:$scope.newMaintenanceId}, { reload: true, inherit: false, notify: true }); 
		});

	
	}

	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewMaintenance',{recordId:$scope.maintenance.ID}, {reload: true}); 
	}

	$scope.deleteMaintenance = function(){

		$scope.confirmDelete = 1;

		maintenanceService.delete($scope.maintenance.ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}

/**********************
* datePicker
**********************/

  $scope.openStartDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedStartDate = true;
  };

  $scope.openEndDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedEndDate = true;
  };

  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };
  
}]);

