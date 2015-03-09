//Provider CRUD Controller
angular
  .module('simplify')
  .controller('ProviderCrudCtrl', ['$scope','$state','$stateParams', '$http', 'providerService', function($scope, $state,$stateParams, $http, providerService){

//we initialize our values
	$scope.formDone = 0;
	$scope.addButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listProvider', {reload: true});
	}

	//redirection to view created provider
	$scope.viewProvider = function(){

		$state.transitionTo('viewProvider',{recordId:$scope.newProviderId});
	}
	$scope.cancelAdd = function(){
		$state.transitionTo('listProvider', {reload: true});
	}

	$scope.add = function () {

		//we add the new provider then reload the providers list
		providerService.add($scope.newProviderForm).then(function(response){
			$scope.response = response.data;
			$scope.newProviderId = $scope.response;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newProviderId;
			str = str.replace('"','');
			$scope.newProviderId = str.replace('"',''); 
			$scope.viewProvider();

		});
	};


}]);

