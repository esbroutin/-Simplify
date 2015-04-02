//Certificate CRUD Controller
angular
  .module('simplify')
  .controller('CertificateViewCtrl', ['$scope',
								'$state',
								'$stateParams',
								'$http',
								'$rootScope',
								'$window',
								'certificateService',
								'providerService',
								'brandService', function($scope, $state,$stateParams, $http,$rootScope,$window, certificateService, providerService, brandService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: certificateService }));

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.list = function(){
		$state.transitionTo('listCertificate', {reload: true});
	}
	
/***************************
*
*VIEW CERTIFICATE MODE (state : VIEW)
*
****************************/

	//we get details for selected certificate
	certificateService.get($stateParams.recordId).then(function(response){
		$scope.certificate = response.data;
		console.log('certificate',$scope.certificate);
	});

	$scope.edit = function(){

		$scope.editButton = 1;

		// we load the provider list uin case we edit the form
		providerService.list(undefined).then(function(response){
			$scope.providers = response.data;
		});

		// we load the provider list uin case we edit the form
		brandService.list(undefined).then(function(response){
			$scope.brands = response.data;
		});

	}

	$scope.save = function(){

		certificateService.update($scope.certificate).then(function(response){
			$scope.certificate.ID = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.certificate.ID;
			str = str.replace('"','');
			$scope.certificate.ID = str.replace('"',''); 
			$rootScope.listAlerts ();
			$state.go('viewCertificate',{recordId:$scope.certificate.ID}, { reload: true, inherit: false, notify: true }); 
		});	
	}

	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewCertificate',{recordId:$scope.certificate.ID}, {reload: true}); 
	}

	$scope.delete = function(){

		$scope.confirmDelete = 1;
		certificateService.delete($scope.certificate.ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}

/**********************
* datePicker
**********************/

  $scope.openStartDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();
    $scope.openedStartDate = true;
  };

  $scope.openEndDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();
    $scope.openedEndDate = true;
  };

  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };
  
}]);

