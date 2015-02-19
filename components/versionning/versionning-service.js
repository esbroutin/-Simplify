'use strict';

simplify.factory('versionningService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/versionning/list/"+search);
    }
  }
});