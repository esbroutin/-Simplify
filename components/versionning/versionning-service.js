'use strict';

simplify.factory('versionningService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/versionning/list/"+search);
    },
    add: function(data) {
         return $http.post("REST/versionning/add",data);
    },
    //non crud services
    listEnterprise: function() {
         return $http.get("REST/enterprise/list");
    },

  }
});