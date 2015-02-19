<?php

/**
 *  Access class to Postgre Database
 *  @creationdate 2013-09-02
 **/ 

namespace PNORD;

use PDO;
use PDOException;
use PNORD\BaseSimplifyObject;

class DB extends BaseSimplifyObject{
  
  public $pdo;
  
  /**
   * Constructor : by default connect to the reference database
   * @param boolean $bAutoOpen : open db from constructor
   **/     
  function __construct($app,$schema="",$bAutoOpen=true){
    $this->app = $app;
    
    if($bAutoOpen){
      $this->open($schema); 
    }
  } 

  /**
  * Open database and select the schema $schema
  * @param string schema schema to selected
  **/
  function open($schema=""){
  // $this->app->log->info('--- $ gTblConfig :  '.$this->dumpRet($this->app->environment['APP_CONFIG']));
    
    $conStr = "pgsql:host=".$this->app->environment['APP_CONFIG']['DB_HOST'].";port=".$this->app->environment['APP_CONFIG']['DB_PORT'].";dbname=".$this->app->environment['APP_CONFIG']['DB_NAME'];
    
    //Open DB
    try {
      $this->pdo = new PDO($conStr,$this->app->environment['APP_CONFIG']['DB_USER'],$this->app->environment['APP_CONFIG']['DB_PASSWD']);
    }
    catch(Exception $e) {
      if($e->getCode()==7){
        throw new SimplifyException($e->getMessage(), SimplifyException::REFERENCE_DATABASE_NOT_FOUND);        
      }else{
        throw $e;
      }
    }
  
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $this->pdo->setAttribute(PDO::ATTR_CASE,PDO::CASE_UPPER);

    //Select Schema (<=> public) *** A CHANGER ***
    try {
      $this->pdo->exec("SET SCHEMA 'public';");
    }
    catch(Exception $e) {
      if($e->getCode()==7){
        throw new SimplifyException($e->getMessage(), SimplifyException::SCENARIO_NOT_FOUND);        
      }else{
        throw $e;
      }
    }
  } 
  
}