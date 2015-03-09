'use strict';

simplify.factory('providerService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/provider/list/"+search);
    },
    add: function(provider) {
         return $http.post("REST/provider/add",provider);
    },
    delete: function(providerId) {
         return $http.delete("REST/provider/delete/"+providerId);
  	},
    get: function(providerId) {
         return $http.get("REST/provider/get/"+providerId);
    },
    update: function(provider) {
         return $http.post("REST/provider/update",provider);
    }
  }
});