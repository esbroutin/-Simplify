simplify.controller('HeaderCtrl', ['$scope','$rootScope','contextService','errorService', function($scope,$rootScope,contextService,errorService) {

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
 
}]);