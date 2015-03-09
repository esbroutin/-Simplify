'use strict';
/**
*
**/
simplify.directive('thCode',['thesaurusService', function(thesaurusService){
    return {
      link: function(scope, element, attrs){
        
        scope.$watch(function(){
        	return attrs["thCode"];
        },
        function(){
		    var codevalue = attrs["thCode"];
	        var codename = attrs["thCat"];

	    	thesaurusService.getDecode(codename,codevalue).then(function(decode){
	        	element.html(decode);
	        });
        });
      }
    }
}]);