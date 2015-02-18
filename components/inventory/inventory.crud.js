//Maintenance CRUD Controller
angular
  .module('simplify')
  .controller('InventoryCrudCtrl', ['$scope','$state','$http', function($scope, $state, $http){
  	console.log('state :' + JSON.stringify($state.current.name));

    $scope.hello = 'Aloha !';

}]);