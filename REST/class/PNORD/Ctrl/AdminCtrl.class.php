<?php
/**
 *  REST service to handle Admin(s)
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class AdminCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * list all alerts
  * @return array(array())
  **/

  function listAlert(){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\AdminDAO($this->app);  
    return $dao->listAlert();   

  }
}