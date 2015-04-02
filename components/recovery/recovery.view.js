//Recovery CRUD Controller
angular
  .module('simplify')
  .controller('RecoveryViewCtrl', ['$scope','$state','$stateParams', '$http', 'recoveryService', function($scope, $state,$stateParams, $http, recoveryService){

//we initialize our values
	$scope.deleteButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listRecovery', {reload: true});
	}

	//we get details for selected recovery
	recoveryService.get($stateParams.recordId).then(function(response){
		$scope.recovery = response.data;
		$scope.available_time = parseFloat($scope.recovery.RECOVERY_STOCK) - parseFloat($scope.recovery.RECOVERY_USED);
		// console.log('available_time', $scope.available_time);
	});

	$scope.edit = function(){

		$scope.editButton = 1;

	}

	// $scope.save = function(){

	// 	recoveryService.update($scope.recovery).then(function(response){
	// 		$scope.response = response.data;
	// 		$scope.newRecoveryId = $scope.response;

	// 		//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
	// 		var str = '';
	// 		str = $scope.newRecoveryId;
	// 		str = str.replace('"','');
	// 		$scope.newRecoveryId = str.replace('"',''); 
 //  			$state.go('viewRecovery',{recordId:$scope.newRecoveryId}, {reload: true}); 
	// 	});

	
	// }

	$scope.delete = function(){

		recoveryService.delete($scope.recovery.ID).then(function(response){
			$scope.response = response.data;
			$scope.showList();
		});
	}

	$scope.cancelEdit = function(){
		//must be changed, we shouldn't have to reload the page, just playing with the variable normally work, but not today (20-02-15)
  		$state.go('viewRecovery',{recordId:$scope.recovery.ID}, {reload: true}); 
	}


}]);

