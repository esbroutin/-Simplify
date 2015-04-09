'use strict';

simplify.factory('adminService', function($http) {
	
  return {
    listAlert: function() {
         return $http.get("REST/admin/alert/list");
    },

    listUsers: function(search) {
         return $http.get("REST/admin/user/list/"+search);
    },
    update: function(admin) {
         return $http.post("REST/admin/update",admin);
    }
  }
});