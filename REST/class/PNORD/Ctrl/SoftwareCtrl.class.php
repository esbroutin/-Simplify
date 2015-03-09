<?php
/**
 *  REST service to handle software(s)
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class SoftwareCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the softwares list
  * @return array(array())
  **/

  function listSoftware($search){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->listSoftware($search);   

  }

  /**
  * get the software data
  * @return array(array())
  **/

  function getSoftware($softwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->getSoftware($softwareId);   

  }

  /**
  * delete the software
  * @return array(array())
  **/

  function deleteSoftware($softwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->deleteSoftware($softwareId);   

  }
  /**
  * add a new software (array)
  * @return success
  **/

  function addSoftware($software){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('software : '.$this->dumpRet($software));
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->addSoftware($software);   

  }
  /**
  * update a software (array)
  * @return success
  **/

  function updateSoftware($software){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('software : '.$this->dumpRet($software));
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->updateSoftware($software);   

  }
    
}