//Hardware List Controller
angular
  .module('simplify')
  .controller('HardwareListCtrl', ['$scope','$state','$q','$http','$filter','hardwareService','ngTableParams', function($scope, $state,$q, $http,$filter, hardwareService, ngTableParams) {
  //on load, we get hardwares list
  $scope.qView = $q.defer(); //promise qui peut être utilisée pour savoir quand les données sont dispos (used to avoid undefined variables or empty one)



	//list
	$scope.listHardware = function(hardware){
	  hardwareService.list().then(function(response){
	  	$scope.hardwares = response.data; 
	    $scope.qView.resolve(); //Notice the availability of data
	    // console.log('list hardwares : ' + JSON.stringify($scope.hardwares));
	  });
	}
	//go to detailed hardware view
	$scope.viewHardware = function(hardware){

		// console.log('hardware : ' + JSON.stringify(hardware.ID));
	  $state.go('viewHardware',{recordId:hardware.ID}); 

	}

	//search function
	$scope.searchHardware = function () {
			if ($scope.hardwareSearch == '' || $scope.hardwareSearch == 'undefined') {
				$scope.listHardware();
			}else{
				hardwareService.list($scope.hardwareSearch).then(function(response){
					$scope.hardwares = response.data;
					// console.log('hardwares : ' + JSON.stringify($scope.hardwares));
				});	
			}
	};	
	
	$scope.listHardware();

}]);