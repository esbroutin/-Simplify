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
  //Admin routing
    .state('admin', {
      url: '/admin', 
      templateUrl: 'components/admin/admin.html',
    }) 
  //Recovery routing
    .state('recovery', {
      url: '/recovery', 
      templateUrl: 'components/recovery/recovery.html',
    }) 
    .state('listRecovery', {
      url: '/recovery/list',
      templateUrl: 'components/recovery/recovery.list.html',
    }) 
    .state('recoveryAdmin', {
      url: '/recovery/admin/list',
      templateUrl: 'components/recovery/recovery.admin.html',
    }) 
    .state('recoveryAdminView', {
      url: '/recovery/admin/view',
      templateUrl: 'components/recovery/recovery.admin.view.html',
    }) 
    .state('addRecovery', {
      url: '/recovery/add', 
      templateUrl: 'components/recovery/recovery.crud.html' ,
    })
    .state('formRecovery', {
      url: '/recovery/form', 
      templateUrl: 'components/recovery/recovery.form.html' ,
    })
    .state('viewRecovery', {
      url: '/recovery/view/:recordId', 
      templateUrl: 'components/recovery/recovery.view.html' ,
    })
    .state('adminDetailsRecovery', {
      url: '/recovery/admin/view/:recordId', 
      templateUrl: 'components/recovery/recovery.admin.details.html' ,
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
      templateUrl: 'components/license/license.view.html' ,
    })
    .state('overviewLicense', {
      url: '/license/overview', 
      templateUrl: 'components/license/license.overview.html' ,
    })
  //Hardware routing
    .state('hardware', {
      url: '/hardware',
      templateUrl: 'components/hardware/hardware.html',
    }) 
    .state('listHardware', {
      url: '/hardware/list',
      templateUrl: 'components/hardware/hardware.list.html',
    })
    .state('viewHardware', {
      url: '/hardware/view/:recordId',
      templateUrl: 'components/hardware/hardware.view.html',
    })
    .state('addHardware', {
      url: '/hardware/add', 
      templateUrl: 'components/hardware/hardware.crud.html' ,
    })
  //Certificate routing
    .state('certificate', {
      url: '/certificate',
      templateUrl: 'components/certificate/certificate.html',
    }) 
    .state('listCertificate', {
      url: '/certificate/list',
      templateUrl: 'components/certificate/certificate.list.html',
    })
    .state('viewCertificate', {
      url: '/certificate/view/:recordId',
      templateUrl: 'components/certificate/certificate.view.html',
    })
    .state('addCertificate', {
      url: '/certificate/add', 
      templateUrl: 'components/certificate/certificate.crud.html' ,
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
  //software routing
    .state('software', {
      url: '/software',
      templateUrl: 'components/software/software.html',
    }) 
    .state('listSoftware', {
      url: '/software/list',
      templateUrl: 'components/software/software.list.html',
    })
    .state('addSoftware', {
      url: '/software/add', 
      templateUrl: 'components/software/software.crud.html' ,
    })
    .state('viewSoftware', {
      url: '/software/view/:recordId', 
      templateUrl: 'components/software/software.view.html' ,
    })
  //Provider routing
    .state('provider', {
      url: '/provider',
      templateUrl: 'components/provider/provider.html',
    }) 
    .state('listProvider', {
      url: '/provider/list',
      templateUrl: 'components/provider/provider.list.html',
    })
    .state('addProvider', {
      url: '/provider/add', 
      templateUrl: 'components/provider/provider.crud.html' ,
    })
    .state('viewProvider', {
      url: '/provider/view/:recordId', 
      templateUrl: 'components/provider/provider.view.html' ,
    })
  //Brand routing
    .state('brand', {
      url: '/brand',
      templateUrl: 'components/brand/brand.html',
    }) 
    .state('listBrand', {
      url: '/brand/list',
      templateUrl: 'components/brand/brand.list.html',
    })
    .state('addBrand', {
      url: '/brand/add', 
      templateUrl: 'components/brand/brand.crud.html' ,
    })
    .state('viewBrand', {
      url: '/brand/view/:recordId', 
      templateUrl: 'components/brand/brand.view.html' ,
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