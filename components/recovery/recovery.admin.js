//Recovery CRUD Controller
angular
  .module('simplify')
  .controller('RecoveryAdminCtrl', ['$scope','$rootScope','$state','$stateParams', '$http', '$modal', 'recoveryService','adminService','$window', function($scope,$rootScope, $state,$stateParams, $http, $modal, recoveryService,adminService,$window){

  $scope.open = function (form) {

    var modalInstance = $modal.open({
      templateUrl: 'templates/modal_refuse.html',
      controller: 'ModalInstanceCtrl',
      resolve: {
        items: function () {
          return form;
        }
      }
    });
    }

  $rootScope.listFormAdmin = function(form){
    recoveryService.listFormAdmin(undefined).then(function(response){
      $scope.forms = response.data; 
      $scope.countFormsOnHold = $scope.forms['COUNT_ON_HOLD']; 
      $scope.forms = $scope.forms['DATA']; 
      // console.log('countFormsOnHold : ', $scope.countFormsOnHold);
      console.log('response.data : ', response.data);

    });
   };

  //show list function
  $scope.viewDetails = function(userId){
    console.log('viewDetails : ', userId);
  $state.go('adminDetailsRecovery',{recordId:userId}); 
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
  $scope.validate = function(form){

    // console.log('form : ' + $scope.recoveryForm);

    recoveryService.validate(form).then(function(response){
      $scope.response = response.data;
      recoveryService.generatePDF(form.ID);
      // console.log(' validate response : ' + $scope.response);
      $rootScope.listFormAdmin();
      // $scope.formDone = 1;
    });

  };



  //we make sure we have a type-> number in the field
   $scope.$watch('agentSearch', function() {
      if ($scope.agentSearch == 1) {
        $scope.listUsers(undefined);
      };
   });


  $scope.listUsers = function(search){

    // console.log('form : ' + $scope.recoveryForm);

    adminService.listUsers(search).then(function(response){
      $scope.users = response.data;
    });

  };

  //on load, we get recovery list
  $rootScope.listFormAdmin();

}]);


angular.module('simplify').controller('ModalInstanceCtrl', ['$scope','$rootScope','$modalInstance','items','recoveryService', function ($scope,$rootScope, $modalInstance, items,recoveryService) {

  console.log('items: ', items);
  $scope.form = items;
  $scope.refuse = function(form){

    console.log('form : ' + $scope.recoveryForm);
    $scope.loading = 1; 
    recoveryService.refuse(form).then(function(response){
      $scope.response = response.data;
      console.log(' refuse response : ' + $scope.response.data);
      $scope.loading = 0; 
      $rootScope.listFormAdmin();
      $modalInstance.close();
      // $scope.formDone = 1;
    });

  };

  $scope.cancel = function () {
    $modalInstance.dismiss('cancel');
  };

}]);