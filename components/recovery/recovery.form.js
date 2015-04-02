//Recovery CRUD Controller
angular
  .module('simplify')
  .controller('RecoveryFormCtrl', ['$scope','$state','$stateParams', '$http', '$window', 'recoveryService', function($scope, $state,$stateParams, $http, $window, recoveryService){
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
			if ($scope.left < 0 || to_use > 24 || isNaN($scope.left)) {
				$scope.invalidForm = 1;

			}else{
				$scope.invalidForm = 0;
			};
			// console.log('left : ', $scope.left);
		}
 
	});

  //switch hours or day
  $scope.switchHourDay = function(){
    if ($scope.recoveryForm.TO_USE >7) {
      $scope.switchButton = 1;
    };
  }


  //redirection to addLicense
  $scope.new = function(){   
    $scope.recoveryForm = {};   
    $scope.formDone = 0;
  }

  //show list function
  $scope.show = function(){
    $state.transitionTo('listRecovery', {reload: true});
  }

	$scope.add = function(){

			console.log('recoveryForm : ' + $scope.recoveryForm);

		recoveryService.addForm($scope.recoveryForm).then(function(response){
			$scope.formId = response.data;
      $scope.formDone = 1;
			console.log(' add response : ' + $scope.formId);
    });

	}

/*************************
*
*DISPLAY PDF (open a new window)
*
***************************/

$scope.displayPDF = function(formId){

  // open a new tab (if Google Chrome) to display the PDF
      
    //ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
    var str = '';
    str = $scope.formId;
    str = str.replace('"','');
    $scope.formId = str.replace('"',''); 
  $window.open('REST/recovery/pdf/read/'+$scope.formId);

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

