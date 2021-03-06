//Hardware CRUD Controller
angular
  .module('simplify')
  .controller('HardwareCrudCtrl', ['$scope',
								'$state',
								'$stateParams',
								'$http',
								'$rootScope',
								'hardwareService',
								'providerService',
								'brandService',
								 function($scope, $state,$stateParams, $http, $rootScope, hardwareService, providerService, brandService){

//we initialize our values
	$scope.formDone = 0;
	$scope.addButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listHardware', {reload: true});
	}

	//redirection to view created hardware
	$scope.viewHardware = function(){

		console.log('$scope.newHardwareId : ' + $scope.newHardwareId);
		$state.transitionTo('viewHardware',{recordId:$scope.newHardwareId});
	}

	$scope.hardware = {};

	// we load the provider list
	providerService.list(undefined).then(function(response){
		$scope.providers = response.data;
			});
	// we load the brand list
	brandService.list(undefined).then(function(response){
		$scope.brands = response.data;
			});
	// we load the types list
	hardwareService.listTypes().then(function(response){
		$scope.types = response.data;
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

	$scope.addType = function () {

		//we add the new provider then reload the providers list
		hardwareService.addType($scope.newTypeForm).then(function(response){
			//reloading & hide the provider form
			hardwareService.listTypes().then(function(response){
				$scope.types = response.data;
				$scope.newType = 0;
				$scope.newTypeForm = {};

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

	$scope.$watchCollection('[hardware.WARRANTY_START, hardware.WARRANTY_END, hardware.PROVIDER,hardware.TYPE,hardware.BRAND, hardware.LABEL]', function(newValues) {
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


	//redirection to addHardware
	$scope.newHardware = function(){   
		$scope.hardware = {};   
		$scope.formDone = 0;
	}

	//return to list when cancelling hardware creation
	$scope.addNewHardware = function(){

		$scope.hardware.PROVIDER_ID = $scope.hardware.PROVIDER.ID 
		$scope.hardware.BRAND_ID = $scope.hardware.BRAND.ID 
		$scope.hardware.TYPE = $scope.hardware.TYPE.ID 
			console.log('hardware : ' + $scope.hardware);

		hardwareService.add($scope.hardware).then(function(response){
			$scope.response = response.data;
			$scope.addButton = 0;
			$scope.formDone = 1;
			$scope.newHardwareId = $scope.response;
			$rootScope.listAlerts ();

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newHardwareId;
			str = str.replace('"','');
			$scope.newHardwareId = str.replace('"',''); 
			// console.log('newHardwareId : ' + $scope.newHardwareId);
		});

	}
	
/**********************
* datePicker (for warranty date selection)
**********************/

	$scope.today = function() {
    $scope.hardware.WARRANTY_START = new Date();
  };

  $scope.clear = function () {
    $scope.hardware.WARRANTY_START = null;
  };

  $scope.openStartDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedStartDate = true;
  };

  $scope.openDeployDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedDeployDate = true;
  };

  $scope.openEndDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedEndDate = true;
  };

  $scope.toggleMin = function() {
    $scope.minDateEnd = $scope.hardware.WARRANTY_START;
  };

  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };

  $scope.today();
  $scope.toggleMin();
}]);

