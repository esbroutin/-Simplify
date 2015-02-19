//License CRUD Controller
angular
  .module('simplify')
  .controller('LicenseViewCtrl', ['$scope','$state','$stateParams', '$http', 'licenseService', function($scope, $state,$stateParams, $http, licenseService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: licenseService }));

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listLicense', {reload: true});
	}
	
/***************************
*
*VIEW LICENSE MODE (state : VIEW)
*
****************************/

if ($state.current.name == 'viewLicense') {

	//we get details for selected license
	licenseService.get($stateParams.recordId).then(function(response){
		$scope.license = response.data;
	});

	$scope.deleteLicense = function(){

		$scope.confirmDelete = 1;

		licenseService.delete($scope.license.LICENSE_ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}
}

}]);

