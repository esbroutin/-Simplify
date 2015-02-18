<?php
/**
 *  REST service to handle maintenance
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class MaintenanceCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the maintenance list
  * @return array(array())
  **/

  function listMaintenance(){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\MaintenanceDAO($this->app);  
    return $dao->listMaintenance();   

  }
    
}