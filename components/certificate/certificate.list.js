//Certificate List Controller
angular
  .module('simplify')
  .controller('CertificateListCtrl', ['$scope','$rootScope','$state','$q','$http','$filter','certificateService', function($scope, $rootScope, $state,$q, $http,$filter, certificateService) {

	//list
	$scope.list = function(certificate){
	  certificateService.list().then(function(response){
	  	$scope.certificates = response.data; 
					console.log('certificates : ' + JSON.stringify($scope.certificates));
	  });
	}
	//go to detailed certificate view
	$scope.view = function(certificateId){

		// console.log('certificate : ' + JSON.stringify(certificate.ID));
	  $state.go('viewCertificate',{recordId:certificateId}); 

	}

	//search function
	$scope.search = function () {
			if ($scope.certificateSearch == '' || $scope.certificateSearch == 'undefined') {
				$scope.list();
			}else{
				certificateService.list($scope.certificateSearch).then(function(response){
					$scope.certificates = response.data;
					// console.log('certificates : ' + JSON.stringify($scope.certificates));
				});	
			}
	};	
	
	$scope.list();

}]);