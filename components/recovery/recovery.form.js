//Recovery CRUD Controller
angular
  .module('simplify')
  .controller('RecoveryFormCtrl', ['$scope','$state','$stateParams', '$http', '$window', 'recoveryService','adminService', function($scope, $state,$stateParams, $http, $window, recoveryService, adminService){
  	$scope.recoveryForm = {};
  	$scope.recoveryForm.AVAILABLE = 0;
    $scope.recoveryForm.TO_USE = 0;
    $scope.recoveryForm.minutes = 0;
  	$scope.recoveryForm.hours = 0;
    $scope.lock = 0;
    $scope.formDone = 0;
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

  $scope.$watchCollection('[recoveryForm.hours,recoveryForm.minutes,recoveryForm.TO_USE,recoveryForm.DATE]', function(newValues) {

    var to_use = $scope.recoveryForm.hours+($scope.recoveryForm.minutes/60);
    $scope.recoveryForm.TO_USE = to_use;
    var available = $scope.available;
    $scope.left = available - to_use;
    if ($scope.left < 0 || to_use > 24 || isNaN($scope.left)) {
      $scope.invalidForm = 1;

    }else{
      $scope.invalidForm = 0;
    };
    if ($scope.recoveryForm.DATE == undefined) {
      $scope.invalidForm = 1;

    };
 
  });
  //switch hours or day
  $scope.switchHourDay = function(){
    if ($scope.recoveryForm.hours >8) {
      $scope.switchButton = 1;
      $scope.recoveryForm.hours = 8;
      $scope.recoveryForm.minutes = 0;
      $scope.recoveryForm.TO_USE = 8;
      $scope.left = $scope.available - $scope.recoveryForm.TO_USE;  
    };
  }


  //redirection to addLicense
  $scope.new = function(){   
    console.log('new');
    $scope.recoveryForm = {};
    $scope.recoveryForm.AVAILABLE = 0;
    $scope.recoveryForm.TO_USE = 0;
    $scope.recoveryForm.minutes = 0;
    $scope.recoveryForm.hours = 1;
    $scope.addButton = 0;
    $scope.lock = 0;
    $scope.formDone = 0;
    //on load, we get recovery list
    recoveryService.list(undefined).then(function(response){
      $scope.recoveries = response.data; 
      // console.log('response.data : ', response.data);
      $scope.recoveries['SUM_RECOVERY_STOCK'] = $scope.recoveries.sum("RECOVERY_STOCK");
      $scope.recoveries['SUM_RECOVERY_USED'] = $scope.recoveries.sum("RECOVERY_USED");
      $scope.available = $scope.recoveries['SUM_RECOVERY_STOCK'] - $scope.recoveries['SUM_RECOVERY_USED'];

    });
  }

  //show list function
  $scope.show = function(){
    $state.transitionTo('listRecovery', {reload: true});
  }

	$scope.add = function(){

      $scope.lock = 1;
      $scope.addButton = 0;
		recoveryService.addForm($scope.recoveryForm).then(function(response){
			$scope.formId = response.data;
      $scope.formDone = 1;
      $scope.lock = 0;
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

