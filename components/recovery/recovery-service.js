'use strict';

simplify.factory('recoveryService', function($http) {
	
  return {
    list: function(search) {
         return $http.get("REST/recovery/list/"+search);
    },
    listForm: function() {
         return $http.get("REST/recovery/form/list");
    },
    listAdmin: function(userId) {
         return $http.get("REST/recovery/admin/list/"+userId);
    },
    countForms: function() {
         return $http.get("REST/recovery/admin/count");
    },
    listFormAdmin: function() {
         return $http.get("REST/recovery/admin/form/list");
    },
    add: function(recovery) {
         return $http.post("REST/recovery/add",recovery);
    },
    generatePDF: function(formId) {
         return $http.get("REST/recovery/pdf/"+formId);
    },
    readPDF: function(formId) {
         return $http.get("REST/recovery/pdf/read/"+formId);
    },
    addForm: function(form) {
         return $http.post("REST/recovery/addForm",form);
    },
    validate: function(form) {
         return $http.post("REST/recovery/admin/validate",form);
    },
    refuse: function(form) {
         return $http.post("REST/recovery/admin/refuse",form);
    },
    delete: function(recoveryId) {
         return $http.delete("REST/recovery/delete/"+recoveryId);
  	},
    get: function(recoveryId) {
         return $http.get("REST/recovery/get/"+recoveryId);
    },
    update: function(recovery) {
         return $http.post("REST/recovery/update",recovery);
    }
  }
});