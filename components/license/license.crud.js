//License CRUD Controller
angular
  .module('simplify')
  .controller('LicenseCrudCtrl', ['$scope',
								'$state',
								'$stateParams',
								'$http',
								'licenseService',
								'providerService',
								'brandService',
								 function($scope, $state,$stateParams, $http, licenseService, providerService, brandService){

//we initialize our values
	$scope.formDone = 0;
	$scope.addButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listLicense', {reload: true});
	}

	//redirection to view created license
	$scope.viewLicense = function(){

		console.log('$scope.newLicenseId : ' + $scope.newLicenseId);
		$state.transitionTo('viewLicense',{recordId:$scope.newLicenseId});
	}

	$scope.license = {};

	// we load the provider list
	providerService.list(undefined).then(function(response){
		$scope.providers = response.data;
			});
	// we load the brand list
	brandService.list(undefined).then(function(response){
		$scope.brands = response.data;
			});

	$scope.addProvider = function () {

		//we add the new provider then reload the providers list
		providerService.add($scope.newProviderForm).then(function(response){
			//reloading & hide the provider form
			providerService.list(undefined).then(function(response){
				$scope.providers = response.data;
				$scope.newProvider = 0;
				$scope.newProviderForm = {};

			});
		});
	};

	$scope.addBrand = function () {

		//we add the new provider then reload the providers list
		brandService.add($scope.newBrandForm).then(function(response){
			//reloading & hide the provider form
			brandService.list(undefined).then(function(response){
				$scope.brands = response.data;
				$scope.newBrand = 0;
				$scope.newBrandForm = {};

			});
		});
	};
	
/****************************
*
*WATCH FORM VALIDITY
*
****************************/

	$scope.$watchCollection('[license.DATE_START, license.DATE_END, license.PROVIDER, license.BRAND, license.LABEL]', function(newValues) {
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


	//redirection to addLicense
	$scope.newLicense = function(){   
		$scope.license = {};   
		$scope.formDone = 0;
	}

	//return to list when cancelling license creation
	$scope.addNewLicense = function(){

		$scope.license.PROVIDER_ID = $scope.license.PROVIDER.ID 
		$scope.license.BRAND_ID = $scope.license.BRAND.ID 
			console.log('license.PROVIDER_ID : ' + $scope.license.PROVIDER_ID);

		licenseService.add($scope.license).then(function(response){
			$scope.addButton = 0;
			$scope.formDone = 1;
			$scope.newLicenseId = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newLicenseId;
			str = str.replace('"','');
			$scope.newLicenseId = str.replace('"',''); 
			// console.log('newLicenseId : ' + $scope.newLicenseId);
		});

	}
	
/**********************
* datePicker
**********************/

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
}]);

