'use strict';

simplify.factory('maintenanceService', function($http) {

  return {
    list: function() {
         return $http.get("REST/maintenance/list",{cache:false});

  	}
  }
});