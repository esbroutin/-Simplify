//Recovery CRUD Controller
angular
  .module('simplify')
  .controller('RecoveryAdminCtrl', ['$scope','$state','$stateParams', '$http', 'recoveryService', function($scope, $state,$stateParams, $http, recoveryService){


  $scope.listFormAdmin = function(form){
    recoveryService.listFormAdmin(undefined).then(function(response){
      $scope.forms = response.data; 
      $scope.countFormsOnHold = $scope.forms['COUNT_ON_HOLD']; 
      $scope.forms = $scope.forms['DATA']; 
      console.log('countFormsOnHold : ', $scope.countFormsOnHold);
      console.log('response.data : ', response.data);

    });
   };


  $scope.refuse = function(form){


      console.log('form : ' + $scope.recoveryForm);

    recoveryService.refuse(form).then(function(response){
      $scope.response = response.data;
      console.log(' refuse response : ' + $scope.response);
      $scope.listFormAdmin();
      // $scope.formDone = 1;
    });

  };

  $scope.validate = function(form){

    console.log('form : ' + $scope.recoveryForm);

    recoveryService.validate(form).then(function(response){
      $scope.response = response.data;
      recoveryService.generatePDF(form.ID);
      console.log(' validate response : ' + $scope.response);
      $scope.listFormAdmin();
      // $scope.formDone = 1;
    });

  };

  //on load, we get recovery list
  $scope.listFormAdmin();

}]);

