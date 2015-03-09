//brand CRUD Controller
angular
  .module('simplify')
  .controller('BrandCrudCtrl', ['$scope','$state','$stateParams', '$http', 'brandService', function($scope, $state,$stateParams, $http, brandService){

//we initialize our values
	$scope.formDone = 0;
	$scope.addButton = 0;
	$scope.status =$state.current.name;

	//show list function
	$scope.showList = function(){
		$state.transitionTo('listBrand', {reload: true});
	}

	//redirection to view created brand
	$scope.viewbrand = function(){

		$state.transitionTo('viewBrand',{recordId:$scope.newBrandId});
	}
	$scope.cancelAdd = function(){
		$scope.showList ();
	}
	$scope.add = function () {

		//we add the new brand then reload the brands list
		brandService.add($scope.newBrandForm).then(function(response){
			$scope.response = response.data;
			$scope.newBrandId = $scope.response;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newBrandId;
			str = str.replace('"','');
			$scope.newBrandId = str.replace('"',''); 
			$scope.viewbrand();

		});
	};


}]);

