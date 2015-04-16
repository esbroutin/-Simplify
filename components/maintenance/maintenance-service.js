'use strict';

simplify.factory('maintenanceService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/maintenance/list/"+search);
    },
    add: function(maintenance) {
         return $http.post("REST/maintenance/add",maintenance);
    },
    delete: function(maintenanceId) {
         return $http.delete("REST/maintenance/delete/"+maintenanceId);
  	},
    get: function(maintenanceId) {
         return $http.get("REST/maintenance/get/"+maintenanceId);
    },
    update: function(maintenance) {
         return $http.post("REST/maintenance/update",maintenance);
    }
  }
});