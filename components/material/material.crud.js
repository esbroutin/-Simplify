//Maintenance CRUD Controller
angular
  .module('simplify')
  .controller('MaterialCrudCtrl', ['$scope','$state','$http','materialService', function($scope, $state, $http, materialService){
  	console.log('state :' + JSON.stringify($state.current.name));

    $scope.hello = 'Aloha !';

}]);