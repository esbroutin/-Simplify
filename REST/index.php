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

// **************************************************
// *	LICENSE
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
// *	PROVIDERS
// **************************************************

$app->get('/provider/list', function () use ($app) {
    if(checkAuth($app)){
      $provider = new \PNORD\Ctrl\ProviderCtrl($app);    
      $ret = $provider->listProvider();
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