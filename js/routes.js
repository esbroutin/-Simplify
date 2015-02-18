//Configuration des routes
simplify.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider){

  //Route par défaut
  $urlRouterProvider.otherwise("/home");

  $stateProvider
// FORBIDDEN ACCESS ------------
    .state('forbiddenAccess',{
        url: "/forbiddenAccess?goto",
        templateUrl: 'templates/forbiddenAccess.html',
    })
//-HOME-------------------
      .state('home', {
          url: "/home",
          templateUrl: "templates/welcome.html" 
      })
//-SIMPLIFY-------------------
    .state('simplify', {
       url: "/simplify",
       template: "<ui-view />" 
    })
  //Maintenance routing
    .state('maintenance', {
      url: '/maintenance', 
      templateUrl: 'components/maintenance/maintenance.html',
    }) 
    .state('listMaintenance', {
      url: '/maintenance/list',
      templateUrl: 'components/maintenance/maintenance.list.html',
    }) 
    .state('addMaintenance', {
      url: '/maintenance/add', 
      templateUrl: 'components/maintenance/maintenance.crud.html' ,
    })
  //License routing
    .state('license', {
      url: '/license', 
      templateUrl: 'components/license/license.html',
    }) 
    .state('listLicense', {
      url: '/license/list',
      templateUrl: 'components/license/license.list.html',
    }) 
    .state('addLicense', {
      url: '/license/add', 
      templateUrl: 'components/license/license.crud.html' ,
    }) 
    .state('viewLicense', {
      url: '/license/view/:recordId', 
      templateUrl: 'components/license/license.crud.html' ,
    })
    .state('overviewLicense', {
      url: '/license/overview', 
      templateUrl: 'components/license/license.overview.html' ,
    })
  //Inventory routing
    .state('inventory', {
      url: '/inventory',
      templateUrl: 'components/inventory/inventory.html',
    }) 
    .state('listInventory', {
      url: '/inventory/list',
      templateUrl: 'components/inventory/inventory.list.html',
    })
    .state('addInventory', {
      url: '/inventory/add', 
      templateUrl: 'components/inventory/inventory.crud.html' ,
    })
  //Material routing
    .state('material', {
      url: '/material',
      templateUrl: 'components/material/material.html',
    }) 
    .state('listMaterial', {
      url: '/material/list',
      templateUrl: 'components/material/material.list.html',
    })
    .state('addMaterial', {
      url: '/material/add',
      templateUrl: 'components/material/material.crud.html' ,
    })
  }]);