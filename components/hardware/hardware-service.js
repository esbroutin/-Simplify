'use strict';

simplify.factory('hardwareService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/hardware/list/"+search);
    },
    listTypes: function() {
         return $http.get("REST/hardware/type/list");
    },
    add: function(hardware) {
         return $http.post("REST/hardware/add",hardware);
    },
    addType: function(type) {
         return $http.post("REST/hardware/type/add",type);
    },
    delete: function(hardwareId) {
         return $http.delete("REST/hardware/delete/"+hardwareId);
  	},
    get: function(hardwareId) {
         return $http.get("REST/hardware/get/"+hardwareId);
    },
    generatePDF: function(hardwareId) {
         return $http.get("REST/hardware/pdf/"+hardwareId);
    },
    update: function(hardware) {
         return $http.post("REST/hardware/update",hardware);
    }
  }
});