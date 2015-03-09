//License List Controller
angular
  .module('simplify')
  .controller('LicenseListCtrl', ['$scope','$rootScope','$state','$q','$http','$filter','licenseService','ngTableParams', function($scope, $rootScope, $state,$q, $http,$filter, licenseService, ngTableParams) {

	//list
	$scope.listLicense = function(license){
	  licenseService.list().then(function(response){
	  	$scope.licenses = response.data; 
	  });
	}
	//go to detailed license view
	$scope.viewLicense = function(license){

		// console.log('license : ' + JSON.stringify(license.ID));
	  $state.go('viewLicense',{recordId:license.ID}); 

	}

	//search function
	$scope.searchLicense = function () {
			if ($scope.licenseSearch == '' || $scope.licenseSearch == 'undefined') {
				$scope.listLicense();
			}else{
				licenseService.list($scope.licenseSearch).then(function(response){
					$scope.licenses = response.data;
					// console.log('licenses : ' + JSON.stringify($scope.licenses));
				});	
			}
	};	
	
	$scope.listLicense();

}]);