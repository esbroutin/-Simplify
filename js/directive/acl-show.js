'use strict';

simplify.directive('aclShow', function(aclService){
    return {
      link: function(scope, element, attrs){
        var moduleName = attrs.aclShow;
        
        aclService.getACL(moduleName).then(function(response){
          if(response.data!=1){
            element.addClass('hide-me');
          }          
        });
      }
    }
  }
);