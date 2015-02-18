//License CRUD Controller
angular
  .module('simplify')
  .controller('LicenseCrudCtrl', ['$scope','$state','$stateParams', '$http', 'licenseService', function($scope, $state,$stateParams, $http, licenseService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: licenseService }));

//we initialize our values
	$scope.formDone = 0;
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listLicense', {reload: true});
	}

	//redirection to view created license
	$scope.viewLicense = function(){
		$state.transitionTo('viewLicense',{recordId:$scope.newLicenseId});
	}

/***************************
*
*VIEW LICENSE MODE (state : VIEW)
*
****************************/

if ($state.current.name == 'viewLicense') {

	//we get details for selected license
	licenseService.get($stateParams.recordId).then(function(response){
		$scope.license = response.data;
	});

	$scope.confirmDeleteLicense = function(){

		$scope.confirmDelete = 1;
		
		licenseService.delete($scope.license.ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}

	$scope.deleteLicense = function(){

		$scope.confirmDelete = 1;

		licenseService.delete($scope.license.LICENSE_ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}
}

/***************************
*
*ADD LICENSE MODE (state : ADD)
*
****************************/

else{
	$scope.license = {};

	// we load the provider list
	licenseService.listProvider().then(function(response){
		$scope.providers = response.data;
			});

	$scope.addProvider = function () {

		//we add the new provider then reload the providers list
		licenseService.addProvider($scope.newProviderForm).then(function(response){
			//reloading & hide the provider form
			licenseService.listProvider().then(function(response){
				$scope.providers = response.data;
				$scope.newProvider = 0;

			});
		});
	};
	
/****************************
*
*WATCH FORM VALIDITY
*
****************************/

	$scope.$watchCollection('[license.DATE_START, license.DATE_END, license.DESCRIPTION, license.PROVIDER, license.LABEL, license.SERIAL]', function(newValues) {
		var countDefined='';

		for (var i = newValues.length - 1; i >= 0; i--) {

			if(newValues[i] !=0 && newValues[i] !=undefined) {
				countDefined++;
			}
		};

		$scope.completeForm = ((countDefined) *100)/(newValues.length);
		$scope.completeForm = Math.round($scope.completeForm);
		$scope.remainingProgress = 100 - $scope.completeForm;
	});

// datePicker
	$scope.today = function() {
    $scope.license.DATE_START = new Date();
  };

  $scope.clear = function () {
    $scope.license.DATE_START = null;
  };

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

  $scope.toggleMin = function() {
    $scope.minDateEnd = $scope.license.DATE_START;
  };

  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };

  $scope.today();
  $scope.toggleMin();

	//redirection to addLicense
	$scope.newLicense = function(){
		$scope.license = {};
		$scope.formDone = 0;
	}

	//return to list when cancelling license creation
	$scope.addNewLicense = function(){

		$scope.license.PROVIDER_ID = $scope.license.PROVIDER.ID 

		licenseService.add($scope.license).then(function(response){
			$scope.response = response.data;
			$scope.addButton = 0;
			$scope.formDone = 1;
			$scope.newLicenseId = $scope.response;
		});

	}
}

}]);

