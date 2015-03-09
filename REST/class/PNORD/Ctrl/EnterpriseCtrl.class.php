<?php
/**
 *  REST service to handle enterprises
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class EnterpriseCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the license list
  * @return array(array())
  **/
  function listEnterprise(){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\EnterpriseDAO($this->app);  
    return $dao->listEnterprise();   
     
  }

  /**
  * add a new license list
  * @return array(array())
  **/
  function addEnterprise($license){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\EnterpriseDAO($this->app);  
    return $dao->addEnterprise($license);   
     
  }
    
}