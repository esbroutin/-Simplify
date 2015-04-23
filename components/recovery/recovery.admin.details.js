//Recovery CRUD Controller
angular
  .module('simplify')
  .controller('RecoveryAdminDetailsCtrl', ['$scope','$state','$stateParams', '$http', 'recoveryService','$window', function($scope, $state,$stateParams, $http, recoveryService,$window){
  
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

  $scope.userId = $stateParams.recordId;
  recoveryService.listAdmin($scope.userId).then(function(response){
    $scope.recoveries = response.data;
    $scope.recoveries['SUM_RECOVERY_STOCK'] = $scope.recoveries.sum("RECOVERY_STOCK");
    $scope.recoveries['SUM_RECOVERY_USED'] = $scope.recoveries.sum("RECOVERY_USED");
    console.log(' recoveries : ' + $scope.recoveries);
    // $scope.formDone = 1;
  });
//go to detailed recovery view
$scope.viewRecovery = function(recovery){

  // console.log('recovery : ' + JSON.stringify(recovery.ID));
  $state.go('viewRecovery',{recordId:recovery.ID}); 

}
  //show list function
  $scope.admin = function(){
    $state.transitionTo('recoveryAdmin', {reload: true});
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
  $window.open('REST/recovery/pdf/read/'+$scope.formId);

}

}]);

