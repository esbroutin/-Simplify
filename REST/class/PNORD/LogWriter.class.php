<?php

/**
 *  Classe de gestion des logs
 *  @creationdate 2013-08-20  
 **/ 

namespace PNORD;

class LogWriter extends BaseSimplifyObject{
  
  protected $requestId;

  function __construct($app){
    $this->app = $app;
    $this->requestId = '*!S*';
  }
  
  public function write($message,$level){
    
    $logFile = $this->app->environment['APP_CONFIG']['LOG']['FILE'];
    $emailError = $this->app->environment['APP_CONFIG']['LOG']['ERROR_EMAIL']; 
        
    $timeOffset = microtime(true) - $_SERVER['REQUEST_TIME']; 
    $dt = date('c') . " " . substr($timeOffset,0,7) . " " . $this->requestId;
    error_log("$dt " . $message . "\n",3,$logFile);
    
    if($level <= \Slim\Log::ERROR){
      $message .= "\nrequest = " . $this->dumpRet($_REQUEST);
      error_log("$dt " . $message . "\n",3,$logFile.".error");
      mail($emailError,"Simplify ERROR : @$dt",$message);
    }    
  }  
}

