//License List Controller
angular
  .module('simplify')
  .controller('BrandListCtrl', ['$scope','$state','$http','brandService', function($scope, $state, $http, brandService) {

  //on load, we get licences list
  brandService.list(undefined).then(function(response){
  	$scope.brands = response.data; 
    // console.log('list brands : ' + JSON.stringify($scope.brands));
  });

//go to detailed brand view
$scope.viewBrand = function(brand){

	// console.log('brand : ' + JSON.stringify(brand.ID));
  $state.go('viewBrand',{recordId:brand.ID}); 

}

//search function
$scope.searchBrand = function () {

	if ($scope.brandSearch != 'undefined' && $scope.brandSearch != ''){

		brandService.list($scope.brandSearch).then(function(response){
			$scope.brands = response.data;
			// console.log('brands : ' + JSON.stringify($scope.brands));

		});
	};
}
		

}]);