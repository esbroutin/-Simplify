simplify.controller('HeaderCtrl', ['$scope','$rootScope','contextService','errorService', function($scope,$rootScope,contextService,errorService) {

  contextService.getContext().then(function(contextInfo){  
    $scope.userName = contextInfo.data.userInfo.user;
    $rootScope.userName = $scope.userName;
    console.log('username : ' + $scope.userName);
  });

  //Popup contenant les erreurs - permet de les consulter sur demande
  $scope.errors = errorService.getAll();
  
  $scope.clearErrors = function(){
    errorService.clearAll();
  }
 
}]);