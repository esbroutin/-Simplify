//Software CRUD Controller
angular
  .module('simplify')
  .controller('SoftwareViewCtrl', ['$scope','$state','$stateParams', '$http', 'softwareService', 'brandService', function($scope, $state,$stateParams, $http, softwareService, brandService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: licenseService }));

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listSoftware', {reload: true});
	}

	//we get details for current software
	softwareService.get($stateParams.recordId).then(function(response){
		$scope.software = response.data;
		// console.log('software : ', $scope.software);
	});

	$scope.edit = function(){

		//we list the brands
		brandService.list(undefined).then(function(response){
			$scope.brands = response.data;
			// console.log('enterprises : ' + $scope.enterprises);
		});
		$scope.editButton = 1;

	}

	$scope.save = function(){
		$scope.software.BRAND_ID = $scope.software.BRAND.ID 
		// console.log('software : ', $scope.software);

		softwareService.update($scope.software).then(function(response){
			$scope.newSoftwareId = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newSoftwareId;
			str = str.replace('"','');
			$scope.newSoftwareId = str.replace('"',''); 
  			$state.go('viewSoftware',{recordId:$scope.newSoftwareId}, {reload: true}); 
		});	
	}

	$scope.deleteSoftware = function(){

		softwareService.delete($scope.software.ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}

	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewSoftware',{recordId:$scope.software.ID}, {reload: true}); 
	}

}]);

