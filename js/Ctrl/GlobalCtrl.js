//global Basic Controller
angular
  .module('simplify')
  .controller('globalCtrl', ['$scope','$rootScope','$state','$http','$q','adminService', function($scope,$rootScope, $state, $http,$q,adminService){

  $scope.qView = $q.defer(); //promise qui peut être utilisée pour savoir quand les données sont dispos dans les controleurs enfants

	//we get all alerts for licenses & hardware (we created a function to avoid full reload of the page when editing hardwares & licenses)
	$rootScope.listAlerts =function () {

		adminService.listAlert().then(function(response){
			$rootScope.alerts = response.data;
			if ($rootScope.alerts.COUNT_ALERTS > 0) {
				$rootScope.noAlerts = 0;
			}else{
				$rootScope.noAlerts = 1;
			}
			// console.log('noAlerts : ' , $rootScope.noAlerts);
	    $scope.qView.resolve(); //Notice the availability of data
		});
	}


	$rootScope.listAlerts();

}]); 