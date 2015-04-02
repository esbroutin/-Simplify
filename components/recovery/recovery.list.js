//License List Controller
angular
  .module('simplify')
  .controller('RecoveryListCtrl', ['$scope','$state','$window','$http','recoveryService', function($scope, $state, $window, $http, recoveryService) {

      $scope.today = new Date();
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
    $scope.recoveries['SUM_RECOVERY_STOCK'] = $scope.recoveries.sum("RECOVERY_STOCK");
    $scope.recoveries['SUM_RECOVERY_USED'] = $scope.recoveries.sum("RECOVERY_USED");
  });
  //on load, we get the recovery requests on hold
  recoveryService.listForm().then(function(response){
  	$scope.forms = response.data; 
  });

//go to detailed recovery view
$scope.viewRecovery = function(recovery){

	// console.log('recovery : ' + JSON.stringify(recovery.ID));
  $state.go('viewRecovery',{recordId:recovery.ID}); 

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
    str = formId;
    str = str.replace('"','');
    $scope.formId = str.replace('"',''); 
  $window.open('REST/recovery/pdf/read/'+formId);

}
//search function
$scope.searchRecovery = function () {

	if ($scope.recoverySearch != 'undefined' && $scope.recoverySearch != ''){

		recoveryService.list($scope.recoverySearch).then(function(response){
			$scope.recoveries = response.data;
	  	$scope.recoveries['SUM_RECOVERY_STOCK'] = $scope.recoveries.sum("RECOVERY_STOCK");
	  	$scope.recoveries['SUM_RECOVERY_USED'] = $scope.recoveries.sum("RECOVERY_USED");
			// console.log('recoveries : ' + JSON.stringify($scope.recoveries));

		});
	};
}
		

}]);