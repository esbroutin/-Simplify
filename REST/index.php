<?php
chdir(dirname(__FILE__));
require 'lib/slim/Slim/Slim.php';
require('../config.inc.php');
require('inc/functions.inc.php');

$db_host = $gTblConfig['DB_HOST'];
$db_user = $gTblConfig['DB_USER'];
$db_passwd = $gTblConfig['DB_PASSWD'];
$db_name = $gTblConfig['DB_NAME']; 
$db_port = $gTblConfig['DB_PORT']; 

session_start();
  
\Slim\Slim::registerAutoloader();

//Autoloader pour mes classes \PNORD
function autoloaderPNORD($class) {
    include dirname(__FILE__) . '/class/' . str_replace('\\', '/', $class) . '.class.php';
}
spl_autoload_register('autoloaderPNORD');

$app = new \Slim\Slim(array( 
    'debug' => false,
    'log.enabled' => true,
    'log.level' => \Slim\Log::DEBUG,
));

//Définition de la configuration, pour la rendre accessible dans tous les controleurs à travers $app->environment['APP_CONFIG']
global $gTblConfig; 
$app->environment['APP_CONFIG'] = $gTblConfig;
global $gTblConfigCustom; 
$app->environment['APP_CONFIG_CUSTOM'] = $gTblConfigCustom;

$app->log->setWriter(new \PNORD\LogWriter($app));

$app->log->info("******* REQUEST START - " . $_SERVER['REQUEST_METHOD'] . " ". $_SERVER["REQUEST_URI"] . " - " . (isset($_SESSION['userid'])?$_SESSION['userid']:"No active session") . " - **********");

/********ACL******************************************************************************************/
//Does the current user has access to the 'moduleName' module ?
$app->get('/acl/module/:moduleName', function($moduleName) use($app){
  echo $bAuth = checkAuth($app,$moduleName);
});

// **************************************************
// *  ADMIN (ALERTS)
// **************************************************

$app->get('/admin/alert/list', function () use ($app) {
    if(checkAuth($app)){
      $admin = new \PNORD\Ctrl\AdminCtrl($app);    
      $ret = $admin->listAlert();
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->post('/admin/update', function () use ($app) {
    if(checkAuth($app)){
      $admin = new \PNORD\Ctrl\AdminCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $admin->update($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
}); 
// **************************************************
// *  LICENSE
// **************************************************

$app->get('/license/list/:search', function ($search) use ($app) {
    if(checkAuth($app)){
      $license = new \PNORD\Ctrl\LicenseCtrl($app);    
      $ret = $license->listLicense($search);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->post('/license/add', function() use($app)  {
    if(checkAuth($app)){
      $license = new \PNORD\Ctrl\LicenseCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $license->addLicense($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->post('/license/update', function() use($app)  {
    if(checkAuth($app)){
      $license = new \PNORD\Ctrl\LicenseCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $license->updateLicense($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->get('/license/get/:licenseId', function($licenseId) use($app)  {
    if(checkAuth($app)){
      $license = new \PNORD\Ctrl\LicenseCtrl($app);  
      $ret = $license->getLicense($licenseId);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
});

//GANTT OVERVIEW
$app->get('/license/gantt', function() use($app)  {
    if(checkAuth($app)){
      $license = new \PNORD\Ctrl\LicenseCtrl($app);  
      $ret = $license->getGantt();
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
});

$app->delete('/license/delete/:licenseId', function($licenseId) use($app)  {
    if(checkAuth($app)){
      $license = new \PNORD\Ctrl\LicenseCtrl($app);  
      $ret = $license->deleteLicense($licenseId);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    } 
});

// **************************************************
// *  HARDWARE
// **************************************************

$app->get('/hardware/list/:search', function ($search) use ($app) {
    if(checkAuth($app)){
      $hardware = new \PNORD\Ctrl\HardwareCtrl($app);    
      $ret = $hardware->listHardware($search);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->post('/hardware/add', function() use($app)  {
    if(checkAuth($app)){
      $hardware = new \PNORD\Ctrl\HardwareCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $hardware->addHardware($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->post('/hardware/update', function() use($app)  {
    if(checkAuth($app)){
      $hardware = new \PNORD\Ctrl\HardwareCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $hardware->updateHardware($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->get('/hardware/get/:hardwareId', function($hardwareId) use($app)  {
    if(checkAuth($app)){
      $hardware = new \PNORD\Ctrl\HardwareCtrl($app);  
      $ret = $hardware->getHardware($hardwareId);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
});

$app->delete('/hardware/delete/:hardwareId', function($hardwareId) use($app)  {
    if(checkAuth($app)){
      $hardware = new \PNORD\Ctrl\HardwareCtrl($app);  
      $ret = $hardware->deleteHardware($hardwareId);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    } 
});

// **************************************************
// *  RECOVERY
// **************************************************

$app->get('/recovery/list/:search', function ($search) use ($app) {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);    
      $ret = $recovery->listRecovery($search);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->get('/recovery/form/list', function () use ($app) {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);    
      $ret = $recovery->listForm();
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->get('/recovery/admin/count', function () use ($app) {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);    
      $ret = $recovery->countForms();
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->get('/recovery/admin/list', function () use ($app) {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);    
      $ret = $recovery->listFormAdmin();
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->post('/recovery/add', function() use($app)  {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $recovery->addRecovery($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->post('/recovery/admin/validate', function() use($app)  {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $recovery->validate($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->post('/recovery/admin/refuse', function() use($app)  {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $recovery->refuse($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->post('/recovery/addForm', function() use($app)  {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $recovery->addForm($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->post('/recovery/update', function() use($app)  {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $recovery->updateRecovery($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->get('/recovery/get/:recoveryId', function($recoveryId) use($app)  {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);  
      $ret = $recovery->getRecovery($recoveryId);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
});

$app->get('/recovery/pdf/:recoveryId', function($recoveryId) use($app)  {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\PDFCtrl($app);  
      $ret = $recovery->generatePDF($recoveryId);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
});

$app->get('/recovery/pdf/read/:formId', function($formId) use($app)  {
      $recovery = new \PNORD\Ctrl\PDFCtrl($app);  
      $ret = $recovery->readPDF($formId);

      $app->log->info('recovery/pdf/read ');

      echo json_encode($ret);  
});

$app->delete('/recovery/delete/:recoveryId', function($recoveryId) use($app)  {
    if(checkAuth($app)){
      $recovery = new \PNORD\Ctrl\RecoveryCtrl($app);  
      $ret = $recovery->deleteRecovery($recoveryId);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    } 
});

// **************************************************
// *	VERSIONNNING (SOFTWARE)
// **************************************************

$app->get('/software/list/:search', function ($search) use ($app) {
    if(checkAuth($app)){
      $software = new \PNORD\Ctrl\SoftwareCtrl($app);    
      $ret = $software->listSoftware($search);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->post('/software/add', function() use($app)  {
    if(checkAuth($app)){
      $software = new \PNORD\Ctrl\SoftwareCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $software->addSoftware($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->post('/software/update', function() use($app)  {
    if(checkAuth($app)){
      $software = new \PNORD\Ctrl\SoftwareCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $software->updateSoftware($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->get('/software/get/:softwareId', function($softwareId) use($app)  {
    if(checkAuth($app)){
      $software = new \PNORD\Ctrl\SoftwareCtrl($app);  
      $ret = $software->getSoftware($softwareId);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
});

$app->delete('/software/delete/:softwareId', function($softwareId) use($app)  {
    if(checkAuth($app)){
      $software = new \PNORD\Ctrl\SoftwareCtrl($app);  
      $ret = $software->deleteSoftware($softwareId);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    } 
});

// **************************************************
// *  MAINTENANCE
// **************************************************

$app->get('/maintenance/list', function () use ($app) {
    if(checkAuth($app)){
      $maintenance = new \PNORD\Ctrl\MaintenanceCtrl($app);    
      $ret = $maintenance->listMaintenance();
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
}); 

// **************************************************
// *  PROVIDERS
// **************************************************

$app->get('/provider/list/:search', function ($search) use ($app) {
    if(checkAuth($app)){
      $provider = new \PNORD\Ctrl\ProviderCtrl($app);    
      $ret = $provider->listProvider($search);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
}); 

$app->get('/provider/get/:providerId', function($providerId) use($app)  {
    if(checkAuth($app)){
      $provider = new \PNORD\Ctrl\ProviderCtrl($app);  
      $ret = $provider->getProvider($providerId);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
});

$app->post('/provider/update', function() use($app)  {
    if(checkAuth($app)){
      $provider = new \PNORD\Ctrl\ProviderCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $provider->updateProvider($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->post('/provider/add', function () use ($app) {
    if(checkAuth($app)){
      $provider = new \PNORD\Ctrl\ProviderCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $provider->addProvider($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->delete('/provider/delete/:providerId', function($providerId) use($app)  {
    if(checkAuth($app)){
      $provider = new \PNORD\Ctrl\ProviderCtrl($app);  
      $ret = $provider->deleteProvider($providerId);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    } 
});

// **************************************************
// *  BRAND
// **************************************************

$app->get('/brand/list/:search', function ($search) use ($app) {
    if(checkAuth($app)){
      $brand = new \PNORD\Ctrl\BrandCtrl($app);    
      $ret = $brand->listBrand($search);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
}); 

$app->get('/brand/get/:providerId', function($providerId) use($app)  {
    if(checkAuth($app)){
      $brand = new \PNORD\Ctrl\BrandCtrl($app);  
      $ret = $brand->getBrand($providerId);
      echo json_encode($ret);  
    }else{
      $app->response->setStatus(403); 
    } 
});

$app->post('/brand/update', function() use($app)  {
    if(checkAuth($app)){
      $brand = new \PNORD\Ctrl\BrandCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $brand->updateBrand($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
});

$app->post('/brand/add', function () use ($app) {
    if(checkAuth($app)){
      $brand = new \PNORD\Ctrl\BrandCtrl($app);  
      $data = json_decode($app->request->getBody());  
      $ret = $brand->addBrand($data);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    }
}); 

$app->delete('/brand/delete/:brandId', function($brandId) use($app)  {
    if(checkAuth($app)){
      $brand = new \PNORD\Ctrl\BrandCtrl($app);  
      $ret = $brand->deleteBrand($brandId);
      echo json_encode($ret);   
    }else{
      $app->response->setStatus(403); 
    } 
});

/******** THESAURUS ************************************************************************/

//Return thesaurus entries
$app->get('/thesaurus/:codename', function($codename) use($app)  {
    if(checkAuth($app)){
      $daoThesaurus = new \PNORD\Model\ThesaurusDAO($app);
      $tblEntries = $daoThesaurus->getList($codename);
    echo json_encode($tblEntries);
    }else{
      $app->response->setStatus(403);    
    }
});

//Return distinct thesaurus entries - used to know if a list of choice is available for a given field (mainly for extra_data)
$app->get('/thesaurus/list/cat', function() use($app)  {
    if(checkAuth($app)){
      $daoThesaurus = new \PNORD\Model\ThesaurusDAO($app);
      $tblEntries = $daoThesaurus->getDistinctCatList();
      echo json_encode($tblEntries);
    }else{
      $app->response->setStatus(403);    
    }
});

/******** CONTEXT ************************************************************************/

/**
* @return connected user, current schema
**/
$app->get('/context', function () use($app)  {
    $userInfo = new \PNORD\Ctrl\UserInfoCtrl();
    $user = $userInfo->getUserInfo(); 
    if ($user == false) {
      $app->response->setStatus(403); 
    }else{
    echo json_encode(array('userInfo'=>$user));
    }
});

$app->run(); 