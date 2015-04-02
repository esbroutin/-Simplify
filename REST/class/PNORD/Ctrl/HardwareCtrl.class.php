<?php
/**
 *  REST service to handle maintenance
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class HardwareCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the hardware details
  * @return array(array())
  **/
  function getHardware($hardwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->getHardware($hardwareId);   
     
  }

  /**
  * get a hardware
  * @return array(array())
  **/
  function listHardware($search){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->listHardware($search);   
     
  }

  /**
  * delete the hardware list
  * @return array(array())
  **/
  function deleteHardware($hardwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->deleteHardware($hardwareId);   
     
  }

  /**
  * add a new hardware
  * @return array(array())
  **/
  function addHardware($hardware){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->addHardware($hardware);   
     
  }

  /**
  * update an existing hardware
  * @return array(array())
  **/
  function updateHardware($hardware){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->updateHardware($hardware);   
     
  }
    
}