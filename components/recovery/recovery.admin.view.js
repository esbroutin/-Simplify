//Recovery CRUD Controller
angular
  .module('simplify')
  .controller('RecoveryAdminViewCtrl', ['$scope','$state','$stateParams', '$http', 'recoveryService', function($scope, $state,$stateParams, $http, recoveryService){
  	$scope.recoveryForm = {};
  	$scope.recoveryForm.AVAILABLE = 0;
  	$scope.recoveryForm.TO_USE = 0;
//function to sum up values
Array.prototype.sum = function (prop) {
    var total = 0
    for ( var i = 0, _len = this.length; i < _len; i++ ) {
    	if (this[i][prop] != null) {
        total += parseFloat(this[i][prop]);
    	};
    }
    return total
}
  //on load, we get recovery list
  recoveryService.list(undefined).then(function(response){
  	$scope.recoveries = response.data; 
			// console.log('response.data : ', response.data);
	  	$scope.recoveries['SUM_RECOVERY_STOCK'] = $scope.recoveries.sum("RECOVERY_STOCK");
	  	$scope.recoveries['SUM_RECOVERY_USED'] = $scope.recoveries.sum("RECOVERY_USED");
	  	$scope.available = $scope.recoveries['SUM_RECOVERY_STOCK'] - $scope.recoveries['SUM_RECOVERY_USED'];

  });

	$scope.$watchCollection('[recoveryForm.TO_USE]', function(newValues) {

		if($scope.recoveryForm.TO_USE !=undefined) {

			var to_use = $scope.recoveryForm.TO_USE;
			var available = $scope.available;
			$scope.left = available - to_use;
			if ($scope.left <= 0 || to_use > 24 || isNaN($scope.left)) {
				$scope.invalidForm = 1;

			}else{
				$scope.invalidForm = 0;
			};
			// console.log('left : ', $scope.left);
		}

	});

  //show list function
  $scope.show = function(){
    $state.transitionTo('listRecovery', {reload: true});
  }

	$scope.add = function(){

			console.log('recoveryForm : ' + $scope.recoveryForm);

		recoveryService.addForm($scope.recoveryForm).then(function(response){
			$scope.response = response.data;
			console.log(' add response : ' + $scope.response);
			// $scope.addButton = 0;
			// $scope.formDone = 1;
		});

	}

/**********************
* datePicker
**********************/

	$scope.today = function() {
    $scope.recoveryForm.DATE = new Date();
  };

  $scope.clear = function () {
    $scope.recoveryForm.DATE = null;
  };

  $scope.openDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedDate = true;
  };

  $scope.toggleMin = function() {
    $scope.minDateEnd = $scope.recoveryForm.DATE;
  };

  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };

  $scope.today();
  $scope.toggleMin();
}]);

