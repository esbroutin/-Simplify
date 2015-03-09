'use strict';

simplify.factory('licenseService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/license/list/"+search);
    },
    add: function(license) {
         return $http.post("REST/license/add",license);
    },
    delete: function(licenseId) {
         return $http.delete("REST/license/delete/"+licenseId);
  	},
    get: function(licenseId) {
         return $http.get("REST/license/get/"+licenseId);
    },
    update: function(license) {
         return $http.post("REST/license/update",license);
    },
    //Gantt Overview
    getGantt: function() {
         return $http.get("REST/license/gantt");
    }
  }
});