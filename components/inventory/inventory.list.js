//Maintenance List Controller
angular
  .module('simplify')
  .controller('InventoryListCtrl', ['$scope','$state','$http', function($scope, $state, $http) {
  	console.log('state :' + JSON.stringify($state.current.name));

    $scope.list = [{id:"1", date:"30/01/15", label:"Astaro", version:"0.1"},
				    {id:"2", date:"29/01/15", label:"ASA", version:"3.1"},
				    {id:"3", date:"29/01/15", label:"ASA", version:"3.1"},
				    {id:"4", date:"29/01/15", label:"ASA", version:"3.1"},
				    {id:"5", date:"29/01/15", label:"ASA", version:"3.1"},
				    {id:"6", date:"29/01/15", label:"ASA", version:"3.1"},
				    {id:"7", date:"29/01/15", label:"ASA", version:"3.1"},
				    {id:"8", date:"29/01/15", label:"ASA", version:"3.1"},
				    {id:"9", date:"29/01/15", label:"ASA", version:"3.1"},
				    {id:"10", date:"29/01/15", label:"Websense", version:"3.1"},
				    {id:"11", date:"29/01/15", label:"ASA", version:"3.1"},
				    {id:"12", date:"29/01/15", label:"ASA", version:"3.1"}
    				];


}]);