//License List Controller
angular
  .module('simplify')
  .controller('LicenseListCtrl', ['$scope','$state','$http','licenseService', function($scope, $state, $http, licenseService) {

  //on load, we get licences list
  licenseService.list().then(function(response){
  	$scope.licenses = response.data; 
    // console.log('list licenses : ' + JSON.stringify($scope.licenses));
  });

//go to detailed license view
$scope.viewLicense = function(license){

	// console.log('license : ' + JSON.stringify(license.ID));
  $state.go('viewLicense',{recordId:license.ID}); 

}

//search function
$scope.searchLicense = function () {

	if ($scope.licenseSearch != 'undefined' && $scope.licenseSearch != ''){

		licenseService.list($scope.licenseSearch).then(function(response){
			$scope.licenses = response.data;
			// console.log('licenses : ' + JSON.stringify($scope.licenses));

		});
	};
}
		

}]);