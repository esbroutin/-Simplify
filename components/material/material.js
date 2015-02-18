//Material Basic Controller
console.log('Material Basic Controller'); 
angular
  .module('simplify')
  .controller('MaterialCtrl', ['$scope','$state','$http', function($scope, $state, $http){
  	console.log('state :' + JSON.stringify($state.current.name));

    $scope.hello = 'Aloha !';
 
}]);