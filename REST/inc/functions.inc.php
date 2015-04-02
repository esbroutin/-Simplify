<?php
/**-------------------
* Global functions file
**--------------------*/

/**
* check user access rights
* @param string $module : module to test user against
* @return boolean
**/
function checkAuth($app,$module=""){
  $app->log->info(__METHOD__ . " ($module)" . (isset($_SESSION['userid'])?$_SESSION['userid']:"user id not set"));
  $bRet = false;
  if(isset($_SESSION['userid']) && isset($app->environment['APP_CONFIG']['auth']['acl'][$_SESSION['userid']])){
    //User has access to the app
    //Now we check if he has access to requested url
    $shortURI = substr($_SERVER["REQUEST_URI"],strlen($app->environment['WEB_DIR']));
    
    if( $module=="" || isset($app->environment['APP_CONFIG']['auth']['acl']['modules'][$module][$_SESSION['userid']]) && 
                             $app->environment['APP_CONFIG']['auth']['acl']['modules'][$module][$_SESSION['userid']]){
      $bRet = true;
    }else{
      $app->log->info($_SESSION['userid'] . " does not have access to module $module - " . $_SERVER["REQUEST_URI"]);
    }  
  }else{
    $app->log->info("Access not granted to the application");     
  }
  // $app->log->info('bRet : ' . $bRet);
  
  // return ('azdazdazdadazdadazdz');
  return $bRet;
}

function isAdmin($app){
  if(isset($app->environment['APP_CONFIG']['auth']['acl']['admin'][$_SESSION['userid']]) && 
		   $app->environment['APP_CONFIG']['auth']['acl']['admin'][$_SESSION['userid']]==true){
    return true;
  }else{
    return false;
  }
}