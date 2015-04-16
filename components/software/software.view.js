//Software CRUD Controller
'use strict';
angular
  .module('simplify')
  .controller('SoftwareViewCtrl', ['$scope','$state','$stateParams', '$http', '$upload','$window', 'softwareService', 'brandService', function($scope, $state,$stateParams, $http,$upload,$window, softwareService, brandService){

// angular.extend(this,$controller('BaseCRUDCtrl', { $scope: $scope, dataService: licenseService }));

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;
	$scope.mode = 1;
	$scope.maintenanceAdd = {};
	$scope.uploading = 0;
	$scope.progressPercentage = 0;
	//show list function
	$scope.showList = function(){
		$state.transitionTo('listSoftware', {reload: true});
	}

	//we get details for current software
	softwareService.get($stateParams.recordId).then(function(response){
		$scope.software = response.data;
		$scope.listMaintenance($scope.software.ID);
		// console.log('software : ', $scope.software);
	});

	$scope.edit = function(){

		//we list the brands
		brandService.list(undefined).then(function(response){
			$scope.brands = response.data;
			// console.log('enterprises : ' + $scope.enterprises);
		});
		$scope.editButton = 1;

	}

	$scope.maintenanceDetails = function(data){
		console.log('maintenance details : ', data);
		$scope.detailsView =1;
		$scope.details = data;

	}

	$scope.listMaintenance = function(data){

		//we list the associated maintenances
		softwareService.listMaintenance(data).then(function(response){
			$scope.maintenances = response.data;
			console.log('maintenances : ' , $scope.maintenances);
		});

	}

/*************************
*
*upload Maintenance Files
*
***************************/

    $scope.upload = function (files) {

        if (files && files.length) {
        		$scope.uploading = 1;
            for (var i = 0; i < files.length; i++) {
              var file = files[i];
              var id = $scope.newMaintenanceId+'_'+file.name;
              console.log('file', file);
              console.log('id', id);
              $upload.upload({
						    url: 'REST/software/maintenance/upload', 
						    headers: {'Content-Type': file.type},
						    method: 'POST',
						    data: file,
						    file: file,
						    fileName: id
                }).progress(function (evt) {
                    $scope.progressPercentage = Math.round(parseInt(100.0 * evt.loaded / evt.total));
                    console.log('progress: ' + $scope.progressPercentage + '% ' + evt.config.file.name);
                }).success(function (data, status, headers, config) {
                	$scope.listMaintenance($scope.software.ID);
                    console.log('file ' + config.file.name + 'uploaded. Response: ' + JSON.stringify(data));
        						$scope.uploading = 0;
                })
            }
        }else{
    			$scope.listMaintenance($scope.software.ID);
					$scope.uploading = 0;
        }
    };
	
/*************************
*
*download Files
*
***************************/

$scope.downloadFiles = function(maintenanceId){
  $window.open('REST/software/maintenance/download/'+maintenanceId);

}
	
	$scope.save = function(){
		$scope.software.BRAND_ID = $scope.software.BRAND.ID 
		// console.log('software : ', $scope.software);

		softwareService.update($scope.software).then(function(response){
			$scope.newSoftwareId = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newSoftwareId;
			str = str.replace('"','');
			$scope.newSoftwareId = str.replace('"',''); 
  			$state.go('viewSoftware',{recordId:$scope.newSoftwareId}, {reload: true}); 
		});	
	}
	
	$scope.addMaintenance = function(){
		// console.log('software : ', $scope.software);
		$scope.maintenanceAdd.LINKED_ID = $scope.software.ID;
		softwareService.addMaintenance($scope.maintenanceAdd).then(function(response){
			$scope.newMaintenanceId = response.data;
		console.log('newMaintenanceId : ', $scope.newMaintenanceId);
			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newMaintenanceId;
			str = str.replace('"','');
			$scope.newMaintenanceId = str.replace('"',''); 
			$scope.upload($scope.files);
			$scope.maintenanceView = 0;
			$scope.mode = 1;
			$scope.maintenanceAdd = {};	
		});	
	}

	$scope.deleteSoftware = function(){

		softwareService.delete($scope.software.ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}

	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewSoftware',{recordId:$scope.software.ID}, {reload: true}); 
	}

/**********************
* datePicker
**********************/

	$scope.today = function() {
    $scope.maintenanceAdd.DATE = new Date();
  };

  $scope.clear = function () {
    $scope.maintenanceAdd.DATE = null;
  };

  $scope.openDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedDate = true;
  };


  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };

  $scope.today();
}]);

