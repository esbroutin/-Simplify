//Maintenance CRUD Controller
angular
  .module('simplify')
  .controller('MaintenanceCrudCtrl', ['$scope',
								'$state',
								'$stateParams',
								'$http',
								'maintenanceService',
								'softwareService',
								 function($scope, $state,$stateParams, $http, maintenanceService, softwareService){

//we initialize our values
	$scope.formDone = 0;
	$scope.addButton = 0;
	$scope.maintenance = {};
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listMaintenance', {reload: true});
	}

	//show list function
	$scope.listSoftwares = function(){

		softwareService.list(undefined).then(function(response){
			$scope.softwares = response.data;
			console.log('softwares : ', $scope.softwares);
		});
	}

	//redirection to view created maintenance
	$scope.viewMaintenance = function(){

		console.log('$scope.newMaintenanceId : ' + $scope.newMaintenanceId);
		$state.transitionTo('viewMaintenance',{recordId:$scope.newMaintenanceId});
	}

	
/****************************
*
*WATCH FORM VALIDITY
*
****************************/

	$scope.$watchCollection('[maintenance.WARRANTY_START, maintenance.WARRANTY_END, maintenance.PROVIDER,maintenance.TYPE,maintenance.BRAND, maintenance.LABEL]', function(newValues) {
		var countDefined='';

		for (var i = newValues.length - 1; i >= 0; i--) {

			if(newValues[i] !=0 && newValues[i] !=undefined) {
				countDefined++;
			}
		};

		$scope.completeForm = ((countDefined) *100)/(newValues.length);
		$scope.completeForm = Math.round($scope.completeForm);
		$scope.remainingProgress = 100 - $scope.completeForm;
	});


	//redirection to addMaintenance
	$scope.newMaintenance = function(){   
		$scope.maintenance = {};   
		$scope.formDone = 0;
	}

	//return to list when cancelling maintenance creation
	$scope.addNewMaintenance = function(){
			$scope.maintenance.SoftwareId = $scope.maintenance.software.ID;
			console.log('maintenance : ' + $scope.maintenance);

		maintenanceService.add($scope.maintenance).then(function(response){
			$scope.response = response.data;
			$scope.addButton = 0;
			$scope.formDone = 1;
			$scope.newMaintenanceId = $scope.response;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newMaintenanceId;
			str = str.replace('"','');
			$scope.newMaintenanceId = str.replace('"',''); 
			// console.log('newMaintenanceId : ' + $scope.newMaintenanceId);
		});

	}
	
/**********************
* datePicker (for warranty date selection)
**********************/

	$scope.today = function() {
    $scope.maintenance.DATE = new Date();
  };

  $scope.clear = function () {
    $scope.maintenance.DATE = null;
  };

  $scope.openDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedDate = true;
  };
  $scope.toggleMin = function() {
    $scope.minDateEnd = $scope.maintenance.DATE;
  };

  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };

  $scope.today();
  $scope.toggleMin();
  $scope.listSoftwares();
}]);

