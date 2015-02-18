<?php

/**
 *  Access class to Postgre Database
 *  @creationdate 2013-09-02
 **/ 

namespace PNORD;

use PDO;
use PDOException;
use PNORD\BaseSimplifyObject;
require('/var/www/webapp-simplify/config.inc.php');
$gTblConfig = array();
$gTblConfig['DB_HOST'] = '10.16.21.57';
$gTblConfig['DB_USER'] = 'postgres';
$gTblConfig['DB_PASSWD'] = 'postgres';
$gTblConfig['DB_NAME'] = "simplify"; 
$gTblConfig['DB_PORT'] = "5433"; 

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
    // $this->app->log->info(__CLASS__ . "::" . __METHOD__ . "($schema)");
    // $this->app->log->info(":***environment***:" . $this->app->environment['APP_CONFIG']);

    //*******  A CHANGER ******
    
    $conStr = "pgsql:host=".'10.16.21.57'.";port=".'5433'.";dbname=".'simplify';
    
    //Open DB
    try {
      $this->pdo = new PDO($conStr,'postgres','postgres');
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

    //Select Schema (<=> schema)
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