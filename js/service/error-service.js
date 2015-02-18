/**
 * Stocke toute les erreurs rencontrées; cf Intercepeteur http dans orloges.js
 **/
//TODO : remove $rootScope injectiion (to be tested)
 simplify.service('errorService',['$rootScope', function($rootScope) {

  var tblErrors = new Array();

  return {
    addError : function(e){
      tblErrors.push(e);
    },

    clearAll : function(){
      tblErrors.length = 0;
    },

    getAll : function(){
      return tblErrors;
    },
  }
}]);