//Provider CRUD Controller
angular
  .module('simplify')
  .controller('ProviderViewCtrl', ['$scope','$state','$stateParams', '$http', 'providerService', function($scope, $state,$stateParams, $http, providerService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: licenseService }));

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listProvider', {reload: true});
	}

	//we get details for selected provider
	providerService.get($stateParams.recordId).then(function(response){
		$scope.provider = response.data;
	});
	$scope.edit = function(){

		$scope.editButton = 1;

	}

	$scope.save = function(){

		providerService.update($scope.provider).then(function(response){
			$scope.response = response.data;
			$scope.newProviderId = $scope.response;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newProviderId;
			str = str.replace('"','');
			$scope.newProviderId = str.replace('"',''); 
  			$state.go('viewProvider',{recordId:$scope.newProviderId}, {reload: true}); 
		});

	
	}

	$scope.deleteProvider = function(){

		providerService.delete($scope.provider.ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}

	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewProvider',{recordId:$scope.provider.ID}, {reload: true}); 
	}

}]);

