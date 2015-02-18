'use strict';
/**
* Controleur de base pour les Controleurs CRUD (Create Read Update Delete)
* @param service dataService : Service implementant les fonctions add, view, list, delete, update 
**/
simplify.controller('BaseCRUDCtrl', ['$scope','$modal','$state','$stateParams','$q','dataService',function($scope,$modal,$state,$stateParams,$q,dataService) {

  $scope.currentRecord = {};
  
  $scope.qView = $q.defer(); //promise qui peut être utilisée pour savoir quand les données sont dispos dans les controleurs enfants

  $scope.getOptionsById = function(tblOptions,searchedOptionsId){
    for(var i=0;i<tblOptions.length;i++){
      if(tblOptions[i].ID==searchedOptionsId){
        return tblOptions[i];
      }     
    }
  }

  $scope.alerts = [];
  $scope.closeAlert = function(index) {
    $scope.alerts.splice(index, 1);
  };

  //Handle of curent editing mode (view/edit)
  $scope.setEditMode = function(mode){
    $scope.editMode = mode;
  }

  $scope.loadData = function(recordId){
    dataService.get($stateParams.recordId).then(
      function(response){
        
        angular.copy(response.data, $scope.currentRecord);
        
        if($scope.currentRecord!=undefined){
          $scope.initExtraData($scope.currentRecord.EXTRA_DATA);
        }
        
        //Keep a copy of the original data - in case of editing is canceled
        $scope.oldRecord = angular.copy($scope.currentRecord);
        
        //We start in edit mode
        $scope.setEditMode("view");
        $scope.qView.resolve(); //Notice the availability of data
      });    
  }

  /*
  * Initialise Extra Data scope variables currentRecord.TBL_EXTRA_DATA and currentRecord.RAW_EXTRA_DATA
  * @param json extraData
  */
  $scope.initExtraData = function(extraData){
      //ExtraData are in json format
      $scope.currentRecord.TBL_EXTRA_DATA = angular.fromJson(extraData);

      //Use RAW_EXTRA_DATA for raw editing
      $scope.currentRecord.RAW_EXTRA_DATA = JSON.stringify($scope.currentRecord.TBL_EXTRA_DATA, undefined, 2);    
  }
  
  //Data loading if a recordId is specified in the URL (as params)
  if($stateParams.recordId != undefined){
    $scope.loadData($stateParams.recordId);
  }else{
    //No Data => add mode
    $scope.setEditMode("add");
    $scope.qView.resolve();
  }

  $scope.checkData = function(newScenario){
    $scope.alerts = []; //Suppression des messages d'erreurs précédents
    return true; //pas de check pour le moment    
  }

  /**
  * Add a new element
  * @param {object} newElement
  * @return promise {check:true/false,save:true/false} 
  **/  
  $scope.add = function (newElement){    
    var qAdd = $q.defer();
    var response = {"check":false,"add":false};
    
    //Reverse operation : json->string for ExtraData
    newElement.EXTRA_DATA = angular.toJson(newElement.TBL_EXTRA_DATA);       
    
    if(newElement.EXTRA_DATA==undefined){
      newElement.EXTRA_DATA = "{}";
    }
    
    if($scope.checkData(newElement)){
      response.check = true;
      dataService.add(newElement).then(
          function(){
            //Enregistrement effectué avec succès - on affiche un message d'information
            response.add = true;
            qAdd.resolve(response);
            $scope.infoMsg = "Enregistrement réalisé avec succès";
            var dialog = $modal.open({
              scope:$scope,
              templateUrl: 'tpls/InfoDialog.html',
            });
            dialog.result.then(function(result){
              if(result=='Y'){
				        $state.go("^.list");  
              }
            }); 
        },function(response){
          //Erreurs rencontrées
          response.add = false;
          qAdd.reject(response);
          if(response.status=="500"){ //Erreur Serveur - on n'affole pas l'utilisateur avec un message technique
            if(response.data.result!=undefined){
              $scope.alerts.push({type:'danger',msg:response.data.message});        
            }else{
              $scope.alerts.push({type:'danger',msg:"Une erreur interne est survenue; L'administrateur a été notifié. Référence : " + response.data.errorDt});        
            }
          }
       });
    }else{
      qAdd.reject(response);
    }
    return qAdd.promise;
  }

 /**
  * Update the current record
  * @param {object} element
  * @return promise {check:true/false,update:true/false} 
  **/
  $scope.save = function (currentRecord){   
     var qSave = $q.defer();
     var response = {"check":false,"update":false};
     if($scope.checkData(currentRecord)){
      response.check = true;

      //Reverse operation : json->string for ExtraData
      currentRecord.EXTRA_DATA = angular.toJson(currentRecord.TBL_EXTRA_DATA);       
       
      //Remove unneeded properties
       delete currentRecord.TBL_EXTRA_DATA;
       delete currentRecord.RAW_EXTRA_DATA;
       delete currentRecord.AT;
       
      //Envoi de la requete de sauvegarde  
      dataService.update(currentRecord).then(
        function(){ //Succés
          response.update = true;
          //Enregistrement effectué avec succès
          //After a successfulk saving, we fetch data from server
          $scope.loadData($stateParams.recordId);
          $scope.editMode='view';
          qSave.resolve(response);
        },
        function(data){ //Erreur
          response.update = false;
          if(data.status=="500"){ //Erreur Serveur - on n'affole pas l'utilisateur avec un message technique
            $scope.alerts.push({type:'danger',msg:"Une erreur interne est survenue; L'administrateur a été notifié. Référence : " + data.data.errorDt});        
          }else{
            $scope.alerts.push({type:'danger',msg:data.message});        
          }       
          qSave.reject(response);
        });
    }else{
      qSave.reject(response);
    }
    return qSave.promise;
  }

  /**
  * Cancel edition - show a Y/N modal dialog
  **/
  $scope.cancel = function(currentForm){
    if(!currentForm.$pristine){
      //TODO : à encapsuler dans un service
      var dialog = $modal.open({
        templateUrl: 'tpls/ConfirmDialog.html',
      });
      dialog.result.then(function(result){
        if(result=='Y'){
          //Cancel modifications
          $scope.currentRecord = angular.copy($scope.oldRecord);
          $scope.setEditMode("view");    
        }
      });
    }else{
      //No modification - go back to read only
      $scope.setEditMode("view");    
    }  
  }

 /**
  * Remove the current record
  * @return promise {remove:true/false} 
  **/
  $scope.markAsRemoved = function(){
    var qRemove = $q.defer();
    var response = {"remove":false};
    dataService.markAsRemoved($scope.currentRecord.ID).then(
          function(){
            //Delete done without error
            response.remove = true;
            qRemove.resolve(response);
            $scope.infoMsg = "Suppression de " + $scope.currentRecord.ID + " effectuée";
            var dialog = $modal.open({
              scope:$scope,
              templateUrl: 'tpls/InfoDialog.html',
            });
            dialog.result.then(function(result){
              if(result=='Y'){
                $state.go("^.list");  
              }
            });
        },function(response){
          qRemove.reject(response);
          if(response.status=="500"){ //Server Side Error
            if(response.data.result!=undefined){
              $scope.alerts.push({type:'danger',msg:response.data.message});        
            }else{
              $scope.alerts.push({type:'danger',msg:"Une erreur interne est survenue; L'administrateur a été notifié. Référence : " + response.data.errorDt});        
            }
          }
      });
    return qRemove.promise;
  }

  /**
  * Delete the current record
  * @return promise {delete:true/false} 
  **/
  $scope.delete = function(){
    var qDelete = $q.defer();
    var response = {"delete":false};
    dataService.delete($scope.currentRecord.ID).then(
          function(){
            //Delete done without error
            response.delete = true;
            qDelete.resolve(response);
            $scope.infoMsg = "Suppression définitive de " + $scope.currentRecord.ID + " effectuée";
            var dialog = $modal.open({
              scope:$scope,
              templateUrl: 'tpls/InfoDialog.html',
            });
            dialog.result.then(function(result){
              if(result=='Y'){
                $state.go("^.list");  
              }
            });
        },function(response){
          qDelete.reject(response);
          if(response.status=="500"){ //Server Side Error
            if(response.data.result!=undefined){
              $scope.alerts.push({type:'danger',msg:response.data.message});        
            }else{
              $scope.alerts.push({type:'danger',msg:"Une erreur interne est survenue; L'administrateur a été notifié. Référence : " + response.data.errorDt});        
            }
          }
      });
    return qDelete.promise;
  }

  /***AUDIT TRAIL****/
  //we decode the AT field stored as json
  $scope.qView.promise.then(function(){
    if($scope.currentRecord!=undefined && $scope.currentRecord.AT!=undefined){
      $scope.tblAT = angular.fromJson($scope.currentRecord.AT);
    }else{
      $scope.tblAT = [];
    }
  });

  /**
  *@return a promise resolved with a boolean : true if there is AT for corresponding input
  **/
  $scope.hasAT = function(model){
    return $scope.qView.promise.then(function(){
      var bFound=false;
   
      if(model!=undefined){
        //model contains 'curentRecord' - we drop it
        var fieldName = model.split(".")[1];

        if($scope.tblAT.length >0){ 
          for(var i=0;i<$scope.tblAT.length;i++){
            if(($scope.tblAT[i][fieldName]!=undefined)){        
              bFound = true;
              break;
            }
          }
        }
      }
      return bFound;
    });
  }

  /**
  * Get the full AT for an input linked to model
  *@return string html AT table
  **/
  $scope.getAT = function(model){
    
    //model contains 'curentRecord' - we drop it
    var fieldName = model.split(".")[1];

    //On boucle sur l'audit trail
    var html = "<table class='table table-striped table-at'><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Valeur</th></tr>";
    for(var i=0;i<$scope.tblAT.length;i++){
      if(($scope.tblAT[i][fieldName]!=undefined)){        
        var newValue;
        //Un seul niveau - par ex curentRecord.LABEL
        newValue = $scope.tblAT[i][fieldName].newValue;
        html += "<tr><td>"+$scope.tblAT[i]['dt']+"</td>"+
                    "<td>"+$scope.tblAT[i]['who']+"</td>"+
                    "<td>MAJ</td>"+
                    "<td>"+newValue+"</td></tr>";
      }
    }
    
    html += "</table>";
    return { "title": "Historique des modifications",
             "content": html}
  };

}]);
