'use strict';

simplify.factory('materialService', function($http) {
  return {
    listMaterial: function() {
         return $http.get("REST/material/list",{cache:true});

  	}
  }
});