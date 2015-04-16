//Certificate CRUD Controller
'use strict';
angular
  .module('simplify')
  .controller('CertificateViewCtrl', ['$scope',
								'$state',
								'$stateParams',
								'$http',
								'$rootScope',
								'$window',
								'certificateService',
								'$upload',
								'providerService',
								'brandService', function($scope, $state,$stateParams, $http,$rootScope,$window, certificateService,$upload, providerService, brandService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: certificateService }));

	$rootScope.listAlerts ();
//we initialize our values
	$scope.deleteButton = 0;
	$scope.uploading = 0;
	$scope.progressPercentage = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.list = function(){
		$state.transitionTo('listCertificate', {reload: true});
	}
/*************************
*
*download Files
*
***************************/

$scope.downloadFiles = function(){
  $window.open('REST/certificate/download/'+$scope.certificate.ID);

}
	
/***************************
*
*VIEW CERTIFICATE MODE (state : VIEW)
*
****************************/

	//we get details for selected certificate
	certificateService.get($stateParams.recordId).then(function(response){
		$scope.certificate = response.data;
		// console.log('certificate',$scope.certificate);
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
			// $scope.certificate.COMMENTS = $scope.certificate.COMMENTS.replace("'",'');
			console.log($scope.certificate) ;

		certificateService.update($scope.certificate).then(function(response){
			$scope.certificate.ID = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.certificate.ID;
			str = str.replace('"','');
			$scope.certificate.ID = str.replace('"',''); 
			$rootScope.listAlerts ();
    		$scope.upload($scope.files);
			$state.go('viewCertificate',{recordId:$scope.certificate.ID}, { reload: true, inherit: false, notify: true }); 
		});	
	}


	
/*************************
*
*upload Files
*
***************************/

    $scope.upload = function (files) {
        if (files && files.length) {
        		$scope.uploading = 1;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var id = $scope.certificate.ID+'_'+file.name;
                console.log('uploading', $scope.uploading);
                console.log('file', file);
                console.log('id', id); 
                $upload.upload({
				    url: 'REST/certificate/upload', 
				    headers: {'Content-Type': file.type},
				    method: 'POST',
				    data: file,
				    file: file,
				    fileName: id
                }).progress(function (evt) {
                    $scope.progressPercentage = Math.round(parseInt(100.0 * evt.loaded / evt.total));
                console.log('uploading', $scope.uploading);
                    console.log('progress: ' + $scope.progressPercentage + '% ' +
                                evt.config.file.name);
                }).success(function (data, status, headers, config) {
        			$scope.uploading = 0;
                console.log('uploading', $scope.uploading);
                    // console.log('file ' + config.file.name + 'uploaded. Response: ' +
                                // JSON.stringify(data));
                });
            }
        }
    };
	
	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewCertificate',{recordId:$scope.certificate.ID}, {reload: true}); 
	}

	$scope.delete = function(){

		$scope.confirmDelete = 1;
		certificateService.delete($scope.certificate.ID).then(function(response){
			$scope.response = response.data;
			$rootScope.listAlerts ();
			$scope.list();
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

