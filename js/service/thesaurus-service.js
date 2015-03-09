'use strict';

simplify.factory('thesaurusService', function($http,$q) {
   var tblDecode = {};
   return {
        get: function(codename) {
          return $http.get("REST/thesaurus/"+codename,{cache:true});
        },
        getDecode: function(codename,codevalue){
        	var deferred = $q.defer();
        	if(tblDecode[codename]==undefined || tblDecode[codename][codevalue]==undefined){
        		//get codename (<=> CAT_CODE) thesaurus entries
        		this.get(codename).then(function(response){
	        		var tblThesaurus = response.data;
	        		for(var i=0;i<tblThesaurus.length;i++){
	        			//CODE found in thesaurus
	        			if(tblThesaurus[i].CODE==codevalue){ 
	        				//Store CODE => DECODE association
	        				var thesaurusEntry = {};
	        				thesaurusEntry[codevalue] = tblThesaurus[i].DECODE;
	        				tblDecode[codename] = thesaurusEntry;
	        				break;
        				}
	        		}
              //If codename (cat) or coded value are not found, return coded value
               if(tblDecode[codename]==undefined || tblDecode[codename][codevalue]==undefined){
                deferred.resolve(codevalue);               
              }else{
	        		 deferred.resolve(tblDecode[codename][codevalue]);        			
              }
        		});

        	}else{
        		deferred.resolve(tblDecode[codename][codevalue]);
        	}
        	return deferred.promise;
        },
        getDistinctCatList:function(){
          return $http.get("REST/thesaurus/list/cat",{cache:true});
        }
   }
});