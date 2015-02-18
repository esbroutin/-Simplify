'use strict';

simplify.factory('contextService', function($http) {
   return {
        getContext: function() {
             return $http.get('REST/context');
        },
   }
});