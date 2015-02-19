<?php

namespace PNORD;

class BaseSimplifyObject{

  public $app;
  public static $db;
    
  /**
  * Constructor
  * @param optional boolean $bNeedDBAccess - set to true if child class need access to database. default value : false
  **/
  function __construct($app,$bNeedDBAccess=false){
    $this->app = $app;

    //PDO Instance - initialised with current scenario stored in user session
    if($bNeedDBAccess && self::$db==null){
      try{
        self::$db = new DB($app,$this->getCurrentScenario());
     }catch(SimplifyException $e){
        if($e->getCode()==SimplifyException::REFERENCE_DATABASE_NOT_FOUND){
          $app->response->setStatus(500);  
          echo json_encode(array("result"=>false,"errorCode"=>SimplifyException::REFERENCE_DATABASE_NOT_FOUND));
          $app->stop();
        }
      } 
    }
  }

  /**
  * helper function for chlid class to get db instance
  * @return \PNORD\DB
  **/
  function db(){
    if(!isset(self::$db->pdo)){
      throw new SimplifyException("DATABASE NOT OPENED", SimplifyException::DATABASE_NOT_OPENED);
    }
    return self::$db->pdo;
  }

  static function dumpRet($mixed = null) {
    ob_start();
    var_dump($mixed);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }
  
  /**
  * Retourne the current scenario stored in user session 
  * @return string 
  **/
  protected function getCurrentScenario(){

    return 'public';
  }

}