//Hardware CRUD Controller
angular
  .module('simplify')
  .controller('HardwareViewCtrl', ['$scope',
								'$state',
								'$stateParams',
								'$http',
								'$rootScope',
								'hardwareService',
								'providerService',
								'brandService', function($scope, $state,$stateParams, $http, $rootScope, hardwareService, providerService, brandService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: hardwareService }));

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listHardware', {reload: true});
	}
	
/***************************
*
*VIEW HARDWARE MODE (state : VIEW)
*
****************************/

	//we get details for selected hardware
	hardwareService.get($stateParams.recordId).then(function(response){
		$scope.hardware = response.data;
	});

	$scope.edit = function(){

		$scope.editButton = 1;

	// we load the provider list uin case we edit the form
	providerService.list(undefined).then(function(response){
		$scope.providers = response.data;
	});

	// we load the provider list uin case we edit the form
	brandService.list(undefined).then(function(response){
		$scope.brands = response.data;
	});

	}
	
	//update current hardware data
	$scope.save = function(){

		$scope.hardware.PROVIDER_ID = $scope.hardware.PROVIDER.ID 
		$scope.hardware.BRAND_ID = $scope.hardware.BRAND.ID 

		hardwareService.update($scope.hardware).then(function(response){
			$scope.newHardwareId = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newHardwareId;
			str = str.replace('"','');
			$scope.newHardwareId = str.replace('"',''); 
			$rootScope.listAlerts ();
			$state.go('viewHardware',{recordId:$scope.newHardwareId}, { reload: true, inherit: false, notify: true }); 
		});

	
	}

	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewHardware',{recordId:$scope.hardware.ID}, {reload: true}); 
	}

	$scope.deleteHardware = function(){

		$scope.confirmDelete = 1;

		hardwareService.delete($scope.hardware.ID).then(function(response){
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

