'use strict';
/**
* Directive to transform an input to a select (ui-select2) filled with thesaurus entries
* Usage : <input type='text' class='form-control' thesaurus-to-select="THESAURUS_CAT_CODE" ng-model="myModel">
**/
simplify.directive('thesaurusToSelect',['$compile','thesaurusService', function($compile,thesaurusService){
    return {
      link: function(scope, element, attrs){
        thesaurusService.getDistinctCatList().then(function(response){
          var bFound = false;
          for(var i=0;i<response.data.length;i++){
            if(response.data[i].CAT==attrs["thesaurusToSelect"]){
              bFound = true;
              break;
            }
          }
          if(bFound){
            //Replace input by a select  
            var html = "<select ui-select2='' ";

            // loop through <input> attributes and apply them on <select>
            var attributes = element.prop("attributes");            
            for(var i=0;i<attributes.length;i++){
              if(attributes[i].name!='type' && attributes[i].name!='thesaurus-to-select'){
                html += attributes[i].name + "=\"" + attributes[i].value + "\" ";
              }
            }
            html += ">";
            
            //Add options
            thesaurusService.get(attrs["thesaurusToSelect"]).then(function(response){
              for(var i=0;i<response.data.length;i++){
                html += "<option value='"+response.data[i].CODE+"'>" + response.data[i].DECODE + "</option>";
              }
            }).then(function(){
              html += "</select>";
              element.replaceWith($compile(html)(scope));              
            });            
          }
        }); 
      }
    }
}]);