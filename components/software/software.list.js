//Software List Controller
angular
  .module('simplify')
  .controller('SoftwareListCtrl', ['$scope','$state','$http','$q','softwareService', function($scope, $state, $http,$q, softwareService) {
  $scope.qView = $q.defer(); //promise qui peut être utilisée pour savoir quand les données sont dispos (used to avoid undefined variables or empty one)

	//list
	$scope.listSoftware = function(software){
	  softwareService.list().then(function(response){
	  	$scope.softwares = response.data; 
	    $scope.qView.resolve(); //Notice the availability of data
	    // console.log('list softwares : ' + JSON.stringify($scope.softwares));
	  });
	}
	//go to detailed software view
	$scope.viewSoftware = function(software){

		// console.log('software : ' + JSON.stringify(software.ID));
	  $state.go('viewSoftware',{recordId:software.ID}); 

	}

	//search function
	$scope.searchSoftware = function () {
			if ($scope.softwareSearch == '' || $scope.softwareSearch == 'undefined') {
				$scope.listSoftware();
			}else{
				softwareService.list($scope.softwareSearch).then(function(response){
					$scope.softwares = response.data;
					// console.log('softwares : ' + JSON.stringify($scope.softwares));
				});	
			}
	};	
	
	$scope.listSoftware();

}]);