'use strict';

simplify.factory('certificateService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/certificate/list/"+search);
    },
    add: function(certificate) {
         return $http.post("REST/certificate/add",certificate);
    },
    delete: function(certificateId) {
         return $http.delete("REST/certificate/delete/"+certificateId);
  	},
    get: function(certificateId) {
         return $http.get("REST/certificate/get/"+certificateId);
    },
    update: function(certificate) {
         return $http.post("REST/certificate/update",certificate);
    }
  }
});