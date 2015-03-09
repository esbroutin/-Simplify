'use strict';

simplify.factory('aclService', function($http) {
   return {
        getACL: function(moduleName) {
             return $http.get('rest/acl/module/'+moduleName,{ cache: true });
        },
   }
});