//License List Controller
angular
  .module('simplify')
  .controller('ProviderListCtrl', ['$scope','$state','$http','providerService', function($scope, $state, $http, providerService) {

  //on load, we get licences list
  providerService.list(undefined).then(function(response){
  	$scope.providers = response.data; 
    // console.log('list providers : ' + JSON.stringify($scope.providers));
  });

//go to detailed provider view
$scope.viewProvider = function(provider){

	// console.log('provider : ' + JSON.stringify(provider.ID));
  $state.go('viewProvider',{recordId:provider.ID}); 

}

//search function
$scope.searchProvider = function () {

	if ($scope.providerSearch != 'undefined' && $scope.providerSearch != ''){

		providerService.list($scope.providerSearch).then(function(response){
			$scope.providers = response.data;
			// console.log('providers : ' + JSON.stringify($scope.providers));

		});
	};
}
		

}]);