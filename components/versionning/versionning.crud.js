//Versionning CRUD Controller
angular
  .module('simplify')
  .controller('VersionningCrudCtrl', ['$scope','$state','$stateParams', '$http', 'versionningService', function($scope, $state,$stateParams, $http, versionningService){

//start variables initialization

	$scope.formDone = 0;
	$scope.addButton = 0;

	//we list the enterprises
	versionningService.listEnterprise().then(function(response){
		$scope.enterprises = response.data;
		// console.log('enterprises : ' + $scope.enterprises);
	});

	//function to add a new versionning entry
	$scope.addNewVersion = function(){
		versionningService.add($scope.version).then(function(response){
			$scope.formDone = 1;
			$scope.newVersion = response.data;
		});
	}

}]);

