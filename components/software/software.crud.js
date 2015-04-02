//Software CRUD Controller
angular
  .module('simplify')
  .controller('SoftwareCrudCtrl', ['$scope',
  																		'$state',
  																		'$stateParams',
  																		'$http',
  																		'softwareService',
  																		'brandService', function($scope, $state,$stateParams, $http, softwareService, brandService){

//start variables initialization

	$scope.formDone = 0;
	$scope.addButton = 0;
	$scope.software = {};

/****************************
*
*WATCH FORM VALIDITY
*
****************************/

	$scope.$watchCollection('[software.LABEL, software.CURRENT_VERSION, software.BRAND]', function(newValues) {
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

	//we list the brands
	brandService.list(undefined).then(function(response){
		$scope.brands = response.data;
	});

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listSoftware', {reload: true});
	}

	//redirection to view created software
	$scope.viewSoftware = function(){

		$state.transitionTo('viewSoftware',{recordId:$scope.newSoftwareId});
	}

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

	//new software form
	$scope.newSoftware = function(){   
		$scope.software = {};   
		$scope.formDone = 0;
	}

	//function to add a new versionning entry
	$scope.addNew = function(){
		// console.log('software : ', $scope.software);

		$scope.software.BRAND_ID = $scope.software.BRAND.ID 
		softwareService.add($scope.software).then(function(response){
			console.log('new id : ', response.data);
			$scope.formDone = 1;
			$scope.addButton = 0;
			$scope.newSoftwareId = response.data;


			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newSoftwareId;
			str = str.replace('"','');
			$scope.newSoftwareId = str.replace('"',''); 
		});
	}

}]);

