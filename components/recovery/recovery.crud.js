//Provider CRUD Controller
angular
  .module('simplify')
  .controller('RecoveryCrudCtrl', ['$scope','$rootScope','$state','$stateParams', '$http', 'recoveryService', function($scope, $rootScope, $state,$stateParams, $http, recoveryService){

	$scope.recovery = {};
	$scope.recovery.LUNCH_PAUSE = true;
	$scope.recovery.IS_SATURDAY = false;
	$scope.recovery.SPECIAL_DAY = false;
	$scope.formDone = 0;
	$scope.addButton = 0;
  $scope.START_TIME = new Date(1970, 0, 1, 07, 30, 0);
  $scope.END_TIME = new Date(1970, 0, 1, 16, 30, 0);

	//show list function
	$scope.show = function(){
		$state.transitionTo('listRecovery', {reload: true});
	}

	//redirection to view created license
	$scope.view = function(){

		console.log('$scope.newRecoveryId : ' + $scope.newRecoveryId);
		$state.transitionTo('viewRecovery',{recordId:$scope.newRecoveryId});
	}

	//redirection to addLicense
	$scope.new = function(){   
		$scope.recovery = {};   
		$scope.formDone = 0;
	}

	//return to list when cancelling license creation
	$scope.add = function(){
		$scope.recovery.USER_ID = $rootScope.userName;

		// console.log('recovery : ' ,$scope.recovery);

		recoveryService.add($scope.recovery).then(function(response){
			$scope.addButton = 0;
			$scope.formDone = 1;
			$scope.newRecoveryId = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newRecoveryId;
			str = str.replace('"','');
			$scope.newRecoveryId = str.replace('"',''); 
			console.log('newRecoveryId : ' + $scope.newRecoveryId);
		});
	}

/****************************
*
*WATCH FORM VALIDITY
*
****************************/

	$scope.$watchCollection('[recovery.START_TIME, recovery.END_TIME, recovery.LABEL]', function(newValues) {
		var countDefined='';

		for (var i = newValues.length - 1; i >= 0; i--) {

			if(newValues[i] !=0 && newValues[i] !=undefined) {
				countDefined++;
			}
		};

		$scope.completeForm = ((countDefined) *100)/(newValues.length);
		$scope.completeForm = Math.round($scope.completeForm);
		$scope.remainingProgress = 100 - $scope.completeForm;
			// console.log('completeForm : ' + $scope.completeForm);
	});

	
/**********************
* datePicker
**********************/

	$scope.today = function() {
    $scope.recovery.DATE = new Date();
  };

  $scope.clear = function () {
    $scope.recovery.DATE = null;
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

  // $scope.today();



}]);

