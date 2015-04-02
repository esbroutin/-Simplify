simplify.controller('HeaderCtrl', ['$scope','$rootScope','$q','$state','contextService','errorService', function($scope,$rootScope,$q,$state,contextService,errorService) {

  $scope.today = new Date();
//we get the username for the current user
  contextService.getContext().then(function(contextInfo){  
    $scope.userName = contextInfo.data.userInfo.user;
    $rootScope.userName = $scope.userName;
  });

  //Popup containing all error, not used yet
  $scope.errors = errorService.getAll();
  
  $scope.clearErrors = function(){
    errorService.clearAll();
  }
  $scope.viewData = function(type, id){
      $state.go(type,{recordId:id}); 
  }
 
}]);