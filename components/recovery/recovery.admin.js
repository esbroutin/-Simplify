//Recovery CRUD Controller
angular
  .module('simplify')
  .controller('RecoveryAdminCtrl', ['$scope','$rootScope','$state','$stateParams', '$http', '$modal', 'recoveryService','adminService','$window', function($scope,$rootScope, $state,$stateParams, $http, $modal, recoveryService,adminService,$window){
  $scope.loadMoreStep = 25;
  $scope.forms = [];
  $scope.countFormsOnHold = 0;

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

  $rootScope.updateList = function(){
    if ($rootScope.reset == 1) {
      $scope.forms = [];
      $scope.countFormsOnHold = 0;
      $rootScope.reset = 0;
    };
    recoveryService.listFormAdmin($scope.forms.length,$scope.loadMoreStep).then(function(response){ 
      $scope.newforms = response.data;
      if ($scope.newforms['DATA'].length < $scope.loadMoreStep) {
        $scope.disableMore = 1;
      };

      $scope.countFormsOnHold = $scope.newforms['COUNT_ON_HOLD']; 
      $scope.forms = $scope.forms.concat($scope.newforms['DATA']);
    });   
  }

  $scope.viewDetails = function(userId){
    $state.go('adminDetailsRecovery',{recordId:userId}); 
  }

/*************************
*
*DISPLAY PDF (open a new window)
*
***************************/

  $scope.displayPDF = function(formId){

    var str = '';
    str = formId;
    str = str.replace('"','');
    $scope.formId = str.replace('"',''); 
    $window.open('REST/recovery/pdf/read/'+$scope.formId);

  }

  $scope.validate = function(form){
    recoveryService.validate(form).then(function(response){
      $scope.response = response.data;
      recoveryService.generatePDF(form.ID);
      $scope.forms = [];
      $rootScope.updateList();
    });

  };

  //we make sure we have a type-> number in the field
   $scope.$watch('agentSearch', function() {
      if ($scope.agentSearch == 1) {
        $scope.listUsers(undefined);
      };
   });

  $scope.listUsers = function(search){

    adminService.listUsers(search).then(function(response){
      $scope.users = response.data;
    });

  };

  $rootScope.updateList(true);

}]);

angular.module('simplify').controller('ModalInstanceCtrl', ['$scope','$rootScope','$modalInstance','items','recoveryService', function ($scope,$rootScope, $modalInstance, items,recoveryService) {

  $scope.form = items;
  $scope.refuse = function(form){

    console.log('form : ' + $scope.recoveryForm);
    $scope.loading = 1; 
    recoveryService.refuse(form).then(function(response){
      $scope.response = response.data;
      $scope.loading = 0;
      $scope.loadMoreStep = 25;
      $rootScope.reset = 1;
      $rootScope.updateList(); 
      $modalInstance.close();
    });

  };

  $scope.cancel = function () {
    $modalInstance.dismiss('cancel');
  };
}]);