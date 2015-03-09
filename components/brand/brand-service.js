'use strict';

simplify.factory('brandService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/brand/list/"+search);
    },
    add: function(brand) {
         return $http.post("REST/brand/add",brand);
    },
    delete: function(brandId) {
         return $http.delete("REST/brand/delete/"+brandId);
  	},
    get: function(brandId) {
         return $http.get("REST/brand/get/"+brandId);
    },
    update: function(brand) {
         return $http.post("REST/brand/update",brand);
    }
  }
});