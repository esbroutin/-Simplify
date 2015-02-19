<?php
/**
 *  REST service to handle maintenance
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class LicenseCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the license details
  * @return array(array())
  **/
  function getLicense($licenseId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\LicenseDAO($this->app);  
    return $dao->getLicense($licenseId);   
     
  }

  /**
  * get the gantt overview
  * @return array(array())
  **/
  function getGantt(){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\LicenseDAO($this->app);  
    return $dao->getGantt();   
     
  }

  /**
  * get the license list
  * @return array(array())
  **/
  function listLicense($search){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\LicenseDAO($this->app);  
    return $dao->listLicense($search);   
     
  }

  /**
  * delete the license list
  * @return array(array())
  **/
  function deleteLicense($licenseId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\LicenseDAO($this->app);  
    return $dao->deleteLicense($licenseId);   
     
  }

  /**
  * add a new license list
  * @return array(array())
  **/
  function addLicense($license){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\LicenseDAO($this->app);  
    return $dao->addLicense($license);   
     
  }
    
}