//Brand CRUD Controller
angular
  .module('simplify')
  .controller('BrandViewCtrl', ['$scope','$state','$stateParams', '$http', 'brandService', function($scope, $state,$stateParams, $http, brandService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: licenseService }));

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listBrand', {reload: true});
	}

	//we get details for selected brand
	brandService.get($stateParams.recordId).then(function(response){
		$scope.brand = response.data;
	});
	$scope.edit = function(){

		$scope.editButton = 1;

	}

	$scope.save = function(){

		brandService.update($scope.brand).then(function(response){
			$scope.response = response.data;
			$scope.newBrandId = $scope.response;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newBrandId;
			str = str.replace('"','');
			$scope.newBrandId = str.replace('"',''); 
  			$state.go('viewBrand',{recordId:$scope.newBrandId}, {reload: true}); 
		});

	
	}

	$scope.deleteBrand = function(){

		brandService.delete($scope.brand.ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}

	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewBrand',{recordId:$scope.brand.ID}, {reload: true}); 
	}

}]);

