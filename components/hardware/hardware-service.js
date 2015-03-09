'use strict';

simplify.factory('hardwareService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/hardware/list/"+search);
    },
    add: function(hardware) {
         return $http.post("REST/hardware/add",hardware);
    },
    delete: function(hardwareId) {
         return $http.delete("REST/hardware/delete/"+hardwareId);
  	},
    get: function(hardwareId) {
         return $http.get("REST/hardware/get/"+hardwareId);
    },
    update: function(hardware) {
         return $http.post("REST/hardware/update",hardware);
    }
  }
});