//license Basic Controller
angular
  .module('simplify')
  .controller('AdminCtrl', ['$scope','$state','$http','adminService', function($scope, $state, $http, adminService){
 
	$scope.update = function () {

		//we update the last password with the new one
		adminService.update($scope.admin).then(function(response){
			$scope.response = response.data;
			$scope.saved = 1;

		});
	};

	$scope.ok = function () {

			$scope.saved = 0;
			$scope.editPassword = 0;

	};

}]);