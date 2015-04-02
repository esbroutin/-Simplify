'use strict';

simplify.factory('aclService', function($http) {
   return {
        getACL: function(moduleName) {
             return $http.get('REST/acl/module/'+moduleName);
        },
   }
});