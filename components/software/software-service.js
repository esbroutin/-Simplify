'use strict';

simplify.factory('softwareService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/software/list/"+search);
    },
    add: function(data) {
         return $http.post("REST/software/add",data);
    },
    delete: function(softwareId) {
         return $http.delete("REST/software/delete/"+softwareId);
  	},
    get: function(softwareId) {
         return $http.get("REST/software/get/"+softwareId);
    },
    update: function(software) {
         return $http.post("REST/software/update",software);
    }
  }
});