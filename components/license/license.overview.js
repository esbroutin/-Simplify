//Maintenance CRUD Controller
angular
  .module('simplify')
  .controller('LicenseOverviewCtrl', ['$scope','$state','$http','licenseService', function($scope, $state, $http, licenseService){
	$scope.uiConfig = {
	  calendar:{
	    height: 450,
	    header:{
	      left: 'month basicWeek basicDay agendaWeek agendaDay',
	      center: 'title',
	      right: 'today prev,next'
	    },
	    dayClick: $scope.alertEventOnClick,
	    eventDrop: $scope.alertOnDrop,
	    eventResize: $scope.alertOnResize
	  }
	};

		$scope.eventSources =  [{title:"row1",start:'2015-04-09'},{title:"row2",start:"2015-01-01",end:"2015-05-14"},{title:"row3",start:"2015-03-01",end:"2015-04-16"}];
	// $scope.loading = 1;
	// licenseService.getGantt().then(function(response){
	// 	$scope.eventSources =  response.data;
	// 	$scope.eventSources =  angular.fromJson($scope.eventSources);
	// 	$scope.eventSources =  JSON.parse($scope.eventSources);
	// 	console.log('$scope.eventSources : ', JSON.stringify($scope.eventSources));  	
	// 	$scope.eventSources =  [{title:"row1",start:'2015-04-09'},{title:"row2",start:"2015-01-01",end:"2015-05-14"},{title:"row3",start:"2015-03-01",end:"2015-04-16"}];
	// 	$scope.loading = 0;
	// });

}]);