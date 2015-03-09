//License CRUD Controller
angular
  .module('simplify')
  .controller('LicenseViewCtrl', ['$scope',
								'$state',
								'$stateParams',
								'$http',
								'$rootScope',
								'$window',
								'licenseService',
								'providerService',
								'brandService', function($scope, $state,$stateParams, $http,$rootScope,$window, licenseService, providerService, brandService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: licenseService }));

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listLicense', {reload: true});
	}
	
/***************************
*
*VIEW LICENSE MODE (state : VIEW)
*
****************************/

	//we get details for selected license
	licenseService.get($stateParams.recordId).then(function(response){
		$scope.license = response.data;
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

	$scope.save = function(){

		$scope.license.PROVIDER_ID = $scope.license.PROVIDER.ID 
		$scope.license.BRAND_ID = $scope.license.BRAND.ID 

		licenseService.update($scope.license).then(function(response){
			$scope.newLicenseId = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newLicenseId;
			str = str.replace('"','');
			$scope.newLicenseId = str.replace('"',''); 
			$rootScope.listAlerts ();
			$state.go('viewLicense',{recordId:$scope.newLicenseId}, { reload: true, inherit: false, notify: true }); 
		});	
	}

	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewLicense',{recordId:$scope.license.LICENSE_ID}, {reload: true}); 
	}

	$scope.deleteLicense = function(){

		$scope.confirmDelete = 1;
		licenseService.delete($scope.license.LICENSE_ID).then(function(response){
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

