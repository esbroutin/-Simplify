'use strict';

simplify.factory('adminService', function($http) {
	
  return {
    listAlert: function() {
         return $http.get("REST/admin/alert/list");
    }
  }
});